<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProjectSubcontractorTeam;
use App\Models\Opportunity;
use App\Models\Subcontractor;
use App\Models\Project;
use Livewire\Attributes\Validate;

class TeamManagement extends Component
{
    use WithPagination;

    // Search and Filter Properties
    public $search = '';
    public $filterProject = '';
    public $filterOpportunity = '';
    public $filterMainSubcontractor = '';
    public $filterRole = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Modal Properties
    public $showModal = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    public $selectedTeam = null;

    // Form Properties
    #[Validate('required|exists:opportunities,id')]
    public $opportunity_id = '';
    
    #[Validate('required|exists:subcontractors,id')]
    public $main_subcontractor_id = '';
    
    #[Validate('nullable|string|max:255')]
    public $role = '';
    
    #[Validate('nullable|string|max:1000')]
    public $notes = '';

    // Supporting subcontractors
    public $supportingSubcontractors = [];

    public function render()
    {
        $teams = $this->getTeams();
        $opportunities = Opportunity::with('project.airline')->get();
        $projects = Project::with('airline')->get();
        $subcontractors = Subcontractor::orderBy('name')->get();

        return view('livewire.team-management', [
            'teams' => $teams,
            'opportunities' => $opportunities,
            'projects' => $projects,
            'subcontractors' => $subcontractors,
            'roles' => $this->getAvailableRoles(),
        ]);
    }

    public function getTeams()
    {
        $query = ProjectSubcontractorTeam::with([
            'opportunity.project.airline', 
            'mainSubcontractor', 
            'supportingSubcontractors'
        ]);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('role', 'like', '%' . $this->search . '%')
                  ->orWhere('notes', 'like', '%' . $this->search . '%')
                  ->orWhereHas('opportunity', function ($oq) {
                      $oq->where('name', 'like', '%' . $this->search . '%')
                         ->orWhereHas('project', function ($pq) {
                             $pq->where('name', 'like', '%' . $this->search . '%');
                         });
                  })
                  ->orWhereHas('mainSubcontractor', function ($sq) {
                      $sq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filters
        if ($this->filterProject) {
            $query->whereHas('opportunity.project', function ($pq) {
                $pq->where('id', $this->filterProject);
            });
        }

        if ($this->filterOpportunity) {
            $query->where('opportunity_id', $this->filterOpportunity);
        }

        if ($this->filterMainSubcontractor) {
            $query->where('main_subcontractor_id', $this->filterMainSubcontractor);
        }

        if ($this->filterRole) {
            $query->where('role', 'like', '%' . $this->filterRole . '%');
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
        $this->filterProject = '';
        $this->filterOpportunity = '';
        $this->filterMainSubcontractor = '';
        $this->filterRole = '';
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
        $team = ProjectSubcontractorTeam::findOrFail($teamId);
        $team->delete();
        session()->flash('message', 'Team deleted successfully.');
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
        $this->opportunity_id = '';
        $this->main_subcontractor_id = '';
        $this->role = '';
        $this->notes = '';
        $this->supportingSubcontractors = [];
    }

    private function fillForm($team)
    {
        $this->opportunity_id = $team->opportunity_id;
        $this->main_subcontractor_id = $team->main_subcontractor_id;
        $this->role = $team->role;
        $this->notes = $team->notes;
        $this->supportingSubcontractors = $team->supportingSubcontractors->pluck('id')->toArray();
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

    private function getAvailableRoles()
    {
        return [
            'Design',
            'Manufacturing',
            'Commercial',
            'Certification',
            'Project Management',
            'Quality Assurance',
            'Testing',
            'Installation',
            'Support',
        ];
    }

    public function updatedSearch()
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
}