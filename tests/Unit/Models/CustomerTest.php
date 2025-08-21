<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Airline;
use App\Models\Subcontractor;
use App\Models\ExternalCustomer;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_airline_can_be_created()
    {
        $airline = Airline::create([
            'name' => 'American Airlines',
            'code' => 'AA',
            'region' => 'North America',
        ]);

        $this->assertDatabaseHas('airlines', [
            'name' => 'American Airlines',
            'code' => 'AA',
        ]);
        
        $this->assertEquals('American Airlines', $airline->name);
        $this->assertEquals('AA', $airline->code);
        $this->assertEquals('North America', $airline->region);
    }

    public function test_subcontractor_can_be_created()
    {
        $subcontractor = Subcontractor::create([
            'name' => 'ABC Manufacturing',
            'contact_name' => 'John Smith',
            'email' => 'john@abcmfg.com',
            'phone' => '+1-555-0100',
            'address' => '123 Industrial Blvd',
            'payment_terms' => 'Net 30',
            'comment' => 'Preferred vendor for leather goods',
        ]);

        $this->assertDatabaseHas('subcontractors', [
            'name' => 'ABC Manufacturing',
            'email' => 'john@abcmfg.com',
        ]);
        
        $this->assertEquals('ABC Manufacturing', $subcontractor->name);
        $this->assertEquals('John Smith', $subcontractor->contact_name);
        $this->assertEquals('Net 30', $subcontractor->payment_terms);
    }

    public function test_external_customer_can_be_created()
    {
        $customer = ExternalCustomer::create([
            'name' => 'XYZ Corporation',
            'contact_name' => 'Jane Doe',
            'email' => 'jane@xyzcorp.com',
            'phone' => '+1-555-0200',
            'address' => '456 Business Park',
            'payment_terms' => 'Pro Forma',
            'notes' => 'New customer, requires credit check',
            'is_regular' => false,
        ]);

        $this->assertDatabaseHas('external_customers', [
            'name' => 'XYZ Corporation',
            'email' => 'jane@xyzcorp.com',
        ]);
        
        $this->assertEquals('XYZ Corporation', $customer->name);
        $this->assertEquals('Pro Forma', $customer->payment_terms);
        $this->assertFalse($customer->is_regular);
    }

    public function test_airline_has_many_quotes()
    {
        $user = User::factory()->create();
        $airline = Airline::create([
            'name' => 'Delta Airlines',
            'code' => 'DL',
        ]);

        $quote1 = Quote::create([
            'user_id' => $user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $airline->id,
            'customer_name' => $airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        $quote2 = Quote::create([
            'user_id' => $user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $airline->id,
            'customer_name' => $airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        $quotes = $airline->quotes;
        
        $this->assertCount(2, $quotes);
        $this->assertTrue($quotes->contains($quote1));
        $this->assertTrue($quotes->contains($quote2));
    }

    public function test_subcontractor_has_many_quotes()
    {
        $user = User::factory()->create();
        $subcontractor = Subcontractor::create([
            'name' => 'Test Subcontractor',
        ]);

        $quote = Quote::create([
            'user_id' => $user->id,
            'customer_type' => 'App\Models\Subcontractor',
            'customer_id' => $subcontractor->id,
            'customer_name' => $subcontractor->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        $quotes = $subcontractor->quotes;
        
        $this->assertCount(1, $quotes);
        $this->assertTrue($quotes->contains($quote));
    }

    public function test_external_customer_has_many_quotes()
    {
        $user = User::factory()->create();
        $customer = ExternalCustomer::create([
            'name' => 'External Corp',
            'contact_name' => 'Contact Person',
            'email' => 'contact@external.com',
        ]);

        $quote = Quote::create([
            'user_id' => $user->id,
            'customer_type' => 'App\Models\ExternalCustomer',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        $quotes = $customer->quotes;
        
        $this->assertCount(1, $quotes);
        $this->assertTrue($quotes->contains($quote));
    }

    public function test_subcontractor_can_be_soft_deleted()
    {
        $subcontractor = Subcontractor::create([
            'name' => 'To Be Deleted LLC',
        ]);

        $subcontractor->delete();

        $this->assertSoftDeleted('subcontractors', ['id' => $subcontractor->id]);
        $this->assertNotNull(Subcontractor::withTrashed()->find($subcontractor->id));
    }

    public function test_external_customer_can_be_soft_deleted()
    {
        $customer = ExternalCustomer::create([
            'name' => 'To Be Deleted Corp',
            'contact_name' => 'Contact',
            'email' => 'delete@example.com',
        ]);

        $customer->delete();

        $this->assertSoftDeleted('external_customers', ['id' => $customer->id]);
        $this->assertNotNull(ExternalCustomer::withTrashed()->find($customer->id));
    }

    public function test_external_customer_regular_flag()
    {
        $regularCustomer = ExternalCustomer::create([
            'name' => 'Regular Customer Inc',
            'contact_name' => 'Regular Contact',
            'email' => 'regular@example.com',
            'is_regular' => true,
        ]);

        $oneTimeCustomer = ExternalCustomer::create([
            'name' => 'One Time Customer LLC',
            'contact_name' => 'One Time Contact',
            'email' => 'onetime@example.com',
            'is_regular' => false,
        ]);

        $this->assertTrue($regularCustomer->is_regular);
        $this->assertFalse($oneTimeCustomer->is_regular);
        
        // Could add scope methods to filter regular vs one-time customers
        $regularCount = ExternalCustomer::where('is_regular', true)->count();
        $oneTimeCount = ExternalCustomer::where('is_regular', false)->count();
        
        $this->assertEquals(1, $regularCount);
        $this->assertEquals(1, $oneTimeCount);
    }

    public function test_airline_unique_code_constraint()
    {
        Airline::create([
            'name' => 'First Airline',
            'code' => 'FA',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Airline::create([
            'name' => 'Different Airline',
            'code' => 'FA', // Duplicate code
        ]);
    }

    public function test_subcontractor_unique_name_constraint()
    {
        Subcontractor::create([
            'name' => 'Unique Subcontractor',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Subcontractor::create([
            'name' => 'Unique Subcontractor', // Duplicate name
        ]);
    }
}