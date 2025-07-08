<?php

namespace App\Livewire\Forecasting;

use Livewire\Component;
use App\Models\Opportunity;
use App\Models\Airline;
use App\Models\User;

class Dashboard extends Component
{
    public $selectedYear;
    public $selectedAirline = '';
    public $selectedRegion = '';
    public $selectedUser = '';
    public $yearRange = [];

    protected $queryString = [
        'selectedYear' => ['except' => ''],
        'selectedAirline' => ['except' => ''],
        'selectedRegion' => ['except' => ''],
        'selectedUser' => ['except' => '']
    ];

    public function mount()
    {
        $this->selectedYear = (int) date('Y');
        $this->yearRange = range(date('Y'), date('Y') + 7);
    }

    public function render()
    {
        $forecastData = $this->getForecastData();
        $airlines = Airline::orderBy('name')->get();
        $regions = Airline::distinct()->pluck('region')->filter()->sort();
        $salesUsers = User::whereIn('role', ['sales', 'managers'])
            ->orderBy('name')
            ->get();

        return view('livewire.forecasting.dashboard', [
            'forecastData' => $forecastData,
            'airlines' => $airlines,
            'regions' => $regions,
            'salesUsers' => $salesUsers,
            'yearRange' => $this->yearRange,
        ])->layout('layouts.app');
    }

    private function getForecastData(): array
    {
        $query = Opportunity::with(['project.airline', 'project.aircraftType', 'assignedTo'])
            ->whereHas('project', function($q) {
                $q->whereNotNull('expected_start_year')
                  ->whereNotNull('expected_close_year')
                  ->where('expected_start_year', '<=', $this->selectedYear)
                  ->where('expected_close_year', '>=', $this->selectedYear);
            });

        // Apply filters
        if ($this->selectedAirline) {
            $query->whereHas('project.airline', function($q) {
                $q->where('id', $this->selectedAirline);
            });
        }

        if ($this->selectedRegion) {
            $query->whereHas('project.airline', function($q) {
                $q->where('region', $this->selectedRegion);
            });
        }

        if ($this->selectedUser) {
            $query->where('assigned_to', $this->selectedUser);
        }

        $opportunities = $query->get();

        // Calculate revenue by probability category using real forecasting data
        $revenueByCategory = [
            'high' => 0,    // >= 70%
            'medium' => 0,  // 40-69%
            'low' => 0,     // < 40%
        ];

        $revenueByType = [];
        $revenueByRegion = [];
        $revenueByLinefitRetrofit = ['linefit' => 0, 'retrofit' => 0];
        $revenueByUser = [];

        foreach ($opportunities as $opportunity) {
            $yearRevenue = $opportunity->getRevenueForYear($this->selectedYear);
            $category = $opportunity->getProbabilityCategory();
            
            // Revenue by category
            $revenueByCategory[$category] += $yearRevenue;
            
            // Revenue by opportunity type
            $type = $opportunity->type->value;
            $revenueByType[$type] = ($revenueByType[$type] ?? 0) + $yearRevenue;
            
            // Revenue by region
            $region = $opportunity->project->airline->region ?? 'Unknown';
            $revenueByRegion[$region] = ($revenueByRegion[$region] ?? 0) + $yearRevenue;
            
            // Revenue by linefit/retrofit
            if ($opportunity->getLinefitRetrofit()) {
                $revenueByLinefitRetrofit[$opportunity->getLinefitRetrofit()] += $yearRevenue;
            }
            
            // Revenue by user (only if no user filter is applied to show breakdown)
            if (!$this->selectedUser && $opportunity->assignedTo) {
                $userName = $opportunity->assignedTo->name;
                $revenueByUser[$userName] = ($revenueByUser[$userName] ?? 0) + $yearRevenue;
            }
        }

        // Sort revenue by user (top performers first)
        arsort($revenueByUser);

        return [
            'revenueByCategory' => $revenueByCategory,
            'revenueByType' => $revenueByType,
            'revenueByRegion' => $revenueByRegion,
            'revenueByLinefitRetrofit' => $revenueByLinefitRetrofit,
            'revenueByUser' => $revenueByUser,
            'totalOpportunities' => $opportunities->count(),
            'totalRevenue' => array_sum($revenueByCategory),
            'baselineRevenue' => $revenueByCategory['high'],
            'conservativeRevenue' => $revenueByCategory['high'] + $revenueByCategory['medium'],
            'optimisticRevenue' => array_sum($revenueByCategory),
        ];
    }

    public function updateYear($year)
    {
        $this->selectedYear = $year;
    }

    public function clearFilters()
    {
        $this->selectedAirline = '';
        $this->selectedRegion = '';
        $this->selectedUser = '';
    }
}
