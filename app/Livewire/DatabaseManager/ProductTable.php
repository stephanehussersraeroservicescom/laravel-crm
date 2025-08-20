<?php

namespace App\Livewire\DatabaseManager;

use App\Models\Product;
use App\Models\ProductClass;
use Livewire\Component;
use Livewire\WithPagination;

class ProductTable extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;
    public $editForm = [];
    public $rootCodeFilter = '';

    protected $rules = [
        'editForm.part_number' => 'required|string|max:255',
        'editForm.root_code' => 'required|string|max:20|exists:product_classes,root_code',
        'editForm.color_name' => 'required|string|max:255',
        'editForm.color_code' => 'nullable|string|max:10',
        'editForm.description' => 'nullable|string',
        'editForm.price' => 'required|numeric|min:0',
        'editForm.moq' => 'required|integer|min:1',
        'editForm.uom' => 'required|in:LY,UNIT',
        'editForm.lead_time_weeks' => 'nullable|string|max:20',
        'editForm.is_active' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRootCodeFilter()
    {
        $this->resetPage();
    }

    public function edit($productId)
    {
        $product = Product::findOrFail($productId);
        $this->editingId = $productId;
        $this->editForm = $product->toArray();
    }

    public function save()
    {
        $this->validate();
        
        // Validate part number prefix matches product class
        if (!empty($this->editForm['root_code'])) {
            $productClass = ProductClass::where('root_code', $this->editForm['root_code'])->first();
            if ($productClass) {
                $prefix = $productClass->part_number_prefix ?? $productClass->root_code;
                if (!str_starts_with($this->editForm['part_number'], $prefix)) {
                    $this->addError('editForm.part_number', "Part number must start with '{$prefix}' for product class {$productClass->root_code}.");
                    return;
                }
            }
        }
        
        if ($this->editingId === 'new') {
            // Check if part number already exists
            if (Product::where('part_number', $this->editForm['part_number'])->exists()) {
                $this->addError('editForm.part_number', 'This part number already exists.');
                return;
            }
            
            Product::create($this->editForm);
            session()->flash('message', 'Product created successfully.');
        } else {
            $product = Product::findOrFail($this->editingId);
            
            // Check if part number already exists (excluding current product)
            if (Product::where('part_number', $this->editForm['part_number'])
                      ->where('id', '!=', $this->editingId)
                      ->exists()) {
                $this->addError('editForm.part_number', 'This part number already exists.');
                return;
            }
            
            $product->update($this->editForm);
            session()->flash('message', 'Product updated successfully.');
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
            'part_number' => '',
            'root_code' => '',
            'color_name' => '',
            'color_code' => '',
            'description' => '',
            'price' => 0,
            'moq' => 1,
            'uom' => 'LY',
            'lead_time_weeks' => '',
            'is_active' => true,
        ];
    }

    public function updatedEditFormRootCode($value)
    {
        if ($value) {
            // Auto-fill fields from product class
            $productClass = ProductClass::where('root_code', $value)->first();
            if ($productClass) {
                // Only auto-fill if creating new product or if fields are empty/default
                if ($this->editingId === 'new' || $this->editForm['price'] == 0) {
                    $this->editForm['price'] = $productClass->price ?? 0;
                }
                if ($this->editingId === 'new' || $this->editForm['moq'] == 1) {
                    $this->editForm['moq'] = $productClass->moq_ly ?? 1;
                }
                if ($this->editingId === 'new' || empty($this->editForm['uom'])) {
                    $this->editForm['uom'] = $productClass->uom ?? 'LY';
                }
                if ($this->editingId === 'new' || empty($this->editForm['lead_time_weeks'])) {
                    $this->editForm['lead_time_weeks'] = $productClass->lead_time_weeks ?? '';
                }
                
                // If part number is empty or doesn't start with prefix, set prefix
                $prefix = $productClass->part_number_prefix ?? $productClass->root_code;
                if (empty($this->editForm['part_number']) || !str_starts_with($this->editForm['part_number'], $prefix)) {
                    $this->editForm['part_number'] = $prefix;
                }
            }
        }
    }

    public function delete($productId)
    {
        $product = Product::findOrFail($productId);
        
        // Check if product is used in quotes
        if ($product->quoteLines()->count() > 0) {
            session()->flash('error', 'Cannot delete product that has been used in quotes.');
            return;
        }
        
        $product->delete();
        session()->flash('message', 'Product deleted successfully.');
    }

    public function toggleStatus($productId)
    {
        $product = Product::findOrFail($productId);
        $product->update(['is_active' => !$product->is_active]);
        
        $status = $product->is_active ? 'activated' : 'deactivated';
        session()->flash('message', "Product {$status} successfully.");
    }

    public function render()
    {
        $query = Product::with(['productClass'])
            ->when($this->search, function ($q) {
                $q->search($this->search);
            })
            ->when($this->rootCodeFilter, function ($q) {
                $q->where('root_code', $this->rootCodeFilter);
            })
            ->orderBy('part_number');

        $products = $query->paginate(15);
        
        // Get product classes for filters and create form
        $productClasses = ProductClass::orderBy('root_code')->get();

        return view('livewire.database-manager.product-table', compact('products', 'productClasses'));
    }
}