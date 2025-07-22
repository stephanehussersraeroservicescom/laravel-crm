<?php

namespace App\Livewire\DatabaseManager;

use App\Models\ProductRoot;
use App\Models\ProductSeriesMapping;
use Livewire\Component;
use Livewire\WithPagination;

class ProductSeriesTable extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;
    public $editForm = [];

    protected $rules = [
        'editForm.series_code' => 'required|string|max:10',
        'editForm.root_code' => 'required|string|exists:product_roots,root_code',
        'editForm.series_name' => 'required|string|max:255',
        'editForm.has_ink_resist' => 'boolean',
        'editForm.is_bio' => 'boolean',
        'editForm.base_series' => 'nullable|string|max:10',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function edit($seriesId)
    {
        $series = ProductSeriesMapping::findOrFail($seriesId);
        $this->editingId = $seriesId;
        $this->editForm = $series->toArray();
    }

    public function save()
    {
        $this->validate();
        
        $series = ProductSeriesMapping::findOrFail($this->editingId);
        $series->update($this->editForm);
        
        $this->editingId = null;
        $this->editForm = [];
        
        session()->flash('message', 'Product series updated successfully.');
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
            'series_code' => '',
            'root_code' => '',
            'series_name' => '',
            'has_ink_resist' => false,
            'is_bio' => false,
            'base_series' => '',
        ];
    }

    public function store()
    {
        $this->validate();
        
        ProductSeriesMapping::create($this->editForm);
        
        $this->editingId = null;
        $this->editForm = [];
        
        session()->flash('message', 'Product series created successfully.');
    }

    public function delete($seriesId)
    {
        ProductSeriesMapping::findOrFail($seriesId)->delete();
        session()->flash('message', 'Product series deleted successfully.');
    }

    public function render()
    {
        $series = ProductSeriesMapping::with('productRoot')
            ->when($this->search, function ($query) {
                $query->where('series_code', 'like', '%' . $this->search . '%')
                      ->orWhere('series_name', 'like', '%' . $this->search . '%')
                      ->orWhere('root_code', 'like', '%' . $this->search . '%');
            })
            ->orderBy('root_code')
            ->orderBy('series_code')
            ->paginate(15);

        $productRoots = ProductRoot::orderBy('root_code')->get();

        return view('livewire.database-manager.product-series-table', compact('series', 'productRoots'));
    }
}