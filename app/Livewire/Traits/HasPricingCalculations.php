<?php

namespace App\Livewire\Traits;

use App\Models\ContractPrice;
use App\Models\ProductClass;
use App\Models\Product;

trait HasPricingCalculations
{
    protected function getRootPricing(ProductClass $root, $partNumber = null)
    {
        // First check for contract pricing
        $contractPrice = ContractPrice::where('product_class_id', $root->id)
            ->where('customer_type', $this->customer_type)
            ->where('customer_id', $this->customer_id)
            ->first();

        if ($contractPrice) {
            return [
                'price' => $contractPrice->price_per_linear_yard,
                'source' => 'contract',
                'moq' => $contractPrice->moq ?? $root->moq ?? 25,
                'lead_time' => $contractPrice->lead_time ?? $root->lead_time_weeks ?? 16,
            ];
        }

        // If no contract price, use standard pricing from price lists
        return [
            'price' => $root->price_per_linear_yard ?? 0,
            'source' => 'standard',
            'moq' => $root->moq ?? 25,
            'lead_time' => $root->lead_time_weeks ?? 16,
        ];
    }

    protected function getProductPricing(Product $product)
    {
        // Check for contract pricing on specific product
        $contractPrice = ContractPrice::where('product_id', $product->id)
            ->where('customer_type', $this->customer_type)
            ->where('customer_id', $this->customer_id)
            ->first();

        if ($contractPrice) {
            return [
                'price' => $contractPrice->price_per_linear_yard,
                'source' => 'contract',
                'moq' => $contractPrice->moq ?? $product->moq ?? 25,
                'lead_time' => $contractPrice->lead_time ?? $product->lead_time_weeks ?? 16,
            ];
        }

        // Use product standard pricing
        return [
            'price' => $product->price_per_linear_yard ?? 0,
            'source' => 'standard',
            'moq' => $product->moq ?? 25,
            'lead_time' => $product->lead_time_weeks ?? 16,
        ];
    }

    protected function calculateLineTotal($index)
    {
        $quantity = (float) ($this->quantities[$index] ?? 0);
        $price = (float) ($this->prices[$index] ?? 0);
        
        return round($quantity * $price, 2);
    }

    protected function calculateQuoteTotal()
    {
        $total = 0;
        
        foreach ($this->quantities as $index => $quantity) {
            $total += $this->calculateLineTotal($index);
        }
        
        return round($total, 2);
    }

    protected function checkMOQRequirement($index)
    {
        $quantity = (float) ($this->quantities[$index] ?? 0);
        $moq = (float) ($this->moqs[$index] ?? 0);
        
        return $quantity < $moq && $moq > 0;
    }

    protected function updatePricingForLine($index)
    {
        if (isset($this->selected_products[$index]) && $this->selected_products[$index]) {
            $pricing = $this->getProductPricing($this->selected_products[$index]);
        } elseif (isset($this->selected_product_classes[$index]) && $this->selected_product_classes[$index]) {
            $productClass = ProductClass::find($this->selected_product_classes[$index]);
            if ($productClass) {
                $pricing = $this->getRootPricing($productClass);
            }
        } else {
            return;
        }

        $this->prices[$index] = $pricing['price'];
        $this->moqs[$index] = $pricing['moq'];
        $this->lead_times[$index] = $pricing['lead_time'];
    }
}