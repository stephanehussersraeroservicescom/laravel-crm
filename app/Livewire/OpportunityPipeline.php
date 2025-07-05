<?php

namespace App\Livewire;

use App\Models\Opportunity;
use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class OpportunityPipeline extends Component
{
    use WithPagination;

    public $search = '';
    public $filterProject = '';
    public $filterType = '';
    public $filterCabinClass = '';
    public $filterProbability = '';
    public $showModal = false;
    public $modalMode = 'create';
    public $selectedOpportunity = null;

    // Opportunity form fields
    public $opportunity = [
        'project_id' => '',
        'type' => 'others',
        'cabin_class' => '',
        'probability' => '',
        'potential_value' => '',
        'status' => 'draft',
        'name' => '',
        'description' => '',
        'comments' => '',
    ];

    public $statuses = [
        'draft' => 'Draft',
        'active' => 'Active',
        'review' => 'Under Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'on_hold' => 'On Hold',
        'completed' => 'Completed',
    ];

    public $types = [
        'vertical' => 'Vertical',
        'panels' => 'Panels',
        'covers' => 'Covers',
        'others' => 'Others',
    ];

    public $cabinClasses = [
        'first_class' => 'First Class',
        'business_class' => 'Business Class',
        'premium_economy' => 'Premium Economy',
        'economy' => 'Economy',
    ];

    protected $listeners = [
        'opportunity-moved' => 'moveOpportunity',
        'opportunity-updated' => '$refresh',
    ];

    public function mount()
    {
        $this->authorize('view', Opportunity::class);
    }

    public function moveOpportunity($opportunityId, $newStatus)
    {
        $opportunity = Opportunity::findOrFail($opportunityId);
        $this->authorize('update', $opportunity);

        $opportunity->update(['status' => $newStatus]);
        
        $this->dispatch('opportunity-moved', [
            'id' => $opportunityId,
            'status' => $newStatus,
        ]);
    }

    public function openModal($mode = 'create', $opportunityId = null)
    {
        $this->modalMode = $mode;
        $this->selectedOpportunity = $opportunityId;
        
        if ($mode === 'edit' && $opportunityId) {
            $opportunity = Opportunity::findOrFail($opportunityId);
            $this->opportunity = $opportunity->toArray();
        } else {
            $this->resetOpportunityForm();
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetOpportunityForm();
        $this->resetErrorBag();
    }

    public function saveOpportunity()
    {
        $this->validate([
            'opportunity.project_id' => 'required|exists:projects,id',
            'opportunity.type' => 'required|in:' . implode(',', array_keys($this->types)),
            'opportunity.cabin_class' => 'required|in:' . implode(',', array_keys($this->cabinClasses)),
            'opportunity.probability' => 'nullable|integer|min:0|max:100',
            'opportunity.potential_value' => 'nullable|numeric|min:0',
            'opportunity.status' => 'required|in:' . implode(',', array_keys($this->statuses)),
            'opportunity.name' => 'nullable|string|max:255',
            'opportunity.description' => 'nullable|string',
            'opportunity.comments' => 'nullable|string',
        ]);

        if ($this->modalMode === 'create') {
            $this->authorize('create', Opportunity::class);
            $opportunity = Opportunity::create($this->opportunity);
        } else {
            $opportunity = Opportunity::findOrFail($this->selectedOpportunity);
            $this->authorize('update', $opportunity);
            $opportunity->update($this->opportunity);
        }

        $this->closeModal();
        $this->dispatch('opportunity-updated');
        
        session()->flash('message', 'Opportunity ' . ($this->modalMode === 'create' ? 'created' : 'updated') . ' successfully.');
    }

    public function deleteOpportunity($opportunityId)
    {
        $opportunity = Opportunity::findOrFail($opportunityId);
        $this->authorize('delete', $opportunity);
        
        $opportunity->delete();
        $this->dispatch('opportunity-updated');
        
        session()->flash('message', 'Opportunity deleted successfully.');
    }

    private function resetOpportunityForm()
    {
        $this->opportunity = [
            'project_id' => '',
            'type' => 'others',
            'cabin_class' => '',
            'probability' => '',
            'potential_value' => '',
            'status' => 'draft',
            'name' => '',
            'description' => '',
            'comments' => '',
        ];
    }

    public function getOpportunitiesByStatus()
    {
        $query = Opportunity::with(['project', 'certificationStatus', 'team.mainSubcontractor'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('comments', 'like', '%' . $this->search . '%')
                        ->orWhereHas('project', function ($q) {
                            $q->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filterProject, function ($q) {
                $q->where('project_id', $this->filterProject);
            })
            ->when($this->filterType, function ($q) {
                $q->where('type', $this->filterType);
            })
            ->when($this->filterCabinClass, function ($q) {
                $q->where('cabin_class', $this->filterCabinClass);
            })
            ->when($this->filterProbability, function ($q) {
                $q->where('probability', '>=', $this->filterProbability);
            });

        $opportunities = [];
        foreach ($this->statuses as $status => $label) {
            $opportunities[$status] = $query->clone()->where('status', $status)->get();
        }

        return $opportunities;
    }

    public function render()
    {
        return view('livewire.opportunity-pipeline', [
            'opportunitiesByStatus' => $this->getOpportunitiesByStatus(),
            'projects' => Project::all(),
        ]);
    }
}