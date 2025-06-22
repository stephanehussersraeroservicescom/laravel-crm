<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\Airline;

class ProjectsTable extends Component
{
    use WithPagination;

    public $region = '';
    public $accountExecutive = '';
    public $search = '';
    public $sortField = 'region';
    public $sortDirection = 'asc';




    public function updated($property, $value)
    {
        if (in_array($property, ['region', 'accountExecutive', 'search'])) {
            $this->resetPage();
        }
    }


    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Project::with(['airline', 'aircraftType', 'verticalSurfaces', 'covers', 'panels'])
            ->when($this->region, fn($q) => $q->whereHas('airline', fn($q2) => $q2->where('region', $this->region)))
            ->when($this->accountExecutive, fn($q) => $q->whereHas('airline', fn($q2) => $q2->where('account_executive', $this->accountExecutive)))
            ->when($this->search, function ($q) {
                    $search = $this->search;
                    $q->where(function ($subQ) use ($search) {
                        $subQ->where('name', 'like', '%' . $search . '%')
                            ->orWhereHas('airline', function ($q2) use ($search) {
                                $q2->where('name', 'like', '%' . $search . '%');
                            });
                    });
                }); 


        // Sorting
        if (in_array($this->sortField, ['region', 'name', 'potential'])) {
            if ($this->sortField === 'region') {
                $query = $query->whereHas('airline', function($q) {
                    $q->orderBy('region', $this->sortDirection);
                });
            } elseif ($this->sortField === 'potential') {
                $query = $query->orderBy('potential', $this->sortDirection);
            } else {
                $query = $query->orderBy($this->sortField, $this->sortDirection);
            }
        }


        $projects = $query->paginate(12);

        $regions = Airline::select('region')->distinct()->pluck('region');
        $executives = Airline::select('account_executive')->distinct()->pluck('account_executive');

        return view('livewire.projects-table', [
            'projects' => $projects,
            'regions' => $regions,
            'executives' => $executives,
        ]);
    }
}
