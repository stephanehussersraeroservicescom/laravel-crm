<?php

namespace App\Livewire;

use App\Livewire\Traits\HasProductSearch;
use App\Livewire\Traits\HasPricingCalculations;
use App\Models\QuoteLine;
use App\Models\ProductClass;
use App\Models\Product;
use Livewire\Component;

class QuoteLineManager extends Component
{
    use HasProductSearch, HasPricingCalculations;

    public $quote;
    public $lines = [];
    
    // Line data arrays
    public $quantities = [];
    public $prices = [];
    public $moqs = [];
    public $lead_times = [];
    public $part_numbers = [];
    public $descriptions = [];
    public $uoms = [];
    public $moq_waived = [];
    
    // Customer context for pricing
    public $customer_type;
    public $customer_id;

    protected $listeners = [
        'quote-saved' => 'refreshLines',
        'customer-selected' => 'updateCustomerContext',
    ];

    public function mount($quote = null, $lineCount = 10)
    {
        $this->quote = $quote;
        $this->initializeLines($lineCount);
        
        if ($quote) {
            $this->loadExistingLines();
        }
    }

    public function initializeLines($count)
    {
        $this->initializeProductSearch($count);
        
        // Initialize line data arrays
        for ($i = 0; $i < $count; $i++) {
            $this->quantities[$i] = '';
            $this->prices[$i] = '';
            $this->moqs[$i] = '';
            $this->lead_times[$i] = '';
            $this->part_numbers[$i] = '';
            $this->descriptions[$i] = '';
            $this->uoms[$i] = 'LY';
            $this->moq_waived[$i] = false;
        }
    }

    public function loadExistingLines()
    {
        $lines = $this->quote->lines()->orderBy('line_number')->get();
        
        foreach ($lines as $index => $line) {
            $this->quantities[$index] = $line->quantity;
            $this->prices[$index] = $line->price;
            $this->moqs[$index] = $line->moq;
            $this->lead_times[$index] = $line->lead_time;
            $this->part_numbers[$index] = $line->part_number;
            $this->descriptions[$index] = $line->description;
            $this->uoms[$index] = $line->uom ?? 'LY';
            $this->moq_waived[$index] = $line->moq_waived ?? false;
            
            // Set selected products/classes
            if ($line->product_id) {
                $this->selected_products[$index] = Product::find($line->product_id);
                $this->product_searches[$index] = $this->selected_products[$index]->part_number ?? '';
            } elseif ($line->product_class_id) {
                $this->selected_product_classes[$index] = $line->product_class_id;
                $productClass = ProductClass::find($line->product_class_id);
                $this->root_searches[$index] = $productClass->code ?? '';
            }
        }
    }

    public function updateCustomerContext($customerType, $customerId)
    {
        $this->customer_type = $customerType;
        $this->customer_id = $customerId;
        
        // Update pricing for all lines
        foreach ($this->lines as $index => $line) {
            $this->updatePricingForLine($index);
        }
    }

    public function productSelected($index, $productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $this->part_numbers[$index] = $product->part_number;
            $this->descriptions[$index] = $product->description;
            $this->uoms[$index] = $product->uom ?? 'LY';
            
            // Clear product class selection
            $this->selected_product_classes[$index] = null;
            $this->root_searches[$index] = '';
            
            $this->updatePricingForLine($index);
        }
    }

    public function productCleared($index)
    {
        $this->part_numbers[$index] = '';
        $this->descriptions[$index] = '';
        $this->prices[$index] = '';
        $this->moqs[$index] = '';
        $this->lead_times[$index] = '';
        $this->uoms[$index] = 'LY';
    }

    public function productClassSelected($index, $rootCode, $hasInkResist = false, $isBio = false)
    {
        $productClass = ProductClass::where('code', $rootCode)->first();
        if ($productClass) {
            $this->part_numbers[$index] = $rootCode;
            $this->descriptions[$index] = $productClass->description;
            $this->uoms[$index] = $productClass->uom ?? 'LY';
            
            // Clear individual product selection
            $this->selected_products[$index] = null;
            $this->product_searches[$index] = '';
            
            $this->updatePricingForLine($index);
        }
    }

    public function productClassCleared($index)
    {
        $this->productCleared($index);
    }

    public function checkMOQ($index)
    {
        if ($this->checkMOQRequirement($index)) {
            $this->dispatch('show-moq-warning', $index);
        }
    }

    public function confirmMOQWaiver($index)
    {
        $this->moq_waived[$index] = true;
    }

    public function saveLines()
    {
        if (!$this->quote) {
            return;
        }

        // Delete existing lines
        $this->quote->lines()->delete();
        
        // Save new lines
        foreach ($this->quantities as $index => $quantity) {
            if (empty($quantity) || empty($this->part_numbers[$index])) {
                continue;
            }
            
            QuoteLine::create([
                'quote_id' => $this->quote->id,
                'line_number' => $index + 1,
                'part_number' => $this->part_numbers[$index],
                'description' => $this->descriptions[$index],
                'quantity' => $quantity,
                'uom' => $this->uoms[$index],
                'price' => $this->prices[$index] ?: 0,
                'moq' => $this->moqs[$index] ?: 0,
                'lead_time' => $this->lead_times[$index] ?: 0,
                'moq_waived' => $this->moq_waived[$index] ?? false,
                'product_id' => $this->selected_products[$index]->id ?? null,
                'product_class_id' => $this->selected_product_classes[$index] ?? null,
            ]);
        }
        
        $this->dispatch('lines-saved');
    }

    public function getQuoteTotalProperty()
    {
        return $this->calculateQuoteTotal();
    }

    public function render()
    {
        return view('livewire.quote-line-manager');
    }
}