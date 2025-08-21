<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\User;
use App\Models\Airline;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuoteLineTest extends TestCase
{
    use RefreshDatabase;

    protected Quote $quote;

    protected function setUp(): void
    {
        parent::setUp();
        
        $user = User::factory()->create();
        $airline = Airline::create(['name' => 'Test Airlines', 'code' => 'TA']);
        
        $this->quote = Quote::create([
            'user_id' => $user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $airline->id,
            'customer_name' => $airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);
    }

    public function test_quote_line_can_be_created()
    {
        $quoteLine = QuoteLine::create([
            'quote_id' => $this->quote->id,
            'part_number' => 'ULFR-3901',
            'description' => 'Ultraleather - Navy Blue',
            'quantity' => 100,
            'unit' => 'LY',
            'standard_price' => 9625, // $96.25 in cents
            'final_price' => 9625,
            'moq' => 5,
            'lead_time' => '6-8 weeks',
        ]);

        $this->assertDatabaseHas('quote_lines', [
            'quote_id' => $this->quote->id,
            'part_number' => 'ULFR-3901',
            'quantity' => 100,
        ]);
        
        $this->assertEquals('ULFR-3901', $quoteLine->part_number);
        $this->assertEquals(100, $quoteLine->quantity);
        $this->assertEquals(9625, $quoteLine->final_price);
    }

    public function test_quote_line_calculates_line_total()
    {
        $quoteLine = QuoteLine::create([
            'quote_id' => $this->quote->id,
            'part_number' => 'ULFR-3901',
            'description' => 'Ultraleather - Navy Blue',
            'quantity' => 50,
            'unit' => 'LY',
            'standard_price' => 10000, // $100.00 in cents
            'final_price' => 9500, // $95.00 in cents (5% discount)
            'moq' => 5,
            'lead_time' => '6-8 weeks',
        ]);

        $lineTotal = $quoteLine->quantity * $quoteLine->final_price;
        $this->assertEquals(475000, $lineTotal); // 50 * $95.00 = $4,750.00 in cents
    }

    public function test_quote_line_with_product_class_relationship()
    {
        $productClass = ProductClass::create([
            'root_code' => 'ULFR',
            'root_name' => 'Ultraleather',
            'part_number_prefix' => 'ULFR',
            'moq_ly' => 5,
            'uom' => 'LY',
            'lead_time_weeks' => '6-8',
            'price' => 96.25,
            'description' => 'Fire Retardant Ultraleather',
        ]);

        $quoteLine = QuoteLine::create([
            'quote_id' => $this->quote->id,
            'product_class_id' => $productClass->id,
            'part_number' => 'ULFR-3901',
            'root_code' => 'ULFR',
            'description' => 'Ultraleather - Navy Blue',
            'quantity' => 100,
            'unit' => 'LY',
            'standard_price' => 9625,
            'final_price' => 9625,
            'moq' => $productClass->moq_ly,
            'lead_time' => $productClass->lead_time_weeks,
        ]);

        $this->assertEquals($productClass->id, $quoteLine->product_class_id);
        $this->assertEquals('ULFR', $quoteLine->root_code);
        $this->assertEquals(5, $quoteLine->moq);
    }

    public function test_quote_line_with_product_relationship()
    {
        // First create product class
        $productClass = ProductClass::create([
            'root_code' => 'ULFR',
            'root_name' => 'Ultraleather',
            'part_number_prefix' => 'ULFR',
            'moq_ly' => 5,
            'uom' => 'LY',
            'lead_time_weeks' => '6-8',
            'price' => 96.25,
            'description' => 'Fire Retardant Ultraleather',
        ]);

        $product = Product::create([
            'part_number' => 'ULFR-3901',
            'root_code' => 'ULFR',
            'color_name' => 'Navy Blue',
            'color_code' => '3901',
            'description' => 'Ultraleather Fire Retardant - Navy Blue',
            'price' => 96.25,
            'moq' => 5,
            'uom' => 'LY',
            'lead_time_weeks' => '6-8',
            'is_active' => true,
        ]);

        $quoteLine = QuoteLine::create([
            'quote_id' => $this->quote->id,
            'product_id' => $product->id,
            'part_number' => $product->part_number,
            'description' => $product->description,
            'quantity' => 100,
            'unit' => $product->uom,
            'standard_price' => $product->price * 100, // Convert to cents
            'final_price' => $product->price * 100,
            'moq' => $product->moq,
            'lead_time' => $product->lead_time_weeks,
        ]);

        $this->assertEquals($product->id, $quoteLine->product_id);
        $this->assertEquals('ULFR-3901', $quoteLine->part_number);
        $this->assertEquals('Navy Blue', $product->color_name);
    }

    public function test_quote_line_respects_sort_order()
    {
        $line1 = QuoteLine::create([
            'quote_id' => $this->quote->id,
            'part_number' => 'ULFR-3901',
            'description' => 'Product 1',
            'quantity' => 10,
            'unit' => 'LY',
            'standard_price' => 10000,
            'final_price' => 10000,
            'sort_order' => 2,
        ]);

        $line2 = QuoteLine::create([
            'quote_id' => $this->quote->id,
            'part_number' => 'ULFR-3902',
            'description' => 'Product 2',
            'quantity' => 20,
            'unit' => 'LY',
            'standard_price' => 10000,
            'final_price' => 10000,
            'sort_order' => 1,
        ]);

        $line3 = QuoteLine::create([
            'quote_id' => $this->quote->id,
            'part_number' => 'ULFR-3903',
            'description' => 'Product 3',
            'quantity' => 30,
            'unit' => 'LY',
            'standard_price' => 10000,
            'final_price' => 10000,
            'sort_order' => 3,
        ]);

        $orderedLines = $this->quote->quoteLines()->orderBy('sort_order')->get();
        
        $this->assertEquals('ULFR-3902', $orderedLines[0]->part_number);
        $this->assertEquals('ULFR-3901', $orderedLines[1]->part_number);
        $this->assertEquals('ULFR-3903', $orderedLines[2]->part_number);
    }

    public function test_quote_line_with_override_values()
    {
        $quoteLine = QuoteLine::create([
            'quote_id' => $this->quote->id,
            'part_number' => 'CUSTOM-001',
            'description' => 'Standard Description',
            'quantity' => 50,
            'unit' => 'LY',
            'standard_price' => 10000,
            'final_price' => 10000,
            'lead_time' => '6-8 weeks',
            'override_price' => 85.00,
            'override_description' => 'Custom Description for Client',
            'override_lead_time' => '4-5 weeks',
            'is_custom_item' => true,
        ]);

        $this->assertTrue($quoteLine->is_custom_item);
        $this->assertEquals(85.00, $quoteLine->override_price);
        $this->assertEquals('Custom Description for Client', $quoteLine->override_description);
        $this->assertEquals('4-5 weeks', $quoteLine->override_lead_time);
    }

    public function test_quote_line_with_moq_waiver()
    {
        $quoteLine = QuoteLine::create([
            'quote_id' => $this->quote->id,
            'part_number' => 'ULFR-3901',
            'description' => 'Ultraleather - Navy Blue',
            'quantity' => 3, // Below MOQ of 5
            'unit' => 'LY',
            'standard_price' => 10000,
            'final_price' => 10000,
            'moq' => 5,
            'moq_waived' => true,
            'moq_waiver_reason' => 'Sample order for new customer',
            'lead_time' => '6-8 weeks',
        ]);

        $this->assertTrue($quoteLine->moq_waived);
        $this->assertEquals('Sample order for new customer', $quoteLine->moq_waiver_reason);
        $this->assertEquals(3, $quoteLine->quantity); // Allowed below MOQ
    }

    public function test_quote_line_pricing_source_tracking()
    {
        $standardPriceLine = QuoteLine::create([
            'quote_id' => $this->quote->id,
            'part_number' => 'ULFR-3901',
            'description' => 'Standard Price Item',
            'quantity' => 10,
            'unit' => 'LY',
            'standard_price' => 10000,
            'final_price' => 10000,
            'pricing_source' => 'standard',
        ]);

        $contractPriceLine = QuoteLine::create([
            'quote_id' => $this->quote->id,
            'part_number' => 'ULFR-3902',
            'description' => 'Contract Price Item',
            'quantity' => 100,
            'unit' => 'LY',
            'standard_price' => 10000,
            'final_price' => 9000, // 10% discount
            'pricing_source' => 'contract',
            'contract_price' => 9000,
            'pricing_reference' => 'Contract #2024-001',
        ]);

        $manualPriceLine = QuoteLine::create([
            'quote_id' => $this->quote->id,
            'part_number' => 'ULFR-3903',
            'description' => 'Manual Price Item',
            'quantity' => 50,
            'unit' => 'LY',
            'standard_price' => 10000,
            'final_price' => 8500,
            'pricing_source' => 'manual',
            'notes' => 'Special pricing approved by management',
        ]);

        $this->assertEquals('standard', $standardPriceLine->pricing_source);
        $this->assertEquals('contract', $contractPriceLine->pricing_source);
        $this->assertEquals('manual', $manualPriceLine->pricing_source);
        $this->assertEquals('Contract #2024-001', $contractPriceLine->pricing_reference);
    }
}