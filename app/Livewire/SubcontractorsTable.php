<?php

namespace App\Livewire;

use Livewire\Component;
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

class SubcontractorsTable extends Component
{
    public SubcontractorForm $form;
    
    public $showModal = false;
    public $editingSubcontractor = null;
    public $showDeleted = false;
    
    // Search and filtering
    public $search = '';
    public $commentFilter = '';

    public function openCreateModal()
    {
        $this->resetModal();
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        logger("Edit method called with ID: $id");
        $this->resetModal();
        $this->editingSubcontractor = Subcontractor::findOrFail($id);
        $this->form->setSubcontractor($this->editingSubcontractor);
        $this->showModal = true;
        logger("Modal should be open now: " . ($this->showModal ? 'true' : 'false'));
        
        // Force a refresh to ensure modal state is updated
        $this->dispatch('modal-updated');
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
        $this->search = '';
        $this->commentFilter = '';
        $this->showDeleted = false;
    }

    public function render()
    {
        $subcontractorsQuery = $this->showDeleted ? Subcontractor::withTrashed() : Subcontractor::query();
        
        // Apply search filter
        if ($this->search) {
            $subcontractorsQuery->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('comment', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply comment filter
        if ($this->commentFilter) {
            $subcontractorsQuery->where('comment', 'like', '%' . $this->commentFilter . '%');
        }
        
        // Get all subcontractors from cache
        $cachedSubcontractors = CachedDataService::getSubcontractors();
        
        // Filter out the current edit ID if editing
        $availableParents = $this->editingSubcontractor 
            ? $cachedSubcontractors->where('id', '!=', $this->editingSubcontractor->id) 
            : $cachedSubcontractors;
        
        return view('livewire.subcontractors-table', [
            'subcontractors' => $subcontractorsQuery->with('parents', 'contacts')->orderBy('name')->get(),
            'availableParents' => $availableParents
        ])->layout('layouts.app');
    }
}