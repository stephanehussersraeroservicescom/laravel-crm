<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\ProjectSubcontractorTeam;
use App\Models\Opportunity;

class ProjectOpportunity extends Component
{
    public $selectedProject = null;
    public $formSelectedProject = null; // Separate for the form
    public $highlightedProject = null; // For highlighting when coming from project creation
    public $mainSubcontractor = null;
    public $supportingSubcontractors = [];
    public $role = '';
    public $notes = '';
    public $selectedOpportunity = ''; // Combined opportunity identifier like "opportunities:123"
    public $editing = false;
    public $editId = null;
    public $showDeleted = false; // Add option to show deleted records
    public $search = ''; // Add search functionality
    public $opportunityTypeFilter = ''; // Add opportunity type filter

    public $availableRoles = [
        'Commercial',
        'Project Management',
        'Design',
        'Certification',
        'Manufacturing',
        'Subcontractor'
    ];

    public function mount($project = null)
    {
        if ($project) {
            $this->selectedProject = $project;
            $this->highlightedProject = $project;
        }
    }

    public function save()
    {
        $this->validate([
            'formSelectedProject' => 'required|exists:projects,id',
            'mainSubcontractor' => 'required|exists:subcontractors,id',
            'supportingSubcontractors' => 'nullable|array',
            'supportingSubcontractors.*' => 'exists:subcontractors,id',
            'role' => 'required|in:Commercial,Project Management,Design,Certification,Manufacturing,Subcontractor',
            'notes' => 'nullable|string',
            'selectedOpportunity' => 'required|string',
        ], [
            'formSelectedProject.required' => 'Please select a project.',
            'formSelectedProject.exists' => 'Please select a valid project.',
            'mainSubcontractor.required' => 'Please select a main subcontractor.',
            'mainSubcontractor.exists' => 'Please select a valid main subcontractor.',
            'supportingSubcontractors.array' => 'Supporting subcontractors must be a valid selection.',
            'supportingSubcontractors.*.exists' => 'Please select valid supporting subcontractors.',
            'role.required' => 'Please select a role for this team.',
            'role.in' => 'Please select a valid role.',
            'selectedOpportunity.required' => 'Please select an opportunity.',
        ]);

        // Ensure main subcontractor is not in supporting list and handle empty array
        $this->supportingSubcontractors = array_filter(
            $this->supportingSubcontractors ?: [], 
            fn($id) => $id != $this->mainSubcontractor && $id !== ''
        );

        // Parse the selected opportunity to get the ID
        $opportunityId = null;
        if ($this->selectedOpportunity) {
            [$type, $opportunityId] = explode(':', $this->selectedOpportunity);
        }

        $teamData = [
            'project_id' => $this->formSelectedProject,
            'main_subcontractor_id' => $this->mainSubcontractor,
            'role' => $this->role,
            'notes' => $this->notes,
            'opportunity_id' => $opportunityId,
        ];

        if ($this->editing && $this->editId) {
            $team = ProjectSubcontractorTeam::withTrashed()->find($this->editId);
            if ($team) {
                $team->update($teamData);
                // Sync supporting subcontractors
                $team->supportingSubcontractors()->sync($this->supportingSubcontractors);
            }
        } else {
            $team = ProjectSubcontractorTeam::create($teamData);
            // Attach supporting subcontractors
            $team->supportingSubcontractors()->sync($this->supportingSubcontractors);
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $team = ProjectSubcontractorTeam::withTrashed()->with('supportingSubcontractors')->findOrFail($id);
        $this->selectedProject = $team->project_id; // For filtering
        $this->formSelectedProject = $team->project_id; // For the form
        $this->mainSubcontractor = $team->main_subcontractor_id;
        $this->supportingSubcontractors = $team->supportingSubcontractors->pluck('id')->toArray();
        $this->role = $team->role;
        $this->notes = $team->notes;
        
        // Set the selected opportunity for the dropdown
        if ($team->opportunity_id) {
            $this->selectedOpportunity = 'opportunities:' . $team->opportunity_id;
        } else {
            $this->selectedOpportunity = '';
        }
        
        $this->editId = $id;
        $this->editing = true;
    }

    public function cancelEdit()
    {
        $this->resetFields();
    }

    public function delete($id)
    {
        $team = ProjectSubcontractorTeam::findOrFail($id);
        $team->supportingSubcontractors()->detach();
        $team->delete();
        $this->resetFields();
    }

    public function toggleShowDeleted()
    {
        $this->showDeleted = !$this->showDeleted;
        $this->resetFields();
    }

    public function restore($id)
    {
        $team = ProjectSubcontractorTeam::withTrashed()->findOrFail($id);
        $team->restore();
        $this->resetFields();
    }

    public function forceDelete($id)
    {
        $team = ProjectSubcontractorTeam::withTrashed()->findOrFail($id);
        $team->supportingSubcontractors()->detach();
        $team->forceDelete();
        $this->resetFields();
    }

    public function getOpportunitiesForProject()
    {
        if (!$this->selectedProject) {
            return [];
        }

        $opportunities = [];
        $project = Project::find($this->selectedProject);
        
        if ($project) {
            // Get all opportunities for this project using the new many-to-many relationship
            foreach ($project->opportunities as $opportunity) {
                $label = $this->getOpportunityLabel($opportunity);
                $opportunities[] = [
                    'value' => 'opportunities:' . $opportunity->id,
                    'label' => $label
                ];
            }
        }
        
        return $opportunities;
    }

    private function getOpportunityLabel($opportunity)
    {
        $cabinClass = $opportunity->cabin_class ? ucfirst($opportunity->cabin_class) . ' Cabin' : 'No Cabin';
        $type = ucwords(str_replace('_', ' ', $opportunity->type));
        
        if ($opportunity->type === 'other' && $opportunity->name) {
            return "{$cabinClass} - {$opportunity->name}";
        }
        
        return "{$cabinClass} - {$type}";
    }


    public function assignTeam($opportunityId)
    {
        $opportunity = Opportunity::find($opportunityId);
        if ($opportunity) {
            $this->selectedOpportunity = 'opportunities:' . $opportunityId;
            
            // Find the project this opportunity belongs to
            $project = $opportunity->projects()->first();
            if ($project) {
                $this->formSelectedProject = $project->id;
            }
        }
    }

    public function getFormOpportunitiesProperty()
    {
        if (!$this->formSelectedProject) {
            return [];
        }

        $opportunities = [];
        $project = Project::find($this->formSelectedProject);
        
        if ($project) {
            foreach ($project->opportunities as $opportunity) {
                $label = $this->getOpportunityLabel($opportunity);
                $opportunities[] = [
                    'value' => 'opportunities:' . $opportunity->id,
                    'label' => $label
                ];
            }
        }
        
        return $opportunities;
    }

    public function getOpportunitiesWithTeamsProperty()
    {
        $query = Opportunity::with(['projects', 'team.mainSubcontractor', 'team.supportingSubcontractors']);
        
        // Apply filters
        if ($this->selectedProject) {
            $query->whereHas('projects', function ($q) {
                $q->where('projects.id', $this->selectedProject);
            });
        }
        
        if ($this->opportunityTypeFilter) {
            $query->where('type', $this->opportunityTypeFilter);
        }
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('comments', 'like', '%' . $this->search . '%')
                  ->orWhereHas('projects', function ($projectQuery) {
                      $projectQuery->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('team.mainSubcontractor', function ($subQuery) {
                      $subQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        $opportunities = $query->get();
        $result = [];
        
        foreach ($opportunities as $opportunity) {
            foreach ($opportunity->projects as $project) {
                $team = $opportunity->team;
                
                // Apply show deleted filter
                if (!$this->showDeleted && $team && $team->trashed()) {
                    continue;
                }
                
                $result[] = [
                    'opportunity' => $opportunity,
                    'project' => $project,
                    'team' => $team
                ];
            }
        }
        
        return collect($result);
    }

    private function resetFields()
    {
        // Don't reset selectedProject if we're highlighting a specific project
        if (!$this->highlightedProject) {
            $this->selectedProject = null;
        }
        $this->formSelectedProject = null;
        $this->mainSubcontractor = null;
        $this->supportingSubcontractors = [];
        $this->role = '';
        $this->notes = '';
        $this->selectedOpportunity = '';
        $this->editing = false;
        $this->editId = null;
    }

    public function render()
    {
        $teamsQuery = $this->showDeleted 
            ? ProjectSubcontractorTeam::withTrashed()
            : ProjectSubcontractorTeam::query();
            
        $teamsQuery = $teamsQuery->with(['project.airline', 'mainSubcontractor', 'supportingSubcontractors']);
        
        // Filter by selected project if one is chosen, otherwise show all teams
        if ($this->selectedProject) {
            $teamsQuery->where('project_id', $this->selectedProject);
        }
        
        // Add search functionality
        if (!empty($this->search)) {
            $teamsQuery->where(function ($query) {
                $query->where('role', 'like', '%' . $this->search . '%')
                      ->orWhere('notes', 'like', '%' . $this->search . '%')
                      ->orWhereHas('project', function ($projectQuery) {
                          $projectQuery->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('mainSubcontractor', function ($subQuery) {
                          $subQuery->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('supportingSubcontractors', function ($supportQuery) {
                          $supportQuery->where('name', 'like', '%' . $this->search . '%');
                      });
            });
        }
        
        // Filter by opportunity type
        if (!empty($this->opportunityTypeFilter)) {
            if ($this->opportunityTypeFilter === 'general') {
                $teamsQuery->whereNull('opportunity_id');
            } else {
                $teamsQuery->whereHas('opportunity', function ($query) {
                    $query->where('type', $this->opportunityTypeFilter);
                });
            }
        }
        
        return view('livewire.project-opportunity', [
            'teams' => $teamsQuery->orderBy('created_at', 'desc')->get(),
            'projects' => Project::with('airline')->orderBy('name')->get(),
            'subcontractors' => Subcontractor::orderBy('name')->get(),
            'availableRoles' => $this->availableRoles,
            'projectOpportunities' => $this->getOpportunitiesForProject(),
            'formOpportunities' => $this->getFormOpportunitiesProperty(),
            'opportunitiesWithTeams' => $this->getOpportunitiesWithTeamsProperty(),
            'opportunities' => Opportunity::all() // For finding opportunities by ID in the view
        ])->layout('layouts.app');
    }
}
