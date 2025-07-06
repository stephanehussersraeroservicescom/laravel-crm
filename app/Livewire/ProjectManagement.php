<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\Airline;
use App\Models\AircraftType;
use App\Models\Status;
use App\Models\User;
use Livewire\Attributes\Validate;

class ProjectManagement extends Component
{
    use WithPagination;

    // Search and Filter Properties
    public $search = '';
    public $filterAirline = '';
    public $filterAircraftType = '';
    public $filterDesignStatus = '';
    public $filterCommercialStatus = '';
    public $showDeleted = false;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Modal Properties
    public $showModal = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    public $selectedProject = null;

    // Form Properties
    #[Validate('required|string|max:255')]
    public $name = '';
    
    #[Validate('required|exists:airlines,id')]
    public $airline_id = '';
    
    #[Validate('nullable|exists:aircraft_types,id')]
    public $aircraft_type_id = '';
    
    #[Validate('nullable|integer|min:1')]
    public $number_of_aircraft = '';
    
    #[Validate('nullable|exists:statuses,id')]
    public $design_status_id = '';
    
    #[Validate('nullable|exists:statuses,id')]
    public $commercial_status_id = '';
    
    #[Validate('nullable|string')]
    public $owner = '';
    
    #[Validate('nullable|string|max:1000')]
    public $comment = '';

    public function mount()
    {
        // Check for team creation context (redirected from team management)
        if (session()->has('team_creation_context')) {
            $context = session()->get('team_creation_context');
            session()->forget('team_creation_context');
            
            // Pre-fill form with context data and open modal
            if (isset($context['airline_id'])) {
                $this->airline_id = $context['airline_id'];
                $this->filterAirline = $context['airline_id'];
            }
            
            // Auto-generate project name
            if (isset($context['opportunity_type']) && isset($context['airline_id'])) {
                $airline = Airline::find($context['airline_id']);
                $this->name = $airline->name . ' - ' . ucfirst($context['opportunity_type']) . ' Project';
            }
            
            $this->owner = auth()->user()->name;
            $this->comment = 'Created from team management for ' . (isset($context['opportunity_type']) ? $context['opportunity_type'] : 'opportunity') . ' staffing';
            
            // Open the modal automatically
            $this->modalMode = 'create';
            $this->showModal = true;
            
            session()->flash('message', 'Project creation form pre-filled from team management context.');
        }
    }

    public function render()
    {
        $projects = $this->getProjects();
        $airlines = Airline::orderBy('name')->get();
        $aircraftTypes = AircraftType::orderBy('name')->get();
        $statuses = Status::orderBy('status')->get();
        $users = User::whereIn('role', ['sales', 'manager'])->orderBy('name')->get();

        return view('livewire.project-management', [
            'projects' => $projects,
            'airlines' => $airlines,
            'aircraftTypes' => $aircraftTypes,
            'statuses' => $statuses,
            'users' => $users,
        ]);
    }

    public function getProjects()
    {
        $query = Project::with(['airline', 'aircraftType', 'designStatus', 'commercialStatus']);
        
        // Include deleted if checkbox is checked
        if ($this->showDeleted) {
            $query->withTrashed();
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('owner', 'like', '%' . $this->search . '%')
                  ->orWhere('comment', 'like', '%' . $this->search . '%')
                  ->orWhereHas('airline', function ($aq) {
                      $aq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filters
        if ($this->filterAirline) {
            $query->where('airline_id', $this->filterAirline);
        }
        
        if ($this->filterAircraftType) {
            $query->where('aircraft_type_id', $this->filterAircraftType);
        }
        
        if ($this->filterDesignStatus) {
            $query->where('design_status_id', $this->filterDesignStatus);
        }
        
        if ($this->filterCommercialStatus) {
            $query->where('commercial_status_id', $this->filterCommercialStatus);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterAirline = '';
        $this->filterAircraftType = '';
        $this->filterDesignStatus = '';
        $this->filterCommercialStatus = '';
        $this->showDeleted = false;
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function openEditModal($projectId)
    {
        $this->selectedProject = Project::withTrashed()->findOrFail($projectId);
        $this->fillForm($this->selectedProject);
        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->selectedProject = null;
    }

    public function save()
    {
        $this->validate();

        if ($this->modalMode === 'create') {
            Project::create($this->getFormData());
            session()->flash('message', 'Project created successfully.');
        } else {
            $this->selectedProject->update($this->getFormData());
            session()->flash('message', 'Project updated successfully.');
        }

        $this->closeModal();
    }

    public function delete($projectId)
    {
        $project = Project::withTrashed()->findOrFail($projectId);
        if ($project->trashed()) {
            $project->restore();
            session()->flash('message', 'Project restored successfully.');
        } else {
            $project->delete();
            session()->flash('message', 'Project deleted successfully.');
        }
    }

    private function resetForm()
    {
        $this->name = '';
        $this->airline_id = '';
        $this->aircraft_type_id = '';
        $this->number_of_aircraft = '';
        $this->design_status_id = '';
        $this->commercial_status_id = '';
        $this->owner = '';
        $this->comment = '';
    }

    private function fillForm($project)
    {
        $this->name = $project->name;
        $this->airline_id = $project->airline_id;
        $this->aircraft_type_id = $project->aircraft_type_id;
        $this->number_of_aircraft = $project->number_of_aircraft;
        $this->design_status_id = $project->design_status_id;
        $this->commercial_status_id = $project->commercial_status_id;
        $this->owner = $project->owner;
        $this->comment = $project->comment;
    }

    private function getFormData()
    {
        return [
            'name' => $this->name,
            'airline_id' => $this->airline_id,
            'aircraft_type_id' => $this->aircraft_type_id ?: null,
            'number_of_aircraft' => $this->number_of_aircraft ?: null,
            'design_status_id' => $this->design_status_id ?: null,
            'commercial_status_id' => $this->commercial_status_id ?: null,
            'owner' => $this->owner,
            'comment' => $this->comment,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterAirline()
    {
        $this->resetPage();
    }

    public function updatedFilterAircraftType()
    {
        $this->resetPage();
    }

    public function updatedFilterDesignStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterCommercialStatus()
    {
        $this->resetPage();
    }

    public function updatedShowDeleted()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}