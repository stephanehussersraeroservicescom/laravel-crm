<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Opportunity;
use App\Models\Project;
use App\Models\Status;
use App\Models\User;
use App\Enums\OpportunityType;
use App\Enums\CabinClass;
use App\Enums\OpportunityStatus;
use Livewire\Attributes\Validate;

class OpportunityManagement extends Component
{
    use WithPagination;

    // Search and Filter Properties
    public $search = '';
    public $filterType = '';
    public $filterCabinClass = '';
    public $filterStatus = '';
    public $filterProject = '';
    public $filterAirline = '';
    public $filterAircraftType = '';
    public $filterAssignedTo = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $showDeleted = false;

    // Modal Properties
    public $showModal = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    public $selectedOpportunity = null;

    // Form Properties
    #[Validate('required|exists:projects,id')]
    public $project_id = '';
    
    #[Validate('required|string')]
    public $type = '';
    
    #[Validate('nullable|string')]
    public $cabin_class = '';
    
    #[Validate('required|string')]
    public $status = 'active';
    
    #[Validate('required|integer|min:0|max:100')]
    public $probability = 50;
    
    #[Validate('required|numeric|min:0')]
    public $potential_value = 0;
    
    #[Validate('nullable|string|max:255')]
    public $name = '';
    
    #[Validate('nullable|string|max:1000')]
    public $description = '';
    
    #[Validate('nullable|string|max:1000')]
    public $comments = '';
    
    #[Validate('nullable|exists:statuses,id')]
    public $certification_status_id = '';
    
    #[Validate('required|exists:users,id')]
    public $assigned_to = '';
    
    #[Validate('nullable|exists:users,id')]
    public $created_by = '';

    public function mount()
    {
        $this->created_by = auth()->id();
        $this->assigned_to = auth()->id();
        
        // Check for team creation context (redirected from team management)
        if (session()->has('team_creation_context')) {
            $context = session()->get('team_creation_context');
            session()->forget('team_creation_context');
            
            // Pre-fill form with context data and open modal
            if (isset($context['airline_id'])) {
                $this->filterAirline = $context['airline_id'];
            }
            
            if (isset($context['project_id'])) {
                $this->project_id = $context['project_id'];
            }
            
            if (isset($context['opportunity_type'])) {
                $this->type = $context['opportunity_type'];
            }
            
            if (isset($context['cabin_class'])) {
                $this->cabin_class = $context['cabin_class'];
            }
            
            // Auto-generate name: Airline + Aircraft Type + Project Name + Opportunity Type
            if (isset($context['project_id'])) {
                $project = Project::with(['airline', 'aircraftType'])->find($context['project_id']);
                if ($project) {
                    $nameParts = [];
                    
                    // Add airline name
                    if ($project->airline) {
                        $nameParts[] = $project->airline->name;
                    }
                    
                    // Add aircraft type if available
                    if ($project->aircraftType) {
                        $nameParts[] = $project->aircraftType->name;
                    }
                    
                    // Add project name
                    $nameParts[] = $project->name;
                    
                    // Add opportunity type if available
                    if (isset($context['opportunity_type'])) {
                        $nameParts[] = ucfirst($context['opportunity_type']);
                    }
                    
                    $this->name = implode(' - ', $nameParts);
                }
            } elseif (isset($context['opportunity_type']) && isset($context['cabin_class'])) {
                // Fallback to simple naming if project not available
                $this->name = ucfirst($context['opportunity_type']) . ' - ' . ucfirst(str_replace('_', ' ', $context['cabin_class']));
            }
            
            $this->description = 'Created from team management for staffing purposes';
            $this->assigned_to = auth()->id();
            
            // Open the modal automatically
            $this->modalMode = 'create';
            $this->showModal = true;
            
            session()->flash('message', 'Opportunity creation form pre-filled from team management context.');
        } else {
            // Set default filter to current user if they have any opportunities
            $currentUserId = auth()->id();
            $userHasOpportunities = Opportunity::where('assigned_to', $currentUserId)->exists();
            
            if ($userHasOpportunities) {
                $this->filterAssignedTo = $currentUserId;
            }
        }
    }

    public function render()
    {
        $opportunities = $this->getOpportunities();
        // Get unique projects
        $projects = Project::with('airline')
            ->select('projects.*')
            ->distinct()
            ->orderBy('name')
            ->get();
        $airlines = \App\Models\Airline::orderBy('name')->get();
        $aircraftTypes = \App\Models\AircraftType::orderBy('name')->get();
        $statuses = Status::where('type', 'certification')->get();
        $users = User::orderBy('name')->get();

        return view('livewire.opportunity-management', [
            'opportunities' => $opportunities,
            'projects' => $projects,
            'airlines' => $airlines,
            'aircraftTypes' => $aircraftTypes,
            'statuses' => $statuses,
            'users' => $users,
            'opportunityTypes' => OpportunityType::cases(),
            'cabinClasses' => CabinClass::cases(),
            'opportunityStatuses' => OpportunityStatus::cases(),
        ])->layout('layouts.app');
    }

    public function getOpportunities()
    {
        $query = Opportunity::with(['project.airline', 'project.aircraftType', 'certificationStatus', 'assignedTo', 'createdBy', 'deletedBy']);
        
        // Include soft deleted records if checkbox is checked
        if ($this->showDeleted) {
            $query->withTrashed();
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('comments', 'like', '%' . $this->search . '%')
                  ->orWhereHas('project', function ($pq) {
                      $pq->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('project.airline', function ($aq) {
                      $aq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filters
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

        if ($this->filterAirline) {
            $query->whereHas('project', function ($q) {
                $q->where('airline_id', $this->filterAirline);
            });
        }

        if ($this->filterAircraftType) {
            $query->whereHas('project', function ($q) {
                $q->where('aircraft_type_id', $this->filterAircraftType);
            });
        }

        if ($this->filterAssignedTo) {
            $query->where('assigned_to', $this->filterAssignedTo);
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
        $this->filterType = '';
        $this->filterCabinClass = '';
        $this->filterStatus = '';
        $this->filterProject = '';
        $this->filterAirline = '';
        $this->filterAircraftType = '';
        $this->filterAssignedTo = '';
        $this->showDeleted = false;
        $this->resetPage();
    }

    public function updatedShowDeleted()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function openEditModal($opportunityId)
    {
        $this->selectedOpportunity = Opportunity::findOrFail($opportunityId);
        $this->fillForm($this->selectedOpportunity);
        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->selectedOpportunity = null;
    }

    public function save()
    {
        $this->validate();
        
        // Check if assigned_to field is empty
        if (empty($this->assigned_to)) {
            $this->addError('assigned_to', 'The assigned to field is required.');
            return;
        }
        
        // Additional enum validation
        $typeValues = array_column(OpportunityType::cases(), 'value');
        $statusValues = array_column(OpportunityStatus::cases(), 'value');
        $cabinValues = array_column(CabinClass::cases(), 'value');
        
        if (!in_array($this->type, $typeValues)) {
            $this->addError('type', 'Invalid opportunity type.');
            return;
        }
        
        if (!in_array($this->status, $statusValues)) {
            $this->addError('status', 'Invalid opportunity status.');
            return;
        }
        
        if ($this->cabin_class && !in_array($this->cabin_class, $cabinValues)) {
            $this->addError('cabin_class', 'Invalid cabin class.');
            return;
        }

        try {
            if ($this->modalMode === 'create') {
                $this->created_by = auth()->id();
                $data = $this->getFormData();
                $data['updated_by'] = auth()->id();
                Opportunity::create($data);
                session()->flash('message', 'Opportunity created successfully.');
            } else {
                $data = $this->getFormData();
                $data['updated_by'] = auth()->id();
                $this->selectedOpportunity->update($data);
                session()->flash('message', 'Opportunity updated successfully.');
            }
            
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving opportunity: ' . $e->getMessage());
        }
    }

    public function delete($opportunityId)
    {
        try {
            $opportunity = Opportunity::findOrFail($opportunityId);
            $opportunity->update(['deleted_by' => auth()->id()]);
            $opportunity->delete();
            session()->flash('message', 'Opportunity deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting opportunity: ' . $e->getMessage());
        }
    }

    public function restore($opportunityId)
    {
        try {
            $opportunity = Opportunity::withTrashed()->findOrFail($opportunityId);
            $opportunity->update(['deleted_by' => null]);
            $opportunity->restore();
            session()->flash('message', 'Opportunity restored successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error restoring opportunity: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->project_id = '';
        $this->type = '';
        $this->cabin_class = '';
        $this->status = 'active';
        $this->probability = 50;
        $this->potential_value = 0;
        $this->name = '';
        $this->description = '';
        $this->comments = '';
        $this->certification_status_id = '';
        $this->assigned_to = auth()->id();
        $this->created_by = auth()->id();
    }

    private function fillForm($opportunity)
    {
        $this->project_id = $opportunity->project_id;
        $this->type = $opportunity->type?->value ?? $opportunity->type;
        $this->cabin_class = $opportunity->cabin_class?->value ?? $opportunity->cabin_class;
        $this->status = $opportunity->status?->value ?? $opportunity->status;
        $this->probability = $opportunity->probability;
        $this->potential_value = $opportunity->potential_value;
        $this->name = $opportunity->name;
        $this->description = $opportunity->description;
        $this->comments = $opportunity->comments;
        $this->certification_status_id = $opportunity->certification_status_id;
        $this->assigned_to = $opportunity->assigned_to;
        $this->created_by = $opportunity->created_by;
    }

    private function getFormData()
    {
        return [
            'project_id' => $this->project_id,
            'type' => $this->type,
            'cabin_class' => $this->cabin_class,
            'status' => $this->status,
            'probability' => $this->probability,
            'potential_value' => $this->potential_value,
            'name' => $this->name,
            'description' => $this->description,
            'comments' => $this->comments,
            'certification_status_id' => $this->certification_status_id ?: null,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedFilterCabinClass()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterProject()
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

    public function updatedFilterAssignedTo()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}