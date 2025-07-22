<?php

namespace App\Livewire\DatabaseManager;

use App\Models\ProductRoot;
use App\Models\StockedProduct;
use Livewire\Component;
use Livewire\WithPagination;

class StockedProductTable extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;
    public $editForm = [];

    protected $rules = [
        'editForm.full_part_number' => 'required|string|max:255',
        'editForm.root_code' => 'required|string|exists:product_roots,root_code',
        'editForm.is_exotic' => 'boolean',
        'editForm.notes' => 'nullable|string',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function edit($stockedProductId)
    {
        $product = StockedProduct::findOrFail($stockedProductId);
        $this->editingId = $stockedProductId;
        $this->editForm = $product->toArray();
    }

    public function save()
    {
        $this->validate();
        
        $product = StockedProduct::findOrFail($this->editingId);
        $product->update($this->editForm);
        
        $this->editingId = null;
        $this->editForm = [];
        
        session()->flash('message', 'Stocked product updated successfully.');
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
            'full_part_number' => '',
            'root_code' => '',
            'is_exotic' => false,
            'notes' => '',
        ];
    }

    public function store()
    {
        $this->validate();
        
        StockedProduct::create($this->editForm);
        
        $this->editingId = null;
        $this->editForm = [];
        
        session()->flash('message', 'Stocked product created successfully.');
    }

    public function delete($stockedProductId)
    {
        StockedProduct::findOrFail($stockedProductId)->delete();
        session()->flash('message', 'Stocked product deleted successfully.');
    }

    public function render()
    {
        $products = StockedProduct::with('productRoot')
            ->when($this->search, function ($query) {
                $query->where('full_part_number', 'like', '%' . $this->search . '%')
                      ->orWhere('root_code', 'like', '%' . $this->search . '%')
                      ->orWhereHas('productRoot', function ($q) {
                          $q->where('root_name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('full_part_number')
            ->paginate(15);

        $productRoots = ProductRoot::orderBy('root_code')->get();

        return view('livewire.database-manager.stocked-product-table', compact('products', 'productRoots'));
    }
}