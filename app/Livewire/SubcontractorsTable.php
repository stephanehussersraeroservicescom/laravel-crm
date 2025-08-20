<?php

namespace App\Livewire;

use App\Livewire\Base\DataTable;
use Livewire\Attributes\Validate;
use App\Models\Subcontractor;
use App\Services\CachedDataService;
use Livewire\Form;

class SubcontractorForm extends Form
{
    #[Validate('required|string|max:255')]
    public $name = '';
    
    #[Validate('nullable|string')]
    public $comment = '';
    
    #[Validate('array')]
    public $selectedParents = [];

    public function setSubcontractor(Subcontractor $subcontractor)
    {
        $this->name = $subcontractor->name;
        $this->comment = $subcontractor->comment ?? '';
        $this->selectedParents = $subcontractor->parents->pluck('id')->toArray();
    }

    public function store()
    {
        $this->validate();
        
        $subcontractor = Subcontractor::create([
            'name' => $this->name,
            'comment' => $this->comment,
        ]);
        
        $subcontractor->parents()->sync($this->selectedParents);
        
        return $subcontractor;
    }

    public function update(Subcontractor $subcontractor)
    {
        $this->validate();
        
        $subcontractor->update([
            'name' => $this->name,
            'comment' => $this->comment,
        ]);
        
        $subcontractor->parents()->sync($this->selectedParents);
        
        return $subcontractor;
    }
}

class SubcontractorsTable extends DataTable
{
    public SubcontractorForm $form;
    
    // Override default sort to use name instead of id
    public $sortField = 'name';
    public $sortDirection = 'asc';
    
    public $showModal = false;
    public $editingSubcontractor = null;
    public $showDeleted = false;
    
    // Additional filters (search is inherited from DataTable)
    public $commentFilter = '';

    public function openCreateModal()
    {
        $this->resetModal();
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetModal();
        $this->editingSubcontractor = Subcontractor::findOrFail($id);
        $this->form->setSubcontractor($this->editingSubcontractor);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->resetModal();
    }

    private function resetModal()
    {
        $this->showModal = false;
        $this->editingSubcontractor = null;
        $this->form->reset();
    }

    public function save()
    {
        if ($this->editingSubcontractor) {
            $this->form->update($this->editingSubcontractor);
            session()->flash('message', 'Subcontractor updated successfully.');
        } else {
            $this->form->store();
            session()->flash('message', 'Subcontractor created successfully.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        Subcontractor::findOrFail($id)->delete();
        session()->flash('message', 'Subcontractor deleted successfully.');
    }

    public function restore($id)
    {
        $subcontractor = Subcontractor::withTrashed()->findOrFail($id);
        $subcontractor->restore();
        session()->flash('message', 'Subcontractor restored successfully.');
    }

    public function updatingSearch()
    {
        // Reset when search changes
    }
    
    public function updatingCommentFilter()
    {
        // Reset when comment filter changes
    }
    
    public function clearFilters()
    {
        parent::clearFilters();
        $this->commentFilter = '';
        $this->showDeleted = false;
    }

    protected function getQuery()
    {
        $query = $this->showDeleted ? Subcontractor::withTrashed() : Subcontractor::query();
        
        // Apply comment filter
        if ($this->commentFilter) {
            $query->where('comment', 'like', '%' . $this->commentFilter . '%');
        }
        
        return $query->with('parents', 'contacts');
    }
    
    protected function getModelClass()
    {
        return Subcontractor::class;
    }
    
    protected function getColumns()
    {
        return [
            'name' => 'Name',
            'comment' => 'Comment',
            'parents' => 'Parent Companies',
            'contacts' => 'Contacts',
            'actions' => 'Actions'
        ];
    }
    
    protected function getSearchableColumns()
    {
        return ['name', 'comment'];
    }

    public function render()
    {
        // Get all subcontractors from cache for parent selection
        $cachedSubcontractors = CachedDataService::getSubcontractors();
        
        // Filter out the current edit ID if editing
        $availableParents = $this->editingSubcontractor 
            ? $cachedSubcontractors->where('id', '!=', $this->editingSubcontractor->id) 
            : $cachedSubcontractors;
        
        return view('livewire.subcontractors-table', [
            'subcontractors' => $this->getTableData(),
            'availableParents' => $availableParents
        ])->layout('layouts.app');
    }
}