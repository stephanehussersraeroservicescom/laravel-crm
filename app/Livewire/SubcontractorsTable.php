<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Subcontractor;

class SubcontractorsTable extends Component
{
    public $name = '';
    public $comment = '';
    public $selectedParents = [];
    public $editing = false;
    public $editId = null;
    public $showDeleted = false; // Add option to show deleted records

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'comment' => 'nullable|string',
            'selectedParents' => 'array',
            'selectedParents.*' => 'exists:subcontractors,id',
        ]);

        if ($this->editing && $this->editId) {
            $subcontractor = Subcontractor::find($this->editId);
            if ($subcontractor) {
                $subcontractor->update([
                    'name' => $this->name,
                    'comment' => $this->comment,
                ]);
                // Sync parent relationships
                $subcontractor->parents()->sync($this->selectedParents);
            }
        } else {
            $subcontractor = Subcontractor::create([
                'name' => $this->name,
                'comment' => $this->comment,
            ]);
            // Attach parent relationships
            $subcontractor->parents()->sync($this->selectedParents);
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $subcontractor = Subcontractor::findOrFail($id);
        $this->name = $subcontractor->name;
        $this->comment = $subcontractor->comment;
        $this->selectedParents = $subcontractor->parents->pluck('id')->toArray();
        $this->editId = $id;
        $this->editing = true;
    }

    public function cancelEdit()
    {
        $this->resetFields();
    }

    public function delete($id)
    {
        Subcontractor::findOrFail($id)->delete();
        $this->resetFields();
    }

    public function toggleShowDeleted()
    {
        $this->showDeleted = !$this->showDeleted;
        $this->resetFields();
    }

    public function restore($id)
    {
        $subcontractor = Subcontractor::withTrashed()->findOrFail($id);
        $subcontractor->restore();
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->name = '';
        $this->comment = '';
        $this->selectedParents = [];
        $this->editing = false;
        $this->editId = null;
    }

    public function render()
    {
        $subcontractorsQuery = $this->showDeleted ? Subcontractor::withTrashed() : Subcontractor::query();
        
        return view('livewire.subcontractors-table', [
            'subcontractors' => $subcontractorsQuery->with('parents', 'contacts')->orderBy('name')->get(),
            'availableParents' => Subcontractor::where('id', '!=', $this->editId ?? 0)->orderBy('name')->get()
        ])->layout('layouts.app');
    }
}