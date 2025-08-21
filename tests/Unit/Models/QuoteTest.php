<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\User;
use App\Models\Airline;
use App\Models\Subcontractor;
use App\Models\ExternalCustomer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_quote_can_be_created_with_airline_customer()
    {
        $user = User::factory()->create();
        $airline = Airline::create(['name' => 'Test Airlines', 'code' => 'TA']);
        
        $quote = Quote::create([
            'user_id' => $user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $airline->id,
            'customer_name' => $airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('quotes', [
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $airline->id,
        ]);
        
        $this->assertInstanceOf(Airline::class, $quote->customer);
        $this->assertEquals('Test Airlines', $quote->customer->name);
    }

    public function test_quote_can_be_created_with_subcontractor_customer()
    {
        $user = User::factory()->create();
        $subcontractor = Subcontractor::create(['name' => 'Test Subcontractor']);
        
        $quote = Quote::create([
            'user_id' => $user->id,
            'customer_type' => 'App\Models\Subcontractor',
            'customer_id' => $subcontractor->id,
            'customer_name' => $subcontractor->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
            'status' => 'draft',
        ]);

        $this->assertInstanceOf(Subcontractor::class, $quote->customer);
        $this->assertEquals('Test Subcontractor', $quote->customer->name);
    }

    public function test_quote_can_be_created_with_external_customer()
    {
        $user = User::factory()->create();
        $customer = ExternalCustomer::create([
            'name' => 'External Company',
            'contact_name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        $quote = Quote::create([
            'user_id' => $user->id,
            'customer_type' => 'App\Models\ExternalCustomer',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
            'status' => 'draft',
        ]);

        $this->assertInstanceOf(ExternalCustomer::class, $quote->customer);
        $this->assertEquals('External Company', $quote->customer->name);
    }

    public function test_quote_generates_unique_number_automatically()
    {
        $user = User::factory()->create();
        $airline = Airline::create(['name' => 'Test Airlines', 'code' => 'TA']);
        
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

        $this->assertNotNull($quote1->quote_number);
        $this->assertNotNull($quote2->quote_number);
        $this->assertNotEquals($quote1->quote_number, $quote2->quote_number);
        $this->assertStringContainsString(date('Y'), $quote1->quote_number);
    }

    public function test_quote_can_be_soft_deleted()
    {
        $user = User::factory()->create();
        $airline = Airline::create(['name' => 'Test Airlines', 'code' => 'TA']);
        
        $quote = Quote::create([
            'user_id' => $user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $airline->id,
            'customer_name' => $airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        $quote->delete();

        $this->assertSoftDeleted('quotes', ['id' => $quote->id]);
        $this->assertNotNull(Quote::withTrashed()->find($quote->id));
    }

    public function test_quote_calculates_total_amount()
    {
        $user = User::factory()->create();
        $airline = Airline::create(['name' => 'Test Airlines', 'code' => 'TA']);
        
        $quote = Quote::create([
            'user_id' => $user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $airline->id,
            'customer_name' => $airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        QuoteLine::create([
            'quote_id' => $quote->id,
            'part_number' => 'TEST-001',
            'description' => 'Test Product 1',
            'quantity' => 10,
            'unit' => 'LY',
            'standard_price' => 10000, // $100.00 in cents
            'final_price' => 9500, // $95.00 in cents
        ]);

        QuoteLine::create([
            'quote_id' => $quote->id,
            'part_number' => 'TEST-002',
            'description' => 'Test Product 2',
            'quantity' => 5,
            'unit' => 'LY',
            'standard_price' => 20000, // $200.00 in cents
            'final_price' => 20000, // $200.00 in cents
        ]);

        $quote->refresh();
        
        // (10 * 95.00) + (5 * 200.00) = 950.00 + 1000.00 = 1950.00
        $this->assertEquals(195000, $quote->total_amount);
        $this->assertEquals('$1,950.00', $quote->total_amount_formatted);
    }

    public function test_quote_status_scopes_work()
    {
        $user = User::factory()->create();
        $airline = Airline::create(['name' => 'Test Airlines', 'code' => 'TA']);
        
        $draftQuote = Quote::create([
            'user_id' => $user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $airline->id,
            'customer_name' => $airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
            'status' => 'draft',
        ]);

        $sentQuote = Quote::create([
            'user_id' => $user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $airline->id,
            'customer_name' => $airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
            'status' => 'sent',
        ]);

        $acceptedQuote = Quote::create([
            'user_id' => $user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $airline->id,
            'customer_name' => $airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
            'status' => 'accepted',
        ]);

        $this->assertEquals(1, Quote::draft()->count());
        $this->assertEquals(1, Quote::sent()->count());
        $this->assertEquals(1, Quote::accepted()->count());
        
        $this->assertTrue(Quote::draft()->first()->is($draftQuote));
        $this->assertTrue(Quote::sent()->first()->is($sentQuote));
        $this->assertTrue(Quote::accepted()->first()->is($acceptedQuote));
    }

    public function test_quote_can_create_revision()
    {
        $user = User::factory()->create();
        $airline = Airline::create(['name' => 'Test Airlines', 'code' => 'TA']);
        
        $originalQuote = Quote::create([
            'user_id' => $user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $airline->id,
            'customer_name' => $airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
            'status' => 'sent',
        ]);

        QuoteLine::create([
            'quote_id' => $originalQuote->id,
            'part_number' => 'TEST-001',
            'description' => 'Test Product',
            'quantity' => 10,
            'unit' => 'LY',
            'standard_price' => 10000,
            'final_price' => 10000,
        ]);

        $revision = $originalQuote->createRevision('Customer requested changes');

        $this->assertEquals($originalQuote->id, $revision->parent_quote_id);
        $this->assertEquals(1, $revision->revision_number);
        $this->assertEquals('Customer requested changes', $revision->revision_reason);
        $this->assertEquals('draft', $revision->status);
        $this->assertEquals(1, $revision->quoteLines()->count());
        $this->assertTrue($revision->isRevision());
    }
}