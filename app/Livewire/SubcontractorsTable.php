<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Subcontractor;

class SubcontractorsTable extends Component
{
    public function render()
    {
        return view('livewire.subcontractors-table', [
            'subcontractors' => Subcontractor::with('parent', 'contacts')->get()
        ])->layout('layouts.app');
    }
}
