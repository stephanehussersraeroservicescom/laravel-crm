<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected ProductClass $productClass;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->productClass = ProductClass::create([
            'root_code' => 'ULFR',
            'root_name' => 'Ultraleather',
            'part_number_prefix' => 'ULFR',
            'moq_ly' => 5,
            'uom' => 'LY',
            'lead_time_weeks' => '6-8',
            'price' => 96.25,
            'description' => 'Fire Retardant Ultraleather',
        ]);
    }

    public function test_product_can_be_created()
    {
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

        $this->assertDatabaseHas('products', [
            'part_number' => 'ULFR-3901',
            'color_name' => 'Navy Blue',
        ]);
        
        $this->assertEquals('ULFR-3901', $product->part_number);
        $this->assertEquals('Navy Blue', $product->color_name);
        $this->assertTrue($product->is_active);
    }

    public function test_product_belongs_to_product_class()
    {
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

        $this->assertEquals('ULFR', $product->root_code);
        $this->assertInstanceOf(ProductClass::class, $product->productClass);
        $this->assertEquals('Ultraleather', $product->productClass->root_name);
    }

    public function test_product_can_be_soft_deleted()
    {
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

        $product->delete();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
        $this->assertNotNull(Product::withTrashed()->find($product->id));
    }

    public function test_product_unique_part_number_constraint()
    {
        Product::create([
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

        $this->expectException(\Illuminate\Database\QueryException::class);

        Product::create([
            'part_number' => 'ULFR-3901', // Duplicate part number
            'root_code' => 'ULFR',
            'color_name' => 'Different Color',
            'color_code' => '3902',
            'description' => 'Different Description',
            'price' => 100.00,
            'moq' => 10,
            'uom' => 'LY',
            'lead_time_weeks' => '8-10',
            'is_active' => true,
        ]);
    }

    public function test_product_with_different_uom()
    {
        $unitProduct = Product::create([
            'part_number' => 'PANEL-001',
            'root_code' => 'ULFR',
            'color_name' => 'Custom Panel',
            'color_code' => '',
            'description' => 'Custom Cut Panel',
            'price' => 250.00,
            'moq' => 1,
            'uom' => 'UNIT',
            'lead_time_weeks' => '2-3',
            'is_active' => true,
        ]);

        $this->assertEquals('UNIT', $unitProduct->uom);
        $this->assertEquals(1, $unitProduct->moq);
        $this->assertEquals(250.00, $unitProduct->price);
    }

    public function test_inactive_product()
    {
        $inactiveProduct = Product::create([
            'part_number' => 'OLD-001',
            'root_code' => 'ULFR',
            'color_name' => 'Discontinued Color',
            'color_code' => '9999',
            'description' => 'Discontinued Product',
            'price' => 50.00,
            'moq' => 100,
            'uom' => 'LY',
            'lead_time_weeks' => 'N/A',
            'is_active' => false,
        ]);

        $this->assertFalse($inactiveProduct->is_active);
        
        // Test scope for active products
        $activeCount = Product::where('is_active', true)->count();
        $inactiveCount = Product::where('is_active', false)->count();
        
        $this->assertEquals(0, $activeCount);
        $this->assertEquals(1, $inactiveCount);
    }
}