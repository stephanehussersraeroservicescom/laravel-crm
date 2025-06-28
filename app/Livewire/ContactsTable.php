<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Subcontractor;

class ContactsTable extends Component
{
    public Subcontractor $subcontractor;

    public function mount(Subcontractor $subcontractor)
    {
        $this->subcontractor = $subcontractor;
    }

    public function render()
    {
        return view('livewire.contacts-table', [
            'contacts' => $this->subcontractor->contacts
        ])->layout('layouts.app');
    }
}

