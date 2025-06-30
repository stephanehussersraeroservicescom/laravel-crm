<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\ProjectSubcontractorTeam;
use App\Models\VerticalSurface;
use App\Models\Panel;
use App\Models\Cover;

class ProjectSubcontractorTeams extends Component
{
    public $selectedProject = null;
    public $highlightedProject = null; // For highlighting when coming from project creation
    public $mainSubcontractor = null;
    public $supportingSubcontractors = [];
    public $role = '';
    public $notes = '';
    public $opportunityType = '';
    public $opportunityId = '';
    public $editing = false;
    public $editId = null;

    public $availableRoles = [
        'Commercial',
        'Project Management',
        'Design',
        'Certification',
        'Manufacturing',
        'Subcontractor'
    ];

    public $opportunityTypes = [
        'vertical_surfaces' => 'Vertical Surfaces',
        'panels' => 'Panels',
        'covers' => 'Covers'
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
            'selectedProject' => 'required|exists:projects,id',
            'mainSubcontractor' => 'required|exists:subcontractors,id',
            'supportingSubcontractors' => 'array|min:1',
            'supportingSubcontractors.*' => 'exists:subcontractors,id',
            'role' => 'required|in:Commercial,Project Management,Design,Certification,Manufacturing,Subcontractor',
            'notes' => 'nullable|string',
            'opportunityType' => 'nullable|in:vertical_surfaces,panels,covers',
            'opportunityId' => 'nullable|integer',
        ], [
            'selectedProject.required' => 'Please select a project.',
            'selectedProject.exists' => 'Please select a valid project.',
            'mainSubcontractor.required' => 'Please select a main subcontractor.',
            'mainSubcontractor.exists' => 'Please select a valid main subcontractor.',
            'supportingSubcontractors.min' => 'Please select at least one supporting subcontractor.',
            'supportingSubcontractors.*.exists' => 'Please select valid supporting subcontractors.',
            'role.required' => 'Please select a role for this team.',
            'role.in' => 'Please select a valid role.',
            'opportunityType.in' => 'Please select a valid opportunity type.',
        ]);

        // Ensure main subcontractor is not in supporting list
        $this->supportingSubcontractors = array_filter(
            $this->supportingSubcontractors, 
            fn($id) => $id != $this->mainSubcontractor
        );

        $teamData = [
            'project_id' => $this->selectedProject,
            'main_subcontractor_id' => $this->mainSubcontractor,
            'role' => $this->role,
            'notes' => $this->notes,
            'opportunity_type' => $this->opportunityType ?: null,
            'opportunity_id' => $this->opportunityId ?: null,
        ];

        if ($this->editing && $this->editId) {
            $team = ProjectSubcontractorTeam::find($this->editId);
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
        $team = ProjectSubcontractorTeam::with('supportingSubcontractors')->findOrFail($id);
        $this->selectedProject = $team->project_id;
        $this->mainSubcontractor = $team->main_subcontractor_id;
        $this->supportingSubcontractors = $team->supportingSubcontractors->pluck('id')->toArray();
        $this->role = $team->role;
        $this->notes = $team->notes;
        $this->opportunityType = $team->opportunity_type;
        $this->opportunityId = $team->opportunity_id;
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

    public function getOpportunitiesForProject()
    {
        if (!$this->selectedProject) {
            return [];
        }

        $opportunities = [];
        $project = Project::find($this->selectedProject);
        
        if ($project) {
            // Get vertical surfaces
            $verticals = VerticalSurface::where('project_id', $project->id)->get();
            foreach ($verticals as $vertical) {
                $opportunities['vertical_surfaces'][] = [
                    'id' => $vertical->id,
                    'name' => ucfirst($vertical->cabin_class) . ' Cabin - Vertical Surfaces'
                ];
            }
            
            // Get panels
            $panels = Panel::where('project_id', $project->id)->get();
            foreach ($panels as $panel) {
                $opportunities['panels'][] = [
                    'id' => $panel->id,
                    'name' => ucfirst($panel->cabin_class) . ' Cabin - Panels'
                ];
            }
            
            // Get covers
            $covers = Cover::where('project_id', $project->id)->get();
            foreach ($covers as $cover) {
                $opportunities['covers'][] = [
                    'id' => $cover->id,
                    'name' => ucfirst($cover->cabin_class) . ' Cabin - Covers'
                ];
            }
        }
        
        return $opportunities;
    }

    private function resetFields()
    {
        // Don't reset selectedProject if we're highlighting a specific project
        if (!$this->highlightedProject) {
            $this->selectedProject = null;
        }
        $this->mainSubcontractor = null;
        $this->supportingSubcontractors = [];
        $this->role = '';
        $this->notes = '';
        $this->opportunityType = '';
        $this->opportunityId = '';
        $this->editing = false;
        $this->editId = null;
    }

    public function render()
    {
        $teamsQuery = ProjectSubcontractorTeam::with(['project.airline', 'mainSubcontractor', 'supportingSubcontractors']);
        
        // Filter by selected project if one is chosen
        if ($this->selectedProject) {
            $teamsQuery->where('project_id', $this->selectedProject);
        }
        
        return view('livewire.project-subcontractor-teams', [
            'teams' => $teamsQuery->orderBy('created_at', 'desc')->get(),
            'projects' => Project::with('airline')->orderBy('name')->get(),
            'subcontractors' => Subcontractor::orderBy('name')->get(),
            'availableRoles' => $this->availableRoles,
            'opportunityTypes' => $this->opportunityTypes,
            'projectOpportunities' => $this->getOpportunitiesForProject()
        ])->layout('layouts.app');
    }
}
