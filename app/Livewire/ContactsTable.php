<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Subcontractor;
use App\Models\Contact;

class ContactsTable extends Component
{
    public Subcontractor $subcontractor;
    
    // Form fields
    public $name = '';
    public $email = '';
    public $role = '';
    public $phone = '';
    
    // Edit state
    public $editing = false;
    public $editId = null;
    
    // Filtering
    public $search = '';
    public $roleFilter = '';
    
    // Success state
    public $success = false;
    public $showDeleted = false; // Add option to show deleted records

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
        ], [
            'name.required' => 'Contact name is required.',
            'name.max' => 'Contact name cannot be longer than 255 characters.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email cannot be longer than 255 characters.',
            'role.max' => 'Role cannot be longer than 255 characters.',
            'phone.max' => 'Phone cannot be longer than 255 characters.',
        ]);

        $contactData = [
            'subcontractor_id' => $this->subcontractor->id,
            'name' => $this->name,
            'email' => $this->email ?: null,
            'role' => $this->role ?: null,
            'phone' => $this->phone ?: null,
        ];

        if ($this->editing && $this->editId) {
            $contact = Contact::find($this->editId);
            if ($contact && $contact->subcontractor_id === $this->subcontractor->id) {
                $contact->update($contactData);
            }
        } else {
            Contact::create($contactData);
        }

        $this->resetFields();
        $this->success = true;
        
        // Hide success message after 3 seconds
        $this->dispatch('success-timer');
    }

    public function edit($id)
    {
        $contact = Contact::where('subcontractor_id', $this->subcontractor->id)->findOrFail($id);
        $this->name = $contact->name;
        $this->email = $contact->email;
        $this->role = $contact->role;
        $this->phone = $contact->phone;
        $this->editId = $id;
        $this->editing = true;
        $this->success = false;
    }

    public function cancelEdit()
    {
        $this->resetFields();
    }

    public function delete($id)
    {
        $contact = Contact::where('subcontractor_id', $this->subcontractor->id)->findOrFail($id);
        $contact->delete();
        $this->resetFields();
    }

    public function toggleShowDeleted()
    {
        $this->showDeleted = !$this->showDeleted;
        $this->resetFields();
    }

    public function restore($id)
    {
        $contact = Contact::withTrashed()->where('subcontractor_id', $this->subcontractor->id)->findOrFail($id);
        $contact->restore();
        $this->resetFields();
    }

    public function forceDelete($id)
    {
        $contact = Contact::withTrashed()->where('subcontractor_id', $this->subcontractor->id)->findOrFail($id);
        $contact->forceDelete();
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->name = '';
        $this->email = '';
        $this->role = '';
        $this->phone = '';
        $this->editing = false;
        $this->editId = null;
    }

    public function render()
    {
        $contactsQuery = $this->showDeleted 
            ? Contact::withTrashed()->where('subcontractor_id', $this->subcontractor->id)
            : Contact::where('subcontractor_id', $this->subcontractor->id);
        
        // Apply search filter
        if ($this->search) {
            $contactsQuery->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply role filter
        if ($this->roleFilter) {
            $contactsQuery->where('role', 'like', '%' . $this->roleFilter . '%');
        }
        
        // Get distinct roles for filter dropdown (include deleted if showing deleted)
        $rolesQuery = $this->showDeleted 
            ? Contact::withTrashed()->where('subcontractor_id', $this->subcontractor->id)
            : Contact::where('subcontractor_id', $this->subcontractor->id);
            
        $availableRoles = $rolesQuery->whereNotNull('role')
            ->where('role', '!=', '')
            ->distinct()
            ->pluck('role')
            ->sort()
            ->values();

        return view('livewire.contacts-table', [
            'contacts' => $contactsQuery->orderBy('name')->get(),
            'availableRoles' => $availableRoles
        ])->layout('layouts.app');
    }
}

