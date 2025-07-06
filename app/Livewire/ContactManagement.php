<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Contact;
use App\Models\Subcontractor;
use Livewire\Attributes\Validate;

class ContactManagement extends Component
{
    use WithPagination;

    // Search and Filter Properties
    public $search = '';
    public $filterSubcontractor = '';
    public $filterRole = '';
    public $filterMarketingConsent = '';
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
    
    #[Validate('nullable|string|max:255')]
    public $role = '';
    
    #[Validate('nullable|string|max:20')]
    public $phone = '';
    
    #[Validate('boolean')]
    public $marketing_consent = false;
    
    #[Validate('nullable|date')]
    public $consent_given_at = '';

    public function render()
    {
        $contacts = $this->getContacts();
        $subcontractors = Subcontractor::orderBy('name')->get();

        return view('livewire.contact-management', [
            'contacts' => $contacts,
            'subcontractors' => $subcontractors,
            'roles' => $this->getAvailableRoles(),
        ]);
    }

    public function getContacts()
    {
        $query = Contact::with('subcontractor');

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
            $query->where('role', 'like', '%' . $this->filterRole . '%');
        }

        if ($this->filterMarketingConsent !== '') {
            $query->where('marketing_consent', $this->filterMarketingConsent);
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
        $this->filterMarketingConsent = '';
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
            // Set consent_given_at if marketing consent is true
            if ($this->marketing_consent && !$this->consent_given_at) {
                $data['consent_given_at'] = now();
            }
            
            Contact::create($data);
            session()->flash('message', 'Contact created successfully.');
        } else {
            // Handle consent timestamp logic
            if ($this->marketing_consent && !$this->selectedContact->consent_given_at) {
                $data['consent_given_at'] = now();
            } elseif (!$this->marketing_consent) {
                $data['consent_given_at'] = null;
            }
            
            $this->selectedContact->update($data);
            session()->flash('message', 'Contact updated successfully.');
        }

        $this->closeModal();
    }

    public function delete($contactId)
    {
        $contact = Contact::findOrFail($contactId);
        $contact->delete();
        session()->flash('message', 'Contact deleted successfully.');
    }

    public function toggleMarketingConsent($contactId)
    {
        $contact = Contact::findOrFail($contactId);
        $contact->marketing_consent = !$contact->marketing_consent;
        
        if ($contact->marketing_consent) {
            $contact->consent_given_at = now();
        } else {
            $contact->consent_given_at = null;
        }
        
        $contact->save();
        
        session()->flash('message', 'Marketing consent updated successfully.');
    }

    private function resetForm()
    {
        $this->subcontractor_id = '';
        $this->name = '';
        $this->email = '';
        $this->role = '';
        $this->phone = '';
        $this->marketing_consent = false;
        $this->consent_given_at = '';
    }

    private function fillForm($contact)
    {
        $this->subcontractor_id = $contact->subcontractor_id;
        $this->name = $contact->name;
        $this->email = $contact->email;
        $this->role = $contact->role;
        $this->phone = $contact->phone;
        $this->marketing_consent = $contact->marketing_consent;
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
            'marketing_consent' => $this->marketing_consent,
            'consent_given_at' => $this->consent_given_at ? $this->consent_given_at : null,
        ];
    }

    private function getAvailableRoles()
    {
        return [
            'Project Manager',
            'Lead Engineer',
            'Sales Manager',
            'Technical Lead',
            'Quality Manager',
            'Production Manager',
            'Account Manager',
            'Design Engineer',
            'Manufacturing Engineer',
            'Certification Specialist',
            'Business Development',
            'Operations Manager',
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

    public function updatedFilterMarketingConsent()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}