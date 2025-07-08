<?php

namespace App\Livewire\Forecasting;

use Livewire\Component;
use App\Models\Opportunity;
use App\Models\Airline;
use App\Models\User;

class Analytics extends Component
{
    // Filters for 5-Year Forecast Chart
    public $forecastAirline = '';
    public $forecastRegion = '';
    public $forecastUser = '';
    
    // Filters for Region Chart
    public $regionYear;
    public $regionAirline = '';
    public $regionUser = '';
    
    // Filters for Type Chart
    public $typeYear;
    public $typeAirline = '';
    public $typeRegion = '';
    public $typeUser = '';
    
    public $yearRange = [];

    protected $queryString = [
        'forecastAirline' => ['except' => ''],
        'forecastRegion' => ['except' => ''],
        'forecastUser' => ['except' => ''],
        'regionYear' => ['except' => ''],
        'regionAirline' => ['except' => ''],
        'regionUser' => ['except' => ''],
        'typeYear' => ['except' => ''],
        'typeAirline' => ['except' => ''],
        'typeRegion' => ['except' => ''],
        'typeUser' => ['except' => '']
    ];

    public function mount()
    {
        $this->regionYear = (int) date('Y');
        $this->typeYear = (int) date('Y');
        $this->yearRange = range(date('Y'), date('Y') + 4); // Next 5 years
    }

    public function render()
    {
        $forecastData = $this->getForecastChartData();
        $regionData = $this->getRegionChartData();
        $typeData = $this->getTypeChartData();
        
        $airlines = Airline::orderBy('name')->get();
        $regions = Airline::distinct()->pluck('region')->filter()->sort();
        $salesUsers = User::whereIn('role', ['sales', 'managers'])
            ->orderBy('name')
            ->get();

        return view('livewire.forecasting.analytics', [
            'forecastData' => $forecastData,
            'regionData' => $regionData,
            'typeData' => $typeData,
            'airlines' => $airlines,
            'regions' => $regions,
            'salesUsers' => $salesUsers,
            'yearRange' => $this->yearRange,
        ])->layout('layouts.app');
    }

    private function getForecastChartData(): array
    {
        $query = Opportunity::with(['project.airline', 'project.aircraftType', 'assignedTo'])
            ->whereHas('project', function($q) {
                $q->whereNotNull('expected_start_year')
                  ->whereNotNull('expected_close_year');
            });

        // Apply forecast-specific filters
        if ($this->forecastAirline) {
            $query->whereHas('project.airline', function($q) {
                $q->where('id', $this->forecastAirline);
            });
        }

        if ($this->forecastRegion) {
            $query->whereHas('project.airline', function($q) {
                $q->where('region', $this->forecastRegion);
            });
        }

        if ($this->forecastUser) {
            $query->where('assigned_to', $this->forecastUser);
        }

        $opportunities = $query->get();
        $yearlyRevenue = $this->calculateYearlyRevenue($opportunities);

        return [
            'yearlyRevenue' => $yearlyRevenue,
            'totalOpportunities' => $opportunities->count(),
        ];
    }

    private function getRegionChartData(): array
    {
        $query = Opportunity::with(['project.airline', 'project.aircraftType', 'assignedTo'])
            ->whereHas('project', function($q) {
                $q->whereNotNull('expected_start_year')
                  ->whereNotNull('expected_close_year')
                  ->where('expected_start_year', '<=', $this->regionYear)
                  ->where('expected_close_year', '>=', $this->regionYear);
            });

        // Apply region chart specific filters
        if ($this->regionAirline) {
            $query->whereHas('project.airline', function($q) {
                $q->where('id', $this->regionAirline);
            });
        }

        if ($this->regionUser) {
            $query->where('assigned_to', $this->regionUser);
        }

        $opportunities = $query->get();
        $revenueByRegion = $this->calculateRevenueByRegionForYear($opportunities, $this->regionYear);

        return [
            'revenueByRegion' => $revenueByRegion,
            'totalOpportunities' => $opportunities->count(),
        ];
    }

    private function getTypeChartData(): array
    {
        $query = Opportunity::with(['project.airline', 'project.aircraftType', 'assignedTo'])
            ->whereHas('project', function($q) {
                $q->whereNotNull('expected_start_year')
                  ->whereNotNull('expected_close_year')
                  ->where('expected_start_year', '<=', $this->typeYear)
                  ->where('expected_close_year', '>=', $this->typeYear);
            });

        // Apply type chart specific filters
        if ($this->typeAirline) {
            $query->whereHas('project.airline', function($q) {
                $q->where('id', $this->typeAirline);
            });
        }

        if ($this->typeRegion) {
            $query->whereHas('project.airline', function($q) {
                $q->where('region', $this->typeRegion);
            });
        }

        if ($this->typeUser) {
            $query->where('assigned_to', $this->typeUser);
        }

        $opportunities = $query->get();
        $revenueByType = $this->calculateRevenueByTypeForYear($opportunities, $this->typeYear);

        return [
            'revenueByType' => $revenueByType,
            'totalOpportunities' => $opportunities->count(),
        ];
    }

    private function calculateYearlyRevenue($opportunities): array
    {
        $yearlyData = [];
        
        foreach ($this->yearRange as $year) {
            $yearlyData[$year] = [
                'baseline' => 0,
                'conservative' => 0,
                'optimistic' => 0,
            ];
            
            foreach ($opportunities as $opportunity) {
                $revenue = $opportunity->getRevenueForYear($year);
                $category = $opportunity->getProbabilityCategory();
                
                if ($category === 'high') {
                    $yearlyData[$year]['baseline'] += $revenue;
                    $yearlyData[$year]['conservative'] += $revenue;
                    $yearlyData[$year]['optimistic'] += $revenue;
                } elseif ($category === 'medium') {
                    $yearlyData[$year]['conservative'] += $revenue;
                    $yearlyData[$year]['optimistic'] += $revenue;
                } else {
                    $yearlyData[$year]['optimistic'] += $revenue;
                }
            }
        }
        
        return $yearlyData;
    }

    private function calculateRevenueByRegionPerYear($opportunities): array
    {
        $regionData = [];
        
        foreach ($this->yearRange as $year) {
            $regionData[$year] = [];
            
            foreach ($opportunities as $opportunity) {
                $revenue = $opportunity->getRevenueForYear($year);
                $region = $opportunity->project->airline->region ?? 'Unknown';
                
                if (!isset($regionData[$year][$region])) {
                    $regionData[$year][$region] = 0;
                }
                $regionData[$year][$region] += $revenue;
            }
            
            // Sort regions by revenue for each year
            if (!empty($regionData[$year])) {
                arsort($regionData[$year]);
            }
        }
        
        return $regionData;
    }

    private function calculateRevenueByCategory($opportunities): array
    {
        $data = ['high' => 0, 'medium' => 0, 'low' => 0];
        
        foreach ($opportunities as $opportunity) {
            $revenue = $opportunity->getRevenueForYear($this->selectedYear);
            $category = $opportunity->getProbabilityCategory();
            $data[$category] += $revenue;
        }
        
        return $data;
    }

    private function calculateRevenueByType($opportunities): array
    {
        $data = [];
        
        foreach ($opportunities as $opportunity) {
            $revenue = $opportunity->getRevenueForYear($this->selectedYear);
            $type = $opportunity->type->value;
            $data[$type] = ($data[$type] ?? 0) + $revenue;
        }
        
        return $data;
    }

    private function calculateRevenueByRegion($opportunities): array
    {
        $data = [];
        
        foreach ($opportunities as $opportunity) {
            $revenue = $opportunity->getRevenueForYear($this->regionYear);
            $region = $opportunity->project->airline->region ?? 'Unknown';
            $data[$region] = ($data[$region] ?? 0) + $revenue;
        }
        
        arsort($data);
        return $data;
    }

    private function calculateRevenueByRegionForYear($opportunities, $year): array
    {
        $data = [];
        
        foreach ($opportunities as $opportunity) {
            $revenue = $opportunity->getRevenueForYear($year);
            $region = $opportunity->project->airline->region ?? 'Unknown';
            $data[$region] = ($data[$region] ?? 0) + $revenue;
        }
        
        arsort($data);
        return $data;
    }

    private function calculateRevenueByTypeForYear($opportunities, $year): array
    {
        $data = [];
        
        foreach ($opportunities as $opportunity) {
            $revenue = $opportunity->getRevenueForYear($year);
            $type = $opportunity->type->value;
            $data[$type] = ($data[$type] ?? 0) + $revenue;
        }
        
        return $data;
    }

    private function calculateRevenueByUser($opportunities): array
    {
        $data = [];
        
        foreach ($opportunities as $opportunity) {
            if ($opportunity->assignedTo) {
                $revenue = $opportunity->getRevenueForYear($this->selectedYear);
                $userName = $opportunity->assignedTo->name;
                $data[$userName] = ($data[$userName] ?? 0) + $revenue;
            }
        }
        
        arsort($data);
        return array_slice($data, 0, 10, true);
    }

    private function calculateMonthlyTrends($opportunities): array
    {
        // For demo, we'll simulate monthly breakdown for the selected year
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = [];
        
        foreach ($months as $index => $month) {
            $data[$month] = 0;
            foreach ($opportunities as $opportunity) {
                // Simulate monthly distribution (in real app, you'd have actual monthly data)
                $yearRevenue = $opportunity->getRevenueForYear($this->selectedYear);
                $data[$month] += $yearRevenue / 12; // Simple equal distribution
            }
        }
        
        return $data;
    }

    public function clearForecastFilters()
    {
        $this->forecastAirline = '';
        $this->forecastRegion = '';
        $this->forecastUser = '';
        $this->dispatch('forecastUpdated');
    }

    public function clearRegionFilters()
    {
        $this->regionAirline = '';
        $this->regionUser = '';
        $this->dispatch('regionUpdated');
    }

    public function clearTypeFilters()
    {
        $this->typeAirline = '';
        $this->typeRegion = '';
        $this->typeUser = '';
        $this->dispatch('typeUpdated');
    }

    // Forecast chart updates
    public function updatedForecastAirline() { $this->dispatch('forecastUpdated'); }
    public function updatedForecastRegion() { $this->dispatch('forecastUpdated'); }
    public function updatedForecastUser() { $this->dispatch('forecastUpdated'); }

    // Region chart updates
    public function updatedRegionYear() { $this->dispatch('regionUpdated'); }
    public function updatedRegionAirline() { $this->dispatch('regionUpdated'); }
    public function updatedRegionUser() { $this->dispatch('regionUpdated'); }

    // Type chart updates
    public function updatedTypeYear() { $this->dispatch('typeUpdated'); }
    public function updatedTypeAirline() { $this->dispatch('typeUpdated'); }
    public function updatedTypeRegion() { $this->dispatch('typeUpdated'); }
    public function updatedTypeUser() { $this->dispatch('typeUpdated'); }

    public function getForecastDataForJs()
    {
        return $this->getForecastChartData();
    }

    public function getRegionDataForJs()
    {
        return $this->getRegionChartData();
    }

    public function getTypeDataForJs()
    {
        return $this->getTypeChartData();
    }
}
