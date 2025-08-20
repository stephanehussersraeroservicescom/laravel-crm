<?php

namespace App\Livewire;

use App\Livewire\Base\DataTable;
use App\Models\Opportunity;
use App\Models\Project;
use App\Models\Airline;
use App\Models\User;

class OpportunityTable extends DataTable
{
    // Additional filters beyond base search
    public $filterType = '';
    public $filterCabinClass = '';
    public $filterStatus = '';
    public $filterProject = '';
    public $filterAirline = '';
    public $filterAircraftType = '';
    public $filterAssignedTo = '';
    public $showDeleted = false;

    // Override default sort
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected function getQuery()
    {
        $query = $this->showDeleted ? Opportunity::withTrashed() : Opportunity::query();
        
        // Apply filters
        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }
        
        if ($this->filterCabinClass) {
            $query->where('cabin_class', $this->filterCabinClass);
        }
        
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }
        
        if ($this->filterProject) {
            $query->where('project_id', $this->filterProject);
        }
        
        if ($this->filterAssignedTo) {
            $query->where('assigned_to', $this->filterAssignedTo);
        }
        
        if ($this->filterAirline) {
            $query->whereHas('project.airline', function ($q) {
                $q->where('id', $this->filterAirline);
            });
        }
        
        if ($this->filterAircraftType) {
            $query->whereHas('project', function ($q) {
                $q->where('aircraft_type', 'like', '%' . $this->filterAircraftType . '%');
            });
        }

        return $query->with([
            'project.airline',
            'assignedUser',
            'createdByUser',
            'certificationStatus'
        ]);
    }

    protected function getModelClass()
    {
        return Opportunity::class;
    }

    protected function getColumns()
    {
        return [
            'name' => 'Opportunity Name',
            'project' => 'Project',
            'type' => 'Type',
            'cabin_class' => 'Cabin Class',
            'status' => 'Status',
            'probability' => 'Probability',
            'potential_value' => 'Potential Value',
            'assigned_to' => 'Assigned To',
            'created_at' => 'Created',
            'actions' => 'Actions'
        ];
    }

    protected function getSearchableColumns()
    {
        return ['name', 'description', 'comments'];
    }

    public function clearFilters()
    {
        parent::clearFilters();
        $this->filterType = '';
        $this->filterCabinClass = '';
        $this->filterStatus = '';
        $this->filterProject = '';
        $this->filterAirline = '';
        $this->filterAircraftType = '';
        $this->filterAssignedTo = '';
        $this->showDeleted = false;
    }

    public function delete($opportunityId)
    {
        $opportunity = Opportunity::findOrFail($opportunityId);
        $opportunity->delete();
        
        session()->flash('message', 'Opportunity deleted successfully.');
    }

    public function restore($opportunityId)
    {
        $opportunity = Opportunity::withTrashed()->findOrFail($opportunityId);
        $opportunity->restore();
        
        session()->flash('message', 'Opportunity restored successfully.');
    }

    // Helper methods for dropdowns
    public function getProjectsProperty()
    {
        return Project::with('airline')->orderBy('name')->get();
    }

    public function getAirlinesProperty()
    {
        return Airline::orderBy('name')->get();
    }

    public function getUsersProperty()
    {
        return User::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.opportunity-table', [
            'opportunities' => $this->getTableData(),
            'projects' => $this->projects,
            'airlines' => $this->airlines,
            'users' => $this->users,
        ])->layout('layouts.app');
    }
}