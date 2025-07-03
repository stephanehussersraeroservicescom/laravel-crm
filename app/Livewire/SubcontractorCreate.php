<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Subcontractor;

class SubcontractorCreate extends Component
{
    public $name;
    public $success = false;

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:subcontractors,name',
        ]);
        Subcontractor::create(['name' => $this->name]);
        $this->reset('name');
        $this->success = true;
    }

    public function render()
    {
        return view('livewire.subcontractor-create')->layout('layouts.app');
    }
}
