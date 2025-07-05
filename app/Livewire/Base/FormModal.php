<?php

namespace App\Livewire\Base;

use Livewire\Component;

abstract class FormModal extends Component
{
    public $show = false;
    public $mode = 'create'; // create, edit, view
    public $itemId = null;
    public $loading = false;

    protected $listeners = [
        'showModal' => 'showModal',
        'hideModal' => 'hideModal',
    ];

    public function showModal($mode = 'create', $itemId = null)
    {
        $this->mode = $mode;
        $this->itemId = $itemId;
        $this->show = true;
        
        if ($mode === 'edit' && $itemId) {
            $this->loadItem();
        } elseif ($mode === 'create') {
            $this->resetForm();
        }
    }

    public function hideModal()
    {
        $this->show = false;
        $this->reset(['mode', 'itemId']);
        $this->resetForm();
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->loading = true;
        
        try {
            if ($this->mode === 'create') {
                $this->authorize('create', $this->getModelClass());
                $this->store();
            } else {
                $this->authorize('update', $this->getModelClass());
                $this->update();
            }
            
            $this->hideModal();
            $this->dispatch('item-saved', $this->mode);
            
        } catch (\Exception $e) {
            $this->addError('general', $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function delete()
    {
        if (!$this->itemId) {
            return;
        }

        $this->authorize('delete', $this->getModelClass());
        
        $this->loading = true;
        
        try {
            $item = $this->getModelClass()::findOrFail($this->itemId);
            $item->delete();
            
            $this->hideModal();
            $this->dispatch('item-deleted');
            
        } catch (\Exception $e) {
            $this->addError('general', $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    protected function loadItem()
    {
        $item = $this->getModelClass()::findOrFail($this->itemId);
        $this->populateForm($item);
    }

    // Abstract methods that must be implemented by child classes
    abstract protected function getModelClass();
    abstract protected function getValidationRules();
    abstract protected function resetForm();
    abstract protected function populateForm($item);
    abstract protected function store();
    abstract protected function update();

    protected function getFormData()
    {
        return array_intersect_key(
            $this->all(),
            array_flip($this->getFillableFields())
        );
    }

    protected function getFillableFields()
    {
        return (new ($this->getModelClass()))->getFillable();
    }

    public function render()
    {
        return view('livewire.base.form-modal');
    }
}