<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProjectSubcontractorTeam;
use App\Models\Opportunity;
use App\Models\Subcontractor;
use App\Models\Project;
use App\Models\Airline;
use App\Enums\TeamRole;
use App\Enums\OpportunityType;
use App\Enums\CabinClass;
use Livewire\Attributes\Validate;

class TeamManagement extends Component
{
    use WithPagination;

    // Search and Filter Properties
    public $searchOpportunityType = '';
    public $searchCabinArea = '';
    public $filterAirline = '';
    public $filterProject = '';
    public $filterOpportunityType = '';
    public $filterCabinClass = '';
    public $filterMainSubcontractor = '';
    public $filterRole = '';
    public $showDeleted = false;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Modal Properties
    public $showModal = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    public $selectedTeam = null;

    // Form Properties - Cascading Selection
    #[Validate('required|exists:airlines,id')]
    public $selected_airline_id = '';
    
    #[Validate('required|exists:projects,id')]
    public $selected_project_id = '';
    
    #[Validate('required|string')]
    public $selected_opportunity_type = '';
    
    #[Validate('required|string')]
    public $selected_cabin_class = '';
    
    #[Validate('required|exists:subcontractors,id')]
    public $main_subcontractor_id = '';
    
    #[Validate('required|string')]
    public $role = '';
    
    #[Validate('nullable|string|max:1000')]
    public $notes = '';
    
    // Computed Properties
    public $filteredProjects = [];
    public $filteredOpportunities = [];
    public $opportunity_id = '';
    public $projectCount = 0;
    public $opportunityCount = 0;
    public $totalProjectsForAirline = 0;
    public $totalOpportunitiesForSelection = 0;

    // Supporting subcontractors
    public $supportingSubcontractors = [];

    public function render()
    {
        $teams = $this->getTeams();
        $airlines = Airline::orderBy('name')->get();
        $projects = Project::with(['airline', 'aircraftType'])
            ->when($this->filterAirline, fn($q) => $q->where('airline_id', $this->filterAirline))
            ->orderBy('name')->get();
        $subcontractors = Subcontractor::orderBy('name')->get();
        
        $this->updateFilteredProjects();
        $this->updateFilteredOpportunities();

        return view('livewire.team-management', [
            'teams' => $teams,
            'airlines' => $airlines,
            'projects' => $projects,
            'subcontractors' => $subcontractors,
            'opportunityTypes' => OpportunityType::cases(),
            'cabinClasses' => CabinClass::cases(),
            'teamRoles' => TeamRole::cases(),
        ]);
    }

    public function getTeams()
    {
        $query = ProjectSubcontractorTeam::with([
            'opportunity.project.airline',
            'opportunity.project.aircraftType',
            'mainSubcontractor', 
            'supportingSubcontractors'
        ]);
        
        // Include deleted if checkbox is checked
        if ($this->showDeleted) {
            $query->withTrashed();
        }

        // Search Opportunity Type
        if ($this->searchOpportunityType) {
            $query->whereHas('opportunity', function ($oq) {
                $oq->where('type', 'like', '%' . $this->searchOpportunityType . '%');
            });
        }
        
        // Search Cabin Area
        if ($this->searchCabinArea) {
            $query->whereHas('opportunity', function ($oq) {
                $oq->where('cabin_class', 'like', '%' . $this->searchCabinArea . '%');
            });
        }

        // Filters
        if ($this->filterAirline) {
            $query->whereHas('opportunity.project', function ($pq) {
                $pq->where('airline_id', $this->filterAirline);
            });
        }
        
        if ($this->filterProject) {
            $query->whereHas('opportunity.project', function ($pq) {
                $pq->where('id', $this->filterProject);
            });
        }
        
        if ($this->filterOpportunityType) {
            $query->whereHas('opportunity', function ($oq) {
                $oq->where('type', $this->filterOpportunityType);
            });
        }
        
        if ($this->filterCabinClass) {
            $query->whereHas('opportunity', function ($oq) {
                $oq->where('cabin_class', $this->filterCabinClass);
            });
        }

        if ($this->filterMainSubcontractor) {
            $query->where('main_subcontractor_id', $this->filterMainSubcontractor);
        }

        if ($this->filterRole) {
            $query->where('role', $this->filterRole);
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
        $this->searchOpportunityType = '';
        $this->searchCabinArea = '';
        $this->filterAirline = '';
        $this->filterProject = '';
        $this->filterOpportunityType = '';
        $this->filterCabinClass = '';
        $this->filterMainSubcontractor = '';
        $this->filterRole = '';
        $this->showDeleted = false;
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function openEditModal($teamId)
    {
        $this->selectedTeam = ProjectSubcontractorTeam::with('supportingSubcontractors')->findOrFail($teamId);
        $this->fillForm($this->selectedTeam);
        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->selectedTeam = null;
    }

    public function save()
    {
        $this->validate();

        // Find or create opportunity based on selections
        if (!$this->opportunity_id) {
            if ($this->selected_project_id && $this->selected_opportunity_type && $this->selected_cabin_class) {
                $opportunity = Opportunity::firstOrCreate([
                    'project_id' => $this->selected_project_id,
                    'type' => $this->selected_opportunity_type,
                    'cabin_class' => $this->selected_cabin_class,
                ], [
                    'name' => ucfirst($this->selected_opportunity_type) . ' - ' . ucfirst(str_replace('_', ' ', $this->selected_cabin_class)),
                    'description' => 'Auto-created opportunity for team assignment',
                ]);
                $this->opportunity_id = $opportunity->id;
            } else {
                session()->flash('error', 'Please complete all selections or create the opportunity first.');
                return;
            }
        }

        if ($this->modalMode === 'create') {
            $team = ProjectSubcontractorTeam::create($this->getFormData());
            session()->flash('message', 'Team created successfully.');
        } else {
            $this->selectedTeam->update($this->getFormData());
            $team = $this->selectedTeam;
            session()->flash('message', 'Team updated successfully.');
        }

        // Sync supporting subcontractors
        $team->supportingSubcontractors()->sync($this->supportingSubcontractors);

        $this->closeModal();
    }

    public function delete($teamId)
    {
        $team = ProjectSubcontractorTeam::withTrashed()->findOrFail($teamId);
        if ($team->trashed()) {
            $team->restore();
            session()->flash('message', 'Team restored successfully.');
        } else {
            $team->delete();
            session()->flash('message', 'Team deleted successfully.');
        }
    }

    public function addSupportingSubcontractor()
    {
        $this->supportingSubcontractors[] = '';
    }

    public function removeSupportingSubcontractor($index)
    {
        unset($this->supportingSubcontractors[$index]);
        $this->supportingSubcontractors = array_values($this->supportingSubcontractors);
    }

    private function resetForm()
    {
        $this->selected_airline_id = '';
        $this->selected_project_id = '';
        $this->selected_opportunity_type = '';
        $this->selected_cabin_class = '';
        $this->main_subcontractor_id = '';
        $this->role = '';
        $this->notes = '';
        $this->opportunity_id = '';
        $this->filteredProjects = collect();
        $this->filteredOpportunities = collect();
        $this->supportingSubcontractors = [];
        $this->projectCount = 0;
        $this->opportunityCount = 0;
        $this->totalProjectsForAirline = 0;
        $this->totalOpportunitiesForSelection = 0;
    }

    private function fillForm($team)
    {
        $this->opportunity_id = $team->opportunity_id;
        $this->selected_airline_id = $team->opportunity->project->airline_id;
        $this->selected_project_id = $team->opportunity->project_id;
        $this->selected_opportunity_type = $team->opportunity->type->value;
        $this->selected_cabin_class = $team->opportunity->cabin_class->value;
        $this->main_subcontractor_id = $team->main_subcontractor_id;
        $this->role = $team->role->value;
        $this->notes = $team->notes;
        $this->supportingSubcontractors = $team->supportingSubcontractors->pluck('id')->toArray();
        
        $this->updateFilteredProjects();
        $this->updateFilteredOpportunities();
    }

    private function getFormData()
    {
        return [
            'opportunity_id' => $this->opportunity_id,
            'main_subcontractor_id' => $this->main_subcontractor_id,
            'role' => $this->role,
            'notes' => $this->notes,
        ];
    }

    // Cascading filter methods
    public function updatedSelectedAirlineId()
    {
        $this->selected_opportunity_type = '';
        $this->selected_cabin_class = '';
        $this->selected_project_id = '';
        $this->opportunity_id = '';
        $this->updateFilteredProjects();
        $this->updateFilteredOpportunities();
    }
    
    public function updatedSelectedOpportunityType()
    {
        $this->selected_cabin_class = '';
        $this->selected_project_id = '';
        $this->opportunity_id = '';
        $this->updateFilteredProjects();
        $this->updateFilteredOpportunities();
    }
    
    public function updatedSelectedCabinClass()
    {
        $this->selected_project_id = '';
        $this->opportunity_id = '';
        $this->updateFilteredProjects();
        $this->updateFilteredOpportunities();
    }
    
    public function updatedSelectedProjectId()
    {
        $this->opportunity_id = '';
        $this->updateFilteredOpportunities();
    }
    
    private function updateFilteredProjects()
    {
        if ($this->selected_airline_id) {
            // Get total projects for this airline (for summary)
            $this->totalProjectsForAirline = Project::where('airline_id', $this->selected_airline_id)->count();
            
            $query = Project::where('airline_id', $this->selected_airline_id);
            
            // If we have opportunity type and cabin class, filter by them
            if ($this->selected_opportunity_type && $this->selected_cabin_class) {
                $query->whereHas('opportunities', function ($q) {
                    $q->where('type', $this->selected_opportunity_type)
                      ->where('cabin_class', $this->selected_cabin_class);
                });
            }
            
            $this->filteredProjects = $query->orderBy('name')->get();
            $this->projectCount = $this->filteredProjects->count();
        } else {
            $this->filteredProjects = collect();
            $this->projectCount = 0;
            $this->totalProjectsForAirline = 0;
        }
        
        $this->updateTotalOpportunitiesForSelection();
    }
    
    private function updateFilteredOpportunities()
    {
        if ($this->selected_project_id) {
            $query = Opportunity::where('project_id', $this->selected_project_id);
            
            if ($this->selected_opportunity_type) {
                $query->where('type', $this->selected_opportunity_type);
            }
            
            if ($this->selected_cabin_class) {
                $query->where('cabin_class', $this->selected_cabin_class);
            }
            
            $this->filteredOpportunities = $query->get();
            $this->opportunityCount = $this->filteredOpportunities->count();
            
            // Auto-select opportunity if only one matches
            if ($this->filteredOpportunities->count() === 1) {
                $this->opportunity_id = $this->filteredOpportunities->first()->id;
            }
        } else {
            $this->filteredOpportunities = collect();
            
            // Count opportunities based on current selections for display purposes
            if ($this->selected_airline_id && $this->selected_opportunity_type && $this->selected_cabin_class) {
                $this->opportunityCount = Opportunity::whereHas('project', function ($q) {
                    $q->where('airline_id', $this->selected_airline_id);
                })
                ->where('type', $this->selected_opportunity_type)
                ->where('cabin_class', $this->selected_cabin_class)
                ->count();
            } else {
                $this->opportunityCount = 0;
            }
        }
    }
    
    private function updateTotalOpportunitiesForSelection()
    {
        if ($this->selected_airline_id) {
            $query = Opportunity::whereHas('project', function ($q) {
                $q->where('airline_id', $this->selected_airline_id);
            });
            
            if ($this->selected_opportunity_type) {
                $query->where('type', $this->selected_opportunity_type);
            }
            
            if ($this->selected_cabin_class) {
                $query->where('cabin_class', $this->selected_cabin_class);
            }
            
            $this->totalOpportunitiesForSelection = $query->count();
        } else {
            $this->totalOpportunitiesForSelection = 0;
        }
    }

    public function updatedSearchOpportunityType()
    {
        $this->resetPage();
    }
    
    public function updatedSearchCabinArea()
    {
        $this->resetPage();
    }

    public function updatedFilterProject()
    {
        $this->resetPage();
        // Reset opportunity filter when project changes
        $this->filterOpportunity = '';
    }

    public function updatedFilterOpportunity()
    {
        $this->resetPage();
    }

    public function updatedFilterMainSubcontractor()
    {
        $this->resetPage();
    }

    public function updatedFilterRole()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
    
    public function updatedFilterAirline()
    {
        $this->resetPage();
    }
    
    public function updatedFilterOpportunityType()
    {
        $this->resetPage();
    }
    
    public function updatedFilterCabinClass()
    {
        $this->resetPage();
    }
    
    public function updatedShowDeleted()
    {
        $this->resetPage();
    }

    // Confirmation dialog methods
    public function createProject()
    {
        // Store current state in session to pre-fill when creating project
        session()->put('team_creation_context', [
            'airline_id' => $this->selected_airline_id,
            'opportunity_type' => $this->selected_opportunity_type,
            'cabin_class' => $this->selected_cabin_class,
        ]);
        
        // Redirect to project management page
        return redirect()->route('manage.projects');
    }

    public function cancelNoProject()
    {
        $this->closeModal();
        session()->flash('message', 'Team creation cancelled - no matching project exists for the selected criteria.');
    }

    public function createOpportunity()
    {
        // Store current state in session to pre-fill when creating opportunity
        session()->put('team_creation_context', [
            'airline_id' => $this->selected_airline_id,
            'project_id' => $this->selected_project_id,
            'opportunity_type' => $this->selected_opportunity_type,
            'cabin_class' => $this->selected_cabin_class,
        ]);
        
        // Redirect to opportunity management page
        return redirect()->route('manage.opportunities');
    }

    public function createOpportunityInExistingProject()
    {
        // Get the first available project for this airline that matches the criteria
        $availableProject = null;
        if ($this->selected_airline_id) {
            $availableProject = Project::where('airline_id', $this->selected_airline_id)->first();
        }
        
        // Store current state in session to pre-fill when creating opportunity
        session()->put('team_creation_context', [
            'airline_id' => $this->selected_airline_id,
            'project_id' => $availableProject ? $availableProject->id : null,
            'opportunity_type' => $this->selected_opportunity_type,
            'cabin_class' => $this->selected_cabin_class,
            'create_in_existing_project' => true,
        ]);
        
        // Redirect to opportunity management page
        return redirect()->route('manage.opportunities');
    }

    public function cancelNoOpportunity()
    {
        $this->closeModal();
        session()->flash('message', 'Team creation cancelled - no matching opportunity exists for the selected criteria.');
    }
}