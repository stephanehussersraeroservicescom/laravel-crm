<?php

namespace App\Livewire\Components;

use App\Livewire\Traits\HasProductSearch;
use App\Livewire\Traits\HasPricingCalculations;
use App\Models\QuoteLine;
use App\Models\Quote;
use App\Models\ProductClass;
use App\Models\Product;
use Livewire\Component;

class QuoteLineManager extends Component
{
    use HasProductSearch, HasPricingCalculations;

    public Quote $quote;
    public array $lines = [];
    public array $quantities = [];
    public array $prices = [];
    public array $moqs = [];
    public array $lead_times = [];
    public array $part_numbers = [];
    public array $descriptions = [];
    public array $has_ink_resist = [];
    public array $is_bio = [];

    protected $listeners = [
        'quote-line-added' => 'addLine',
        'quote-line-removed' => 'removeLine',
        'recalculate-totals' => 'calculateTotals',
    ];

    public function mount(Quote $quote)
    {
        $this->quote = $quote;
        $this->loadExistingLines();
        $this->initializeProductSearch(count($this->lines));
    }

    public function loadExistingLines()
    {
        $quoteLines = $this->quote->quoteLines()->orderBy('id')->get();
        
        foreach ($quoteLines as $index => $line) {
            $this->lines[$index] = $line->id;
            $this->quantities[$index] = $line->quantity;
            $this->prices[$index] = $line->price;
            $this->moqs[$index] = $line->moq;
            $this->lead_times[$index] = $line->lead_time;
            $this->part_numbers[$index] = $line->part_number;
            $this->descriptions[$index] = $line->description;
            $this->has_ink_resist[$index] = $line->has_ink_resist;
            $this->is_bio[$index] = $line->is_bio;
            
            // Set selected products/classes
            if ($line->product_id) {
                $this->selected_products[$index] = Product::find($line->product_id);
            } elseif ($line->product_class_id) {
                $this->selected_product_classes[$index] = $line->product_class_id;
            }
        }
    }

    public function addLine()
    {
        $index = count($this->lines);
        $this->lines[$index] = null;
        $this->quantities[$index] = '';
        $this->prices[$index] = '';
        $this->moqs[$index] = 25;
        $this->lead_times[$index] = 16;
        $this->part_numbers[$index] = '';
        $this->descriptions[$index] = '';
        $this->has_ink_resist[$index] = false;
        $this->is_bio[$index] = false;
        
        // Extend product search arrays
        $this->product_searches[$index] = '';
        $this->root_searches[$index] = '';
        $this->filtered_products[$index] = collect();
        $this->filtered_roots[$index] = collect();
        $this->selected_products[$index] = null;
        $this->selected_product_classes[$index] = null;
    }

    public function removeLine($index)
    {
        // If this is an existing line, mark it for deletion
        if (isset($this->lines[$index]) && $this->lines[$index]) {
            QuoteLine::find($this->lines[$index])?->delete();
        }
        
        // Remove from arrays
        unset($this->lines[$index]);
        unset($this->quantities[$index]);
        unset($this->prices[$index]);
        unset($this->moqs[$index]);
        unset($this->lead_times[$index]);
        unset($this->part_numbers[$index]);
        unset($this->descriptions[$index]);
        unset($this->has_ink_resist[$index]);
        unset($this->is_bio[$index]);
        
        // Remove from product search arrays
        unset($this->product_searches[$index]);
        unset($this->root_searches[$index]);
        unset($this->filtered_products[$index]);
        unset($this->filtered_roots[$index]);
        unset($this->selected_products[$index]);
        unset($this->selected_product_classes[$index]);
        
        // Reindex arrays
        $this->lines = array_values($this->lines);
        $this->quantities = array_values($this->quantities);
        $this->prices = array_values($this->prices);
        $this->moqs = array_values($this->moqs);
        $this->lead_times = array_values($this->lead_times);
        $this->part_numbers = array_values($this->part_numbers);
        $this->descriptions = array_values($this->descriptions);
        $this->has_ink_resist = array_values($this->has_ink_resist);
        $this->is_bio = array_values($this->is_bio);
        
        $this->product_searches = array_values($this->product_searches);
        $this->root_searches = array_values($this->root_searches);
        $this->filtered_products = array_values($this->filtered_products);
        $this->filtered_roots = array_values($this->filtered_roots);
        $this->selected_products = array_values($this->selected_products);
        $this->selected_product_classes = array_values($this->selected_product_classes);
        
        $this->dispatch('quote-totals-updated');
    }

    public function saveLines()
    {
        foreach ($this->lines as $index => $lineId) {
            $lineData = [
                'quote_id' => $this->quote->id,
                'product_id' => $this->selected_products[$index]?->id,
                'product_class_id' => $this->selected_product_classes[$index],
                'quantity' => $this->quantities[$index] ?? 0,
                'price' => $this->prices[$index] ?? 0,
                'moq' => $this->moqs[$index] ?? 25,
                'lead_time' => $this->lead_times[$index] ?? 16,
                'part_number' => $this->part_numbers[$index] ?? '',
                'description' => $this->descriptions[$index] ?? '',
                'has_ink_resist' => $this->has_ink_resist[$index] ?? false,
                'is_bio' => $this->is_bio[$index] ?? false,
            ];
            
            if ($lineId) {
                QuoteLine::find($lineId)->update($lineData);
            } else {
                $quoteLine = QuoteLine::create($lineData);
                $this->lines[$index] = $quoteLine->id;
            }
        }
        
        session()->flash('message', 'Quote lines saved successfully!');
        $this->dispatch('quote-lines-saved');
    }

    public function productSelected($index, $productId)
    {
        $this->updatePricingForLine($index);
        $product = Product::find($productId);
        if ($product) {
            $this->part_numbers[$index] = $product->part_number;
            $this->descriptions[$index] = $product->description;
        }
    }

    public function productCleared($index)
    {
        $this->part_numbers[$index] = '';
        $this->descriptions[$index] = '';
        $this->prices[$index] = '';
        $this->moqs[$index] = 25;
        $this->lead_times[$index] = 16;
    }

    public function productClassSelected($index, $rootCode, $hasInkResist = false, $isBio = false)
    {
        $this->updatePricingForLine($index);
        $this->has_ink_resist[$index] = $hasInkResist;
        $this->is_bio[$index] = $isBio;
        
        $productClass = ProductClass::where('code', $rootCode)->first();
        if ($productClass) {
            $this->part_numbers[$index] = $this->generatePartNumber($productClass, $hasInkResist, $isBio);
            $this->descriptions[$index] = $this->generateDescription($productClass, $hasInkResist, $isBio);
        }
    }

    public function productClassCleared($index)
    {
        $this->part_numbers[$index] = '';
        $this->descriptions[$index] = '';
        $this->prices[$index] = '';
        $this->moqs[$index] = 25;
        $this->lead_times[$index] = 16;
        $this->has_ink_resist[$index] = false;
        $this->is_bio[$index] = false;
    }

    private function generatePartNumber($productClass, $hasInkResist, $isBio)
    {
        $partNumber = $productClass->code;
        
        if ($hasInkResist) {
            $partNumber .= '-IR';
        }
        
        if ($isBio) {
            $partNumber .= '-BIO';
        }
        
        return $partNumber;
    }

    private function generateDescription($productClass, $hasInkResist, $isBio)
    {
        $description = $productClass->description;
        
        $addons = [];
        if ($hasInkResist) {
            $addons[] = 'Ink Resistant';
        }
        if ($isBio) {
            $addons[] = 'Bio-based';
        }
        
        if (!empty($addons)) {
            $description .= ' (' . implode(', ', $addons) . ')';
        }
        
        return $description;
    }

    public function calculateTotals()
    {
        $total = $this->calculateQuoteTotal();
        $this->dispatch('quote-total-calculated', ['total' => $total]);
    }

    public function render()
    {
        return view('livewire.components.quote-line-manager');
    }
}