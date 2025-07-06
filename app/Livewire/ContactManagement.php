<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Contact;
use App\Models\Subcontractor;
use App\Enums\ContactRole;
use Livewire\Attributes\Validate;

class ContactManagement extends Component
{
    use WithPagination;

    // Search and Filter Properties
    public $search = '';
    public $filterSubcontractor = '';
    public $filterRole = '';
    public $showDeleted = false;
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Modal Properties
    public $showModal = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    public $selectedContact = null;

    // Form Properties
    #[Validate('required|exists:subcontractors,id')]
    public $subcontractor_id = '';
    
    #[Validate('required|string|max:255')]
    public $name = '';
    
    #[Validate('required|email|max:255')]
    public $email = '';
    
    #[Validate('nullable|string|in:engineering,program_management,design,certification')]
    public $role = '';
    
    #[Validate('nullable|string|max:20')]
    public $phone = '';
    
    #[Validate('nullable|date')]
    public $consent_given_at = '';

    public function render()
    {
        $contacts = $this->getContacts();
        $subcontractors = Subcontractor::orderBy('name')->get();

        return view('livewire.contact-management', [
            'contacts' => $contacts,
            'subcontractors' => $subcontractors,
            'roles' => ContactRole::cases(),
        ]);
    }

    public function getContacts()
    {
        $query = Contact::with('subcontractor');
        
        // Include deleted if checkbox is checked
        if ($this->showDeleted) {
            $query->withTrashed();
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('role', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhereHas('subcontractor', function ($sq) {
                      $sq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filters
        if ($this->filterSubcontractor) {
            $query->where('subcontractor_id', $this->filterSubcontractor);
        }

        if ($this->filterRole) {
            $query->where('role', $this->filterRole);
        }


        // Sorting
        if ($this->sortBy === 'subcontractor_name') {
            $query->join('subcontractors', 'contacts.subcontractor_id', '=', 'subcontractors.id')
                  ->orderBy('subcontractors.name', $this->sortDirection)
                  ->select('contacts.*');
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query->paginate($this->perPage);
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterSubcontractor = '';
        $this->filterRole = '';
        $this->showDeleted = false;
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function openEditModal($contactId)
    {
        $this->selectedContact = Contact::findOrFail($contactId);
        $this->fillForm($this->selectedContact);
        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->selectedContact = null;
    }

    public function save()
    {
        $this->validate();

        $data = $this->getFormData();

        if ($this->modalMode === 'create') {
            Contact::create($data);
            $this->dispatch('refresh-contacts');
            session()->flash('message', 'Contact created successfully.');
        } else {
            $this->selectedContact->update($data);
            $this->dispatch('refresh-contacts');
            session()->flash('message', 'Contact updated successfully.');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete($contactId)
    {
        $contact = Contact::withTrashed()->findOrFail($contactId);
        if ($contact->trashed()) {
            $contact->restore();
            session()->flash('message', 'Contact restored successfully.');
        } else {
            $contact->delete();
            session()->flash('message', 'Contact deleted successfully.');
        }
        $this->dispatch('refresh-contacts');
    }


    private function resetForm()
    {
        $this->subcontractor_id = '';
        $this->name = '';
        $this->email = '';
        $this->role = '';
        $this->phone = '';
        $this->consent_given_at = '';
    }

    private function fillForm($contact)
    {
        $this->subcontractor_id = $contact->subcontractor_id;
        $this->name = $contact->name;
        $this->email = $contact->email;
        $this->role = $contact->role?->value;
        $this->phone = $contact->phone;
        $this->consent_given_at = $contact->consent_given_at?->format('Y-m-d');
    }

    private function getFormData()
    {
        return [
            'subcontractor_id' => $this->subcontractor_id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'consent_given_at' => $this->consent_given_at ? $this->consent_given_at : null,
        ];
    }


    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterSubcontractor()
    {
        $this->resetPage();
    }

    public function updatedFilterRole()
    {
        $this->resetPage();
    }


    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedShowDeleted()
    {
        $this->resetPage();
    }

    #[\Livewire\Attributes\On('refresh-contacts')]
    public function refreshContacts()
    {
        // This will trigger a re-render
    }
}