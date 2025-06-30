<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Contact;
use App\Models\Subcontractor;

class ContactCreate extends Component
{
    public $subcontractor;
    public $name, $email, $role, $phone;
    public $success = false;

    public function mount(Subcontractor $subcontractor)
    {
        $this->subcontractor = $subcontractor;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'role' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);
        Contact::create([
            'subcontractor_id' => $this->subcontractor->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
        ]);
        $this->reset(['name', 'email', 'role', 'phone']);
        $this->success = true;
    }

    public function render()
    {
        return view('livewire.contact-create')->layout('layouts.app');
    }
}

