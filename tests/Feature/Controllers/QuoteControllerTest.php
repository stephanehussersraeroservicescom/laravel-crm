<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\Airline;
use App\Models\Subcontractor;
use App\Models\ExternalCustomer;
use App\Models\ProductClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuoteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Airline $airline;
    protected ProductClass $productClass;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->airline = Airline::create([
            'name' => 'Test Airlines',
            'code' => 'TA',
        ]);
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
        
        $this->actingAs($this->user);
    }

    public function test_quotes_index_page_loads()
    {
        $response = $this->get(route('quotes.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('quotes.index');
    }

    public function test_quote_create_page_loads()
    {
        $response = $this->get(route('quotes.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('quotes.create');
        $response->assertViewHas('airlines');
        $response->assertViewHas('subcontractors');
        $response->assertViewHas('externalCustomers');
    }

    public function test_quote_show_page_loads()
    {
        $quote = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        $response = $this->get(route('quotes.show', $quote));
        
        $response->assertStatus(200);
        $response->assertViewIs('quotes.show');
        $response->assertViewHas('quote', $quote);
    }

    public function test_quote_edit_page_loads()
    {
        $quote = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        $response = $this->get(route('quotes.edit', $quote));
        
        $response->assertStatus(200);
        $response->assertViewIs('quotes.edit');
        $response->assertViewHas('quote', $quote);
        $response->assertViewHas('airlines');
        $response->assertViewHas('subcontractors');
        $response->assertViewHas('externalCustomers');
    }

    public function test_quote_preview_pdf_loads()
    {
        $quote = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
            'shipping_terms' => 'Ex Works Dallas Texas',
            'payment_terms' => 'Pro Forma',
        ]);

        QuoteLine::create([
            'quote_id' => $quote->id,
            'part_number' => 'ULFR-3901',
            'description' => 'Ultraleather - Navy Blue',
            'quantity' => 100,
            'unit' => 'LY',
            'standard_price' => 9625,
            'final_price' => 9625,
        ]);

        $response = $this->get(route('quotes.preview', $quote));
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_quote_can_be_deleted()
    {
        $quote = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        $response = $this->delete(route('quotes.destroy', $quote));
        
        $response->assertRedirect(route('quotes.index'));
        $this->assertSoftDeleted('quotes', ['id' => $quote->id]);
    }

    public function test_quote_download_pdf()
    {
        $quote = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'quote_number' => '2025-0001',
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        $response = $this->get(route('quotes.download', $quote));
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename="quote-2025-0001.pdf"');
    }

    public function test_unauthorized_user_cannot_access_quotes()
    {
        $this->app['auth']->guard()->logout();
        
        $response = $this->get(route('quotes.index'));
        
        $response->assertRedirect(route('login'));
    }

    public function test_quote_with_different_customer_types()
    {
        // Test with Airline customer
        $airlineQuote = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        $response = $this->get(route('quotes.show', $airlineQuote));
        $response->assertStatus(200);
        $response->assertSee($this->airline->name);

        // Test with Subcontractor customer
        $subcontractor = Subcontractor::create(['name' => 'Test Subcontractor']);
        $subQuote = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Subcontractor',
            'customer_id' => $subcontractor->id,
            'customer_name' => $subcontractor->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        $response = $this->get(route('quotes.show', $subQuote));
        $response->assertStatus(200);
        $response->assertSee($subcontractor->name);

        // Test with External Customer
        $external = ExternalCustomer::create([
            'name' => 'External Company',
            'contact_name' => 'John Doe',
            'email' => 'john@external.com',
        ]);
        $extQuote = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\ExternalCustomer',
            'customer_id' => $external->id,
            'customer_name' => $external->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        $response = $this->get(route('quotes.show', $extQuote));
        $response->assertStatus(200);
        $response->assertSee($external->name);
    }

    public function test_quote_with_multiple_lines()
    {
        $quote = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        // Add multiple quote lines
        for ($i = 1; $i <= 5; $i++) {
            QuoteLine::create([
                'quote_id' => $quote->id,
                'part_number' => "ULFR-390{$i}",
                'description' => "Product {$i}",
                'quantity' => $i * 10,
                'unit' => 'LY',
                'standard_price' => 10000,
                'final_price' => 10000,
                'sort_order' => $i,
            ]);
        }

        $response = $this->get(route('quotes.show', $quote));
        
        $response->assertStatus(200);
        $quote->refresh();
        $this->assertCount(5, $quote->quoteLines);
        
        // Check total calculation
        $expectedTotal = (10 * 10000) + (20 * 10000) + (30 * 10000) + (40 * 10000) + (50 * 10000);
        $this->assertEquals($expectedTotal, $quote->total_amount);
    }
}