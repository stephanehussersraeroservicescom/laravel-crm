<?php

namespace App\Livewire\Traits;

use App\Models\Product;
use App\Models\ProductClass;

trait HasProductSearch
{
    public $product_searches = [];
    public $root_searches = [];
    public $filtered_products = [];
    public $filtered_roots = [];
    public $selected_products = [];
    public $selected_product_classes = [];

    public function initializeProductSearch($lineCount = 10)
    {
        $this->product_searches = array_fill(0, $lineCount, '');
        $this->root_searches = array_fill(0, $lineCount, '');
        $this->filtered_products = array_fill(0, $lineCount, collect());
        $this->filtered_roots = array_fill(0, $lineCount, collect());
        $this->selected_products = array_fill(0, $lineCount, null);
        $this->selected_product_classes = array_fill(0, $lineCount, null);
    }

    public function updatedProductSearches($value, $index)
    {
        if (strlen($value) >= 2) {
            $this->filtered_products[$index] = Product::where('part_number', 'like', '%' . $value . '%')
                ->orWhere('description', 'like', '%' . $value . '%')
                ->limit(10)
                ->get();
        } else {
            $this->filtered_products[$index] = collect();
        }
    }

    public function updatedRootSearches($value, $index)
    {
        if (strlen($value) >= 2) {
            $this->filtered_roots[$index] = ProductClass::where('code', 'like', '%' . $value . '%')
                ->orWhere('description', 'like', '%' . $value . '%')
                ->limit(10)
                ->get();
        } else {
            $this->filtered_roots[$index] = collect();
        }
    }

    public function selectProduct($index, $productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $this->selected_products[$index] = $product;
            $this->product_searches[$index] = $product->part_number;
            $this->filtered_products[$index] = collect();
            
            // Trigger product selection event
            $this->productSelected($index, $productId);
        }
    }

    public function selectRoot($index, $rootCode, $hasInkResist = false, $isBio = false)
    {
        $root = ProductClass::where('code', $rootCode)->first();
        if ($root) {
            $this->selected_product_classes[$index] = $root->id;
            $this->root_searches[$index] = $root->code;
            $this->filtered_roots[$index] = collect();
            
            // Trigger product class selection event
            $this->productClassSelected($index, $rootCode, $hasInkResist, $isBio);
        }
    }

    public function clearProduct($index)
    {
        $this->selected_products[$index] = null;
        $this->product_searches[$index] = '';
        $this->filtered_products[$index] = collect();
        
        // Trigger product cleared event
        $this->productCleared($index);
    }

    public function clearRoot($index)
    {
        $this->selected_product_classes[$index] = null;
        $this->root_searches[$index] = '';
        $this->filtered_roots[$index] = collect();
        
        // Trigger product class cleared event
        $this->productClassCleared($index);
    }

    // Abstract methods that should be implemented by the using class
    abstract public function productSelected($index, $productId);
    abstract public function productCleared($index);
    abstract public function productClassSelected($index, $rootCode, $hasInkResist = false, $isBio = false);
    abstract public function productClassCleared($index);
}