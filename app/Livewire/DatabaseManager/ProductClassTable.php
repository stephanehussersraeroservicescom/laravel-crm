<?php

namespace App\Livewire\DatabaseManager;

use App\Models\ProductClass;
use App\Models\PriceList;
use Livewire\Component;
use Livewire\WithPagination;

class ProductClassTable extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;
    public $editForm = [];
    
    // Price editing
    public $editingPriceId = null;
    public $priceForm = [];
    public $showPriceHistory = false;
    public $historyRootCode = '';
    
    // Bulk price update
    public $selectedProducts = [];
    public $showBulkPriceUpdate = false;
    public $bulkPriceIncrease = '';

    protected $rules = [
        'editForm.root_code' => 'required|string|max:20',
        'editForm.root_name' => 'required|string|max:100',
        'editForm.part_number_prefix' => 'nullable|string|max:255',
        'editForm.description' => 'nullable|string',
        'editForm.has_ink_resist' => 'boolean',
        'editForm.is_bio' => 'boolean',
        'editForm.price' => 'required|numeric|min:0',
        'editForm.moq_ly' => 'required|integer|min:1',
        'editForm.uom' => 'required|in:LY,UNIT',
        'editForm.lead_time_weeks' => 'nullable|string|max:20',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($rootCode)
    {
        $root = ProductClass::where('root_code', $rootCode)->firstOrFail();
        $this->editingId = $rootCode;
        $this->editForm = $root->toArray();
    }

    public function save()
    {
        $this->validate();
        
        if ($this->editingId === 'new') {
            // Creating new product
            ProductClass::create($this->editForm);
            session()->flash('message', 'Product class created successfully.');
        } else {
            // Updating existing product
            $root = ProductClass::where('root_code', $this->editingId)->firstOrFail();
            $root->update($this->editForm);
            session()->flash('message', 'Product class updated successfully.');
        }
        
        $this->editingId = null;
        $this->editForm = [];
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
            'root_code' => '',
            'root_name' => '',
            'part_number_prefix' => '',
            'description' => '',
            'has_ink_resist' => false,
            'is_bio' => false,
            'price' => 0,
            'moq_ly' => 1,
            'uom' => 'LY',
            'lead_time_weeks' => '',
        ];
    }

    public function store()
    {
        $this->validate();
        
        ProductClass::create($this->editForm);
        
        $this->editingId = null;
        $this->editForm = [];
        
        session()->flash('message', 'Product class created successfully.');
    }

    public function delete($rootCode)
    {
        ProductClass::where('root_code', $rootCode)->firstOrFail()->delete();
        session()->flash('message', 'Product class deleted successfully.');
    }
    
    // Price Management Methods
    public function editPrice($rootCode)
    {
        $root = ProductClass::where('root_code', $rootCode)->firstOrFail();
        $currentPrice = $root->priceLists()
            ->where('is_active', true)
            ->current()
            ->first();
            
        $this->editingPriceId = $rootCode;
        $this->priceForm = [
            'root_code' => $rootCode,
            'price_ly' => $currentPrice ? $currentPrice->price_ly : '',
            'moq_ly' => $currentPrice ? $currentPrice->moq_ly : $root->moq_ly,
        ];
    }
    
    public function savePrice()
    {
        $this->validate([
            'priceForm.price_ly' => 'required|numeric|min:0',
            'priceForm.moq_ly' => 'required|integer|min:1',
        ]);
        
        // Get current price to archive it
        $currentPrice = PriceList::where('root_code', $this->priceForm['root_code'])
            ->where('is_active', true)
            ->current()
            ->first();
            
        // If there's a current price and it's different, deactivate it
        if ($currentPrice && $currentPrice->price_ly != $this->priceForm['price_ly']) {
            $currentPrice->update([
                'is_active' => false,
                'expiry_date' => now()
            ]);
        }
        
        // Create new price entry (only if different or no current price)
        if (!$currentPrice || $currentPrice->price_ly != $this->priceForm['price_ly']) {
            PriceList::create([
                'list_type' => 'standard', // Single default price type
                'root_code' => $this->priceForm['root_code'],
                'price_ly' => $this->priceForm['price_ly'],
                'moq_ly' => $this->priceForm['moq_ly'],
                'effective_date' => now(),
                'is_active' => true,
                'imported_from' => 'inline_edit'
            ]);
        }
        
        $this->cancelPrice();
        session()->flash('message', 'Price updated successfully.');
    }
    
    public function cancelPrice()
    {
        $this->editingPriceId = null;
        $this->priceForm = [];
    }
    
    public function showHistory($rootCode)
    {
        $this->historyRootCode = $rootCode;
        $this->showPriceHistory = true;
    }
    
    public function closeHistory()
    {
        $this->showPriceHistory = false;
        $this->historyRootCode = '';
    }
    
    public function toggleProductSelection($rootCode)
    {
        if (in_array($rootCode, $this->selectedProducts)) {
            $this->selectedProducts = array_filter($this->selectedProducts, fn($id) => $id !== $rootCode);
        } else {
            $this->selectedProducts[] = $rootCode;
        }
    }
    
    public function showBulkUpdate()
    {
        if (empty($this->selectedProducts)) {
            session()->flash('error', 'Please select products to update prices.');
            return;
        }
        $this->showBulkPriceUpdate = true;
    }
    
    public function applyBulkPriceIncrease()
    {
        $this->validate([
            'bulkPriceIncrease' => 'required|numeric|min:-100|max:1000'
        ]);
        
        $multiplier = (100 + (float)$this->bulkPriceIncrease) / 100;
        $updated = 0;
        
        foreach ($this->selectedProducts as $rootCode) {
            $currentPrices = PriceList::where('root_code', $rootCode)
                ->where('is_active', true)
                ->current()
                ->get();
                
            foreach ($currentPrices as $price) {
                $newPrice = round($price->price_ly * $multiplier, 2);
                
                // Archive old price
                $price->update([
                    'is_active' => false,
                    'expiry_date' => now()
                ]);
                
                // Create new price
                PriceList::create([
                    'list_type' => $price->list_type,
                    'root_code' => $price->root_code,
                    'price_ly' => $newPrice,
                    'moq_ly' => $price->moq_ly,
                    'effective_date' => now(),
                    'is_active' => true,
                    'imported_from' => 'bulk_update_' . $this->bulkPriceIncrease . '%'
                ]);
                $updated++;
            }
        }
        
        $this->selectedProducts = [];
        $this->showBulkPriceUpdate = false;
        $this->bulkPriceIncrease = '';
        
        session()->flash('message', "Applied price increase to {$updated} price entries.");
    }
    
    public function cancelBulkUpdate()
    {
        $this->showBulkPriceUpdate = false;
        $this->bulkPriceIncrease = '';
    }
    
    public function toggleSelectAll()
    {
        // Get current page root codes
        $currentPageIds = ProductClass::when($this->search, function ($query) {
                $query->where('root_code', 'like', '%' . $this->search . '%')
                      ->orWhere('root_name', 'like', '%' . $this->search . '%')
                      ->orWhere('category', 'like', '%' . $this->search . '%');
            })
            ->orderBy('root_code')
            ->paginate(15)
            ->pluck('root_code')
            ->toArray();
        
        if (count(array_intersect($currentPageIds, $this->selectedProducts)) === count($currentPageIds)) {
            // All items on current page are selected, so deselect them
            $this->selectedProducts = array_diff($this->selectedProducts, $currentPageIds);
        } else {
            // Select all items on current page
            $this->selectedProducts = array_unique(array_merge($this->selectedProducts, $currentPageIds));
        }
    }

    public function render()
    {
        $roots = ProductClass::with(['priceLists' => function ($query) {
                $query->where('is_active', true)->current();
            }])
            ->when($this->search, function ($query) {
                $query->where('root_code', 'like', '%' . $this->search . '%')
                      ->orWhere('root_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('root_code')
            ->paginate(15);

        $priceHistory = [];
        if ($this->showPriceHistory && $this->historyRootCode) {
            $priceHistory = PriceList::where('root_code', $this->historyRootCode)
                ->orderBy('effective_date', 'desc')
                ->get();
        }

        return view('livewire.database-manager.product-class-table', compact('roots', 'priceHistory'));
    }
}