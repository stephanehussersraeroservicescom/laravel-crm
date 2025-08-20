<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Airline;
use App\Models\Subcontractor;
use App\Models\ExternalCustomer;
use Illuminate\Support\Collection;

class CustomerSelector extends Component
{
    public $search = '';
    public $showDropdown = false;
    public $selectedCustomer = null;
    public $selectedCustomerType = null;
    public $selectedCustomerId = null;
    public $selectedCustomerName = '';
    public $showNewCustomerForm = false;
    
    // New customer form fields
    public $newCustomerName = '';
    public $newCustomerContact = '';
    public $newCustomerEmail = '';
    public $newCustomerPhone = '';
    public $newCustomerAddress = '';
    public $newCustomerPaymentTerms = 'Pro Forma';
    public $newCustomerNotes = '';
    
    // For binding to parent component/form
    public $quoteId = null;

    protected $rules = [
        'newCustomerName' => 'required|string|max:255',
        'newCustomerContact' => 'nullable|string|max:255',
        'newCustomerEmail' => 'nullable|email|max:255',
        'newCustomerPhone' => 'nullable|string|max:50',
        'newCustomerAddress' => 'nullable|string',
        'newCustomerPaymentTerms' => 'required|string|max:50',
        'newCustomerNotes' => 'nullable|string',
    ];

    public function mount($quoteId = null, $customerType = null, $customerId = null, $customerName = null)
    {
        $this->quoteId = $quoteId;
        
        // If editing existing quote, load the customer
        if ($customerType && $customerId) {
            $this->selectedCustomerType = $customerType;
            $this->selectedCustomerId = $customerId;
            $this->selectedCustomerName = $customerName;
            $this->search = $customerName;
            
            // Load the actual customer model
            $model = $customerType::find($customerId);
            if ($model) {
                $this->selectedCustomer = [
                    'id' => $model->id,
                    'name' => $model->name ?? $model->company_name,
                    'type' => $customerType,
                    'type_label' => $this->getTypeLabel($customerType),
                    'model' => $model
                ];
            }
        }
    }

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $this->showDropdown = true;
        } else {
            $this->showDropdown = false;
            $this->resetSelection();
        }
    }

    public function searchCustomers()
    {
        if (strlen($this->search) < 2) {
            return collect();
        }

        $results = collect();
        
        // Search Airlines
        $airlines = Airline::where('name', 'like', '%' . $this->search . '%')
            ->limit(5)
            ->get()
            ->map(function ($airline) {
                return [
                    'id' => $airline->id,
                    'name' => $airline->name,
                    'type' => 'App\\Models\\Airline',
                    'type_label' => 'Airline',
                    'region' => $airline->region,
                    'model' => $airline
                ];
            });
        
        // Search Subcontractors
        $subcontractors = Subcontractor::where('name', 'like', '%' . $this->search . '%')
            ->limit(5)
            ->get()
            ->map(function ($subcontractor) {
                return [
                    'id' => $subcontractor->id,
                    'name' => $subcontractor->name,
                    'type' => 'App\\Models\\Subcontractor',
                    'type_label' => 'Subcontractor',
                    'model' => $subcontractor
                ];
            });
        
        // Search External Customers
        $externalCustomers = ExternalCustomer::search($this->search)
            ->limit(5)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->display_name,
                    'type' => 'App\\Models\\ExternalCustomer',
                    'type_label' => $customer->is_regular ? 'Regular Customer' : 'Ad-hoc Customer',
                    'contact' => $customer->contact_name,
                    'model' => $customer
                ];
            });
        
        $results = $results->merge($airlines)
                          ->merge($subcontractors)
                          ->merge($externalCustomers);
        
        return $results;
    }

    public function selectCustomer($customerId, $customerType)
    {
        $model = $customerType::find($customerId);
        
        if ($model) {
            $this->selectedCustomer = [
                'id' => $model->id,
                'name' => $model->name ?? $model->display_name ?? $model->company_name,
                'type' => $customerType,
                'type_label' => $this->getTypeLabel($customerType),
                'model' => $model
            ];
            
            $this->selectedCustomerType = $customerType;
            $this->selectedCustomerId = $model->id;
            $this->selectedCustomerName = $model->name ?? $model->display_name ?? $model->company_name;
            $this->search = $this->selectedCustomerName;
            $this->showDropdown = false;
            $this->showNewCustomerForm = false;
            
            // Emit event to parent component
            $this->dispatch('customerSelected', [
                'customer_type' => $customerType,
                'customer_id' => $model->id,
                'customer_name' => $this->selectedCustomerName,
            ]);
        }
    }

    public function showNewCustomer()
    {
        $this->showNewCustomerForm = true;
        $this->showDropdown = false;
        $this->newCustomerName = $this->search;
    }

    public function createNewCustomer()
    {
        $this->validate();
        
        $customer = ExternalCustomer::create([
            'name' => $this->newCustomerName,
            'contact_name' => $this->newCustomerContact,
            'email' => $this->newCustomerEmail,
            'phone' => $this->newCustomerPhone,
            'address' => $this->newCustomerAddress,
            'payment_terms' => $this->newCustomerPaymentTerms,
            'notes' => $this->newCustomerNotes,
        ]);
        
        // Select the newly created customer
        $this->selectCustomer($customer->id, 'App\\Models\\ExternalCustomer');
        
        // Reset form
        $this->resetNewCustomerForm();
        
        session()->flash('message', 'New customer created successfully.');
    }

    public function cancelNewCustomer()
    {
        $this->resetNewCustomerForm();
    }

    private function resetNewCustomerForm()
    {
        $this->showNewCustomerForm = false;
        $this->newCustomerName = '';
        $this->newCustomerContact = '';
        $this->newCustomerEmail = '';
        $this->newCustomerPhone = '';
        $this->newCustomerAddress = '';
        $this->newCustomerPaymentTerms = 'Pro Forma';
        $this->newCustomerNotes = '';
    }

    private function resetSelection()
    {
        if (!$this->selectedCustomer) {
            $this->selectedCustomerType = null;
            $this->selectedCustomerId = null;
            $this->selectedCustomerName = '';
        }
    }

    private function getTypeLabel($type)
    {
        return match($type) {
            'App\\Models\\Airline' => 'Airline',
            'App\\Models\\Subcontractor' => 'Subcontractor',
            'App\\Models\\ExternalCustomer' => 'External Customer',
            default => 'Unknown'
        };
    }

    public function render()
    {
        return view('livewire.customer-selector', [
            'customers' => $this->searchCustomers()
        ]);
    }
}