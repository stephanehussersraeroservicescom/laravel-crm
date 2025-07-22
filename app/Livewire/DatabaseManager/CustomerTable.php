<?php

namespace App\Livewire\DatabaseManager;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerTable extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;
    public $editForm = [];

    protected $rules = [
        'editForm.company_name' => 'required|string|max:255',
        'editForm.contact_name' => 'required|string|max:255',
        'editForm.email' => 'nullable|email|max:255',
        'editForm.phone' => 'nullable|string|max:255',
        'editForm.address' => 'nullable|string',
        'editForm.billing_address' => 'nullable|string',
        'editForm.shipping_address' => 'nullable|string',
        'editForm.tax_id' => 'nullable|string|max:255',
        'editForm.payment_terms' => 'nullable|string|max:255',
        'editForm.is_subcontractor' => 'boolean',
        'editForm.has_blanket_po' => 'boolean',
        'editForm.credit_limit' => 'nullable|numeric|min:0',
        'editForm.account_manager' => 'nullable|string|max:255',
        'editForm.notes' => 'nullable|string',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $this->editingId = $customerId;
        $this->editForm = $customer->toArray();
    }

    public function save()
    {
        $this->validate();
        
        $customer = Customer::findOrFail($this->editingId);
        $customer->update($this->editForm);
        
        $this->editingId = null;
        $this->editForm = [];
        
        session()->flash('message', 'Customer updated successfully.');
    }

    public function cancel()
    {
        $this->editingId = null;
        $this->editForm = [];
    }

    public function create()
    {
        $this->editingId = 'new';
        $this->editForm = [
            'company_name' => '',
            'contact_name' => '',
            'email' => '',
            'phone' => '',
            'address' => '',
            'billing_address' => '',
            'shipping_address' => '',
            'tax_id' => '',
            'payment_terms' => '',
            'is_subcontractor' => false,
            'has_blanket_po' => false,
            'credit_limit' => null,
            'account_manager' => '',
            'notes' => '',
        ];
    }

    public function store()
    {
        $this->validate();
        
        Customer::create($this->editForm);
        
        $this->editingId = null;
        $this->editForm = [];
        
        session()->flash('message', 'Customer created successfully.');
    }

    public function delete($customerId)
    {
        Customer::findOrFail($customerId)->delete();
        session()->flash('message', 'Customer deleted successfully.');
    }

    public function render()
    {
        $customers = Customer::query()
            ->when($this->search, function ($query) {
                $query->where('company_name', 'like', '%' . $this->search . '%')
                      ->orWhere('contact_name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('company_name')
            ->paginate(15);

        return view('livewire.database-manager.customer-table', compact('customers'));
    }
}