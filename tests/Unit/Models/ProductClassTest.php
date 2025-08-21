<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ProductClass;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductClassTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_class_can_be_created()
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
            'has_ink_resist' => false,
            'is_bio' => false,
        ]);

        $this->assertDatabaseHas('product_classes', [
            'root_code' => 'ULFR',
            'root_name' => 'Ultraleather',
        ]);
        
        $this->assertEquals('ULFR', $productClass->root_code);
        $this->assertEquals('Ultraleather', $productClass->root_name);
        $this->assertEquals(96.25, $productClass->price);
    }

    public function test_product_class_with_ink_resist()
    {
        $productClass = ProductClass::create([
            'root_code' => 'ULPROFR',
            'root_name' => 'Pro',
            'part_number_prefix' => 'ULPROFR',
            'moq_ly' => 5,
            'uom' => 'LY',
            'lead_time_weeks' => '6-8',
            'price' => 104.75,
            'description' => 'Pro with Ink Resistant coating',
            'has_ink_resist' => true,
            'is_bio' => false,
        ]);

        $this->assertTrue($productClass->has_ink_resist);
        $this->assertFalse($productClass->is_bio);
    }

    public function test_product_class_with_bio()
    {
        $productClass = ProductClass::create([
            'root_code' => 'ULVBIOFR',
            'root_name' => 'Volar Bio',
            'part_number_prefix' => 'ULVBIOFR',
            'moq_ly' => 5,
            'uom' => 'LY',
            'lead_time_weeks' => '6-8',
            'price' => 168.25,
            'description' => 'Bio-based sustainable material',
            'has_ink_resist' => false,
            'is_bio' => true,
        ]);

        $this->assertFalse($productClass->has_ink_resist);
        $this->assertTrue($productClass->is_bio);
    }

    public function test_product_class_has_many_products()
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

        $product1 = Product::create([
            'part_number' => 'ULFR-3901',
            'root_code' => 'ULFR',
            'color_name' => 'Navy Blue',
            'color_code' => '3901',
            'price' => 96.25,
            'moq' => 5,
            'uom' => 'LY',
            'lead_time_weeks' => '6-8',
            'is_active' => true,
        ]);

        $product2 = Product::create([
            'part_number' => 'ULFR-3902',
            'root_code' => 'ULFR',
            'color_name' => 'Dark Gray',
            'color_code' => '3902',
            'price' => 96.25,
            'moq' => 5,
            'uom' => 'LY',
            'lead_time_weeks' => '6-8',
            'is_active' => true,
        ]);

        $products = $productClass->products;
        
        $this->assertCount(2, $products);
        $this->assertTrue($products->contains($product1));
        $this->assertTrue($products->contains($product2));
    }

    public function test_product_class_can_be_soft_deleted()
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

        $productClass->delete();

        $this->assertSoftDeleted('product_classes', ['id' => $productClass->id]);
        $this->assertNotNull(ProductClass::withTrashed()->find($productClass->id));
    }

    public function test_product_class_with_different_moq_values()
    {
        $lowMoq = ProductClass::create([
            'root_code' => 'ULFR',
            'root_name' => 'Ultraleather',
            'part_number_prefix' => 'ULFR',
            'moq_ly' => 5,
            'uom' => 'LY',
            'lead_time_weeks' => '6-8',
            'price' => 96.25,
            'description' => 'Low MOQ Product',
        ]);

        $highMoq = ProductClass::create([
            'root_code' => 'BHC',
            'root_name' => 'Burn Barrier',
            'part_number_prefix' => 'BHC',
            'moq_ly' => 500,
            'uom' => 'LY',
            'lead_time_weeks' => '12-16',
            'price' => 153.95,
            'description' => 'High MOQ Product',
        ]);

        $this->assertEquals(5, $lowMoq->moq_ly);
        $this->assertEquals(500, $highMoq->moq_ly);
        $this->assertEquals('12-16', $highMoq->lead_time_weeks);
    }

    public function test_product_class_with_unit_uom()
    {
        $unitProduct = ProductClass::create([
            'root_code' => 'PANEL',
            'root_name' => 'Custom Panel',
            'part_number_prefix' => 'PANEL',
            'moq_ly' => 1,
            'uom' => 'UNIT',
            'lead_time_weeks' => '2-3',
            'price' => 250.00,
            'description' => 'Custom cut panels sold by unit',
        ]);

        $this->assertEquals('UNIT', $unitProduct->uom);
        $this->assertEquals(1, $unitProduct->moq_ly);
    }

    public function test_product_class_price_variations()
    {
        $standardPrice = ProductClass::create([
            'root_code' => 'ULFR',
            'root_name' => 'Ultraleather',
            'part_number_prefix' => 'ULFR',
            'moq_ly' => 5,
            'uom' => 'LY',
            'lead_time_weeks' => '6-8',
            'price' => 96.25,
            'description' => 'Standard price product',
        ]);

        $premiumPrice = ProductClass::create([
            'root_code' => 'ULVBIOFR',
            'root_name' => 'Volar Bio',
            'part_number_prefix' => 'ULVBIOFR',
            'moq_ly' => 5,
            'uom' => 'LY',
            'lead_time_weeks' => '6-8',
            'price' => 168.25,
            'description' => 'Premium bio-based product',
        ]);

        $this->assertLessThan($premiumPrice->price, $standardPrice->price);
        $this->assertEquals(168.25, $premiumPrice->price);
        $this->assertEquals(96.25, $standardPrice->price);
    }
}