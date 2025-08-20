<?php

namespace App\Livewire\DatabaseManager;

use App\Models\Airline;
use App\Models\ContractPrice;
use App\Models\ProductClass;
use Livewire\Component;
use Livewire\WithPagination;

class ContractPriceTable extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;
    public $editForm = [];
    public $filterAirline = '';

    protected $rules = [
        'editForm.contract_price' => 'required|numeric|min:0',
        'editForm.customer_identifier' => 'required_without:editForm.airline_id|nullable|string|max:255',
        'editForm.airline_id' => 'required_without:editForm.customer_identifier|nullable|exists:airlines,id',
        'editForm.part_number' => 'nullable|string|max:255',
        'editForm.root_code' => 'nullable|exists:product_roots,root_code',
        'editForm.notes' => 'nullable|string|max:1000',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterAirline()
    {
        $this->resetPage();
    }

    public function edit($contractId)
    {
        $contract = ContractPrice::findOrFail($contractId);
        $this->editingId = $contractId;
        $this->editForm = $contract->toArray();
        
        // Convert price from cents to dollars for display
        $this->editForm['contract_price'] = $this->editForm['contract_price'] / 100;
        
        // Convert date objects to strings for form binding (keep these for edit mode)
        if ($this->editForm['valid_from']) {
            $this->editForm['valid_from'] = $contract->valid_from->format('Y-m-d');
        }
        if ($this->editForm['valid_to']) {
            $this->editForm['valid_to'] = $contract->valid_to->format('Y-m-d');
        }
    }

    public function save()
    {
        $this->validate();
        
        // Convert price to cents for storage
        $this->editForm['contract_price'] = $this->editForm['contract_price'] * 100;
        
        $contract = ContractPrice::findOrFail($this->editingId);
        $contract->update($this->editForm);
        
        $this->editingId = null;
        $this->editForm = [];
        
        session()->flash('message', 'Contract price updated successfully.');
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
            'customer_identifier' => '',
            'part_number' => '',
            'root_code' => '',
            'airline_id' => '',
            'contract_price' => '',
            'notes' => '',
        ];
    }

    public function store()
    {
        $this->validate();
        
        // Convert price to cents for storage
        $this->editForm['contract_price'] = $this->editForm['contract_price'] * 100;
        
        // Set automatic dates
        $this->editForm['valid_from'] = now()->toDateString();
        // valid_to is null by default (no expiration until new price is entered)
        
        // Terminate any existing active contracts for the same party and scope
        $this->terminateExistingContracts();
        
        ContractPrice::create($this->editForm);
        
        $this->editingId = null;
        $this->editForm = [];
        
        session()->flash('message', 'Contract price created successfully. Previous prices have been terminated.');
    }
    
    private function terminateExistingContracts()
    {
        $query = ContractPrice::whereNull('valid_to'); // Only active contracts
        
        // Match same customer or airline
        if ($this->editForm['customer_identifier']) {
            $query->where('customer_identifier', $this->editForm['customer_identifier']);
        } elseif ($this->editForm['airline_id']) {
            $query->where('airline_id', $this->editForm['airline_id']);
        }
        
        // Match same scope (part number or root code)
        if ($this->editForm['part_number']) {
            $query->where('part_number', $this->editForm['part_number']);
        } elseif ($this->editForm['root_code']) {
            $query->where('root_code', $this->editForm['root_code'])->whereNull('part_number');
        } else {
            // Company-wide contract
            $query->whereNull('root_code')->whereNull('part_number');
        }
        
        // Terminate these contracts by setting valid_to to today
        $query->update(['valid_to' => now()->subDay()->toDateString()]);
    }

    public function delete($contractId)
    {
        ContractPrice::findOrFail($contractId)->delete();
        session()->flash('message', 'Contract price deleted successfully.');
    }

    public function render()
    {
        $contracts = ContractPrice::with(['airline'])
            ->when($this->search, function ($query) {
                $query->where('customer_identifier', 'like', '%' . $this->search . '%')
                      ->orWhere('part_number', 'like', '%' . $this->search . '%')
                      ->orWhere('root_code', 'like', '%' . $this->search . '%')
                      ->orWhere('contract_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('airline', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->when($this->filterAirline, function ($query) {
                $query->where('airline_id', $this->filterAirline);
            })
            ->orderBy('valid_from', 'desc')
            ->orderBy('customer_identifier')
            ->paginate(15);

        $airlines = Airline::orderBy('name')->get();
        $productRoots = ProductClass::orderBy('root_code')->get();

        return view('livewire.database-manager.contract-price-table', compact('contracts', 'airlines', 'productRoots'));
    }
}