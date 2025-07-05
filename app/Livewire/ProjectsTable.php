<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\Airline;
use App\Models\Subcontractor;
use App\Models\User;
use App\Models\AircraftType;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;

class ProjectsTable extends Component
{
    use WithPagination;

    public $region = '';
    public $accountExecutive = '';
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    // Modal properties
    public $showModal = false;
    public $showNewAirlineForm = false;
    public $showSubcontractorConfirm = false;
    public $showDisclosureModal = false;
    public $createdProjectId = null;
    public $selectedProject = null;
    public $name = '';
    public $selectedAirline = null;
    public $newAirlineName = '';
    public $newAirlineRegion = '';
    public $newAirlineAccountExecutive = '';
    public $aircraft_type_id = null;
    public $number_of_aircraft = '';
    public $design_status_id = null;
    public $commercial_status_id = null;
    public $comment = '';
    public $editing = false;
    public $editId = null;
    
    // Confidential airline fields
    public $isConfidential = false;
    public $airlineCodePlaceholder = '';
    public $confidentialityNotes = '';
    public $newAirlineId = '';
    public $disclosureReason = '';
    public $filterAirlineDisclosed = '';

    // Opportunity types (new system)
    public $opportunityTypes = [
        'vertical' => 'Vertical',
        'panels' => 'Panels', 
        'covers' => 'Covers',
        'others' => 'Others'
    ];
    public $selectedOpportunityTypes = [];

    public $availableRegions = [
        'North America',
        'South America', 
        'Europe',
        'Asia',
        'Africa',
        'Oceania',
        'Middle East'
    ];

    public $isCreatingAirline = false;
    public $showDeleted = false; // Add option to show deleted records

    public function mount()
    {
        // Pre-select current user if they have sales role
        if (Auth::check() && Auth::user()->role === 'sales') {
            $this->newAirlineAccountExecutive = Auth::user()->name;
        }
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModalFields();
    }

    public function save()
    {
        // Convert empty strings to null for numeric fields
        $this->aircraft_type_id = $this->aircraft_type_id ?: null;
        $this->number_of_aircraft = $this->number_of_aircraft ?: null;
        $this->design_status_id = $this->design_status_id ?: null;
        $this->commercial_status_id = $this->commercial_status_id ?: null;
        
        $validationRules = [
            'name' => 'required|string|max:255',
            'aircraft_type_id' => 'nullable|integer|exists:aircraft_types,id',
            'number_of_aircraft' => 'nullable|integer|min:1',
            'design_status_id' => 'nullable|integer|exists:statuses,id',
            'commercial_status_id' => 'nullable|integer|exists:statuses,id',
            'comment' => 'nullable|string',
        ];

        if ($this->isConfidential) {
            $validationRules['airlineCodePlaceholder'] = 'nullable|string|max:50';
            $validationRules['confidentialityNotes'] = 'nullable|string';
        } else {
            $validationRules['selectedAirline'] = 'required_without_all:newAirlineName,newAirlineRegion';
            $validationRules['newAirlineName'] = 'required_without:selectedAirline|string|max:255';
            $validationRules['newAirlineRegion'] = 'required_with:newAirlineName|in:' . implode(',', $this->availableRegions);
        }
        
        $this->validate($validationRules, [
            'name.required' => 'Project name is required.',
            'selectedAirline.required_without_all' => 'Please select an existing airline or create a new one.',
            'newAirlineName.required_without' => 'Airline name is required when creating a new airline.',
            'newAirlineRegion.required_with' => 'Region is required when creating a new airline.',
            'newAirlineRegion.in' => 'Please select a valid region.',
            'aircraft_type_id.exists' => 'Please select a valid aircraft type.',
            'aircraft_type_id.integer' => 'Aircraft type must be a valid selection.',
            'number_of_aircraft.integer' => 'Number of aircraft must be a valid number.',
            'number_of_aircraft.min' => 'Number of aircraft must be at least 1.',
            'design_status_id.exists' => 'Please select a valid design status.',
            'design_status_id.integer' => 'Design status must be a valid selection.',
            'commercial_status_id.exists' => 'Please select a valid commercial status.',
            'commercial_status_id.integer' => 'Commercial status must be a valid selection.',
        ]);

        // Handle airline creation or selection
        if ($this->isConfidential) {
            // Get the confidential airline
            $confidentialAirline = Airline::where('code', 'CONFIDENTIAL')->first();
            $airlineId = $confidentialAirline->id;
        } elseif ($this->selectedAirline) {
            $airlineId = $this->selectedAirline;
        } else {
            // Create new airline
            $airline = Airline::create([
                'name' => $this->newAirlineName,
                'region' => $this->newAirlineRegion,
                'account_executive' => $this->newAirlineAccountExecutive,
            ]);
            $airlineId = $airline->id;
        }

        if ($this->editing && $this->editId) {
            $project = Project::withTrashed()->find($this->editId);
            if ($project) {
                $project->update([
                    'name' => $this->name,
                    'airline_id' => $airlineId,
                    'aircraft_type_id' => $this->aircraft_type_id,
                    'number_of_aircraft' => $this->number_of_aircraft,
                    'design_status_id' => $this->design_status_id,
                    'commercial_status_id' => $this->commercial_status_id,
                    'comment' => $this->comment,
                ]);
            }
            $this->closeModal();
        } else {
            $project = Project::create([
                'name' => $this->name,
                'airline_id' => $airlineId,
                'aircraft_type_id' => $this->aircraft_type_id,
                'number_of_aircraft' => $this->number_of_aircraft,
                'design_status_id' => $this->design_status_id,
                'commercial_status_id' => $this->commercial_status_id,
                'comment' => $this->comment,
                'airline_disclosed' => !$this->isConfidential,
                'airline_code_placeholder' => $this->isConfidential ? $this->airlineCodePlaceholder : null,
                'confidentiality_notes' => $this->isConfidential ? $this->confidentialityNotes : null,
            ]);
            
            // Create opportunities for the project
            $this->createOpportunities($project->id);
            
            // Store the created project ID and show subcontractor confirmation
            $this->createdProjectId = $project->id;
            $this->showModal = false;
            // Reset fields but preserve the confirmation state and project ID
            $this->name = '';
            $this->selectedAirline = null;
            $this->showNewAirlineForm = false;
            $this->newAirlineName = '';
            $this->newAirlineRegion = '';
            $this->newAirlineAccountExecutive = '';
            $this->aircraft_type_id = null;
            $this->number_of_aircraft = '';
            $this->design_status_id = null;
            $this->commercial_status_id = null;
            $this->comment = '';
            $this->editing = false;
            $this->editId = null;
            $this->selectedOpportunities = [];
            
            $this->showSubcontractorConfirm = true;
        }
    }

    public function edit($id)
    {
        $project = Project::withTrashed()->findOrFail($id);
        $this->name = $project->name;
        $this->selectedAirline = $project->airline_id;
        $this->aircraft_type_id = $project->aircraft_type_id;
        $this->number_of_aircraft = $project->number_of_aircraft;
        $this->design_status_id = $project->design_status_id;
        $this->commercial_status_id = $project->commercial_status_id;
        $this->comment = $project->comment;
        $this->editId = $id;
        $this->editing = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        Project::findOrFail($id)->delete();
    }

    public function toggleShowDeleted()
    {
        $this->showDeleted = !$this->showDeleted;
    }

    public function restore($id)
    {
        $project = Project::withTrashed()->findOrFail($id);
        $project->restore();
    }

    public function forceDelete($id)
    {
        $project = Project::withTrashed()->findOrFail($id);
        $project->forceDelete();
    }

    private function resetModalFields()
    {
        $this->name = '';
        $this->selectedAirline = null;
        $this->showNewAirlineForm = false;
        $this->showSubcontractorConfirm = false;
        $this->createdProjectId = null;
        $this->newAirlineName = '';
        $this->newAirlineRegion = '';
        $this->newAirlineAccountExecutive = '';
        $this->aircraft_type_id = null;
        $this->number_of_aircraft = '';
        $this->design_status_id = null;
        $this->commercial_status_id = null;
        $this->comment = '';
        $this->selectedOpportunities = [];
        $this->editing = false;
        $this->editId = null;
        
        // Re-select current user if they have sales role
        if (Auth::check() && Auth::user()->role === 'sales') {
            $this->newAirlineAccountExecutive = Auth::user()->name;
        }
    }

    public function updated($property, $value)
    {
        if (in_array($property, ['region', 'accountExecutive', 'search'])) {
            $this->resetPage();
        }
        
        // Hide new airline form when existing airline is selected
        if ($property === 'selectedAirline' && $value) {
            $this->showNewAirlineForm = false;
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
    }    public function addSubcontractorsNow()
    {
        // Store the project ID before resetting fields
        $projectId = $this->createdProjectId;
        
        $this->showSubcontractorConfirm = false;
        $this->resetModalFields();

        // Redirect to project teams page with the created project
        return $this->redirect(route('project.teams', ['project' => $projectId]));
    }

    public function addSubcontractorsLater()
    {
        $this->showSubcontractorConfirm = false;
        $this->resetModalFields();
    }

    private function createOpportunities($projectId)
    {
        foreach ($this->selectedOpportunityTypes as $opportunityType) {
            // Create default opportunities for each cabin class
            $cabinClasses = ['first_class', 'business_class', 'premium_economy', 'economy'];
            
            foreach ($cabinClasses as $cabinClass) {
                $opportunity = \App\Models\Opportunity::create([
                    'type' => $opportunityType,
                    'cabin_class' => $cabinClass,
                    'status' => 'draft',
                    'probability' => 50, // Default 50%
                    'created_by' => auth()->id(),
                ]);
                
                // Attach to project
                $opportunity->projects()->attach($projectId);
            }
        }
    }

    // Airline disclosure management methods
    public function openDisclosureModal($projectId)
    {
        $this->selectedProject = Project::find($projectId);
        
        if (!$this->selectedProject->isAirlineDisclosed()) {
            $this->showDisclosureModal = true;
            $this->newAirlineId = '';
            $this->disclosureReason = '';
        }
    }

    public function closeDisclosureModal()
    {
        $this->showDisclosureModal = false;
        $this->selectedProject = null;
        $this->newAirlineId = '';
        $this->disclosureReason = '';
        $this->resetErrorBag();
    }

    public function discloseAirline()
    {
        $this->validate([
            'newAirlineId' => 'required|exists:airlines,id',
            'disclosureReason' => 'required|string|min:10',
        ]);

        if (!$this->selectedProject) {
            return;
        }

        // Check that the selected airline is not the confidential one
        $airline = Airline::find($this->newAirlineId);
        if ($airline->code === 'CONFIDENTIAL') {
            $this->addError('newAirlineId', 'Cannot select the confidential airline placeholder.');
            return;
        }

        // Disclose the airline
        $success = $this->selectedProject->discloseAirline($this->newAirlineId, auth()->id());

        if ($success) {
            $this->closeDisclosureModal();
            session()->flash('message', 'Airline has been successfully disclosed.');
        } else {
            $this->addError('newAirlineId', 'Failed to disclose airline. Please try again.');
        }
    }

    public function markAsConfidential($projectId, $placeholderCode = null)
    {
        $project = Project::find($projectId);
        $project->markAsConfidential($placeholderCode, 'Marked as confidential via project table');
        
        session()->flash('message', 'Project marked as confidential.');
    }

    public function render()
    {
        $query = $this->showDeleted ? Project::withTrashed() : Project::query();
        
        $query = $query->with(['airline', 'aircraftType', 'designStatus', 'commercialStatus'])
            ->when($this->region, fn($q) => $q->whereHas('airline', fn($q2) => $q2->where('region', $this->region)))
            ->when($this->accountExecutive, fn($q) => $q->whereHas('airline', fn($q2) => $q2->where('account_executive', $this->accountExecutive)))
            ->when($this->filterAirlineDisclosed !== '', function ($query) {
                if ($this->filterAirlineDisclosed === 'disclosed') {
                    $query->disclosed();
                } elseif ($this->filterAirlineDisclosed === 'confidential') {
                    $query->nonDisclosed();
                }
            })
            ->when($this->search, function ($q) {
                    $search = $this->search;
                    $q->where(function ($subQ) use ($search) {
                        $subQ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('airline_code_placeholder', 'like', '%' . $search . '%')
                            ->orWhereHas('airline', function ($q2) use ($search) {
                                $q2->where('name', 'like', '%' . $search . '%');
                            });
                    });
                }); 

        // Sorting
        if (in_array($this->sortField, ['name', 'created_at'])) {
            $query = $query->orderBy($this->sortField, $this->sortDirection);
        } elseif ($this->sortField === 'airline') {
            $query = $query->join('airlines', 'projects.airline_id', '=', 'airlines.id')->orderBy('airlines.name', $this->sortDirection)->select('projects.*');
        }

        $projects = $query->orderBy('created_at', 'desc')->get();

        $regions = Airline::select('region')->distinct()->pluck('region');
        $executives = Airline::select('account_executive')->distinct()->pluck('account_executive');

        return view('livewire.projects-table', [
            'projects' => $projects,
            'regions' => $regions,
            'executives' => $executives,
            'airlines' => Airline::where('code', '!=', 'CONFIDENTIAL')->orderBy('name')->get(),
            'disclosableAirlines' => Airline::where('code', '!=', 'CONFIDENTIAL')->orderBy('name')->get(),
            'aircraftTypes' => AircraftType::orderBy('name')->get(),
            'statuses' => Status::orderBy('status')->get(),
            'salesUsers' => User::where('role', 'sales')->orderBy('name')->get(),
            'availableRegions' => $this->availableRegions,
            'opportunities' => $this->opportunities,
        ])->layout('layouts.app');
    }
}
