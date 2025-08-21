<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use App\Models\User;
use App\Models\Quote;
use App\Models\Airline;
use App\Livewire\QuoteIndex;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuoteIndexTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Airline $airline;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->airline = Airline::create([
            'name' => 'Test Airlines',
            'code' => 'TA',
        ]);
        
        $this->actingAs($this->user);
    }

    public function test_quote_index_component_renders()
    {
        Livewire::test(QuoteIndex::class)
            ->assertStatus(200)
            ->assertSee('Quotes');
    }

    public function test_quotes_are_displayed_in_table()
    {
        $quote1 = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'quote_number' => '2025-0001',
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
            'status' => 'draft',
        ]);

        $quote2 = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'quote_number' => '2025-0002',
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
            'status' => 'sent',
        ]);

        Livewire::test(QuoteIndex::class)
            ->assertSee('2025-0001')
            ->assertSee('2025-0002')
            ->assertSee('Test Airlines')
            ->assertSee('Draft')
            ->assertSee('Sent');
    }

    public function test_search_filters_quotes()
    {
        Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => 'American Airlines',
            'quote_number' => '2025-0001',
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => 'Delta Airlines',
            'quote_number' => '2025-0002',
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        Livewire::test(QuoteIndex::class)
            ->set('search', 'American')
            ->assertSee('2025-0001')
            ->assertSee('American Airlines')
            ->assertDontSee('2025-0002')
            ->assertDontSee('Delta Airlines');
    }

    public function test_status_filter_works()
    {
        Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'quote_number' => '2025-0001',
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
            'status' => 'draft',
        ]);

        Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'quote_number' => '2025-0002',
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
            'status' => 'sent',
        ]);

        Livewire::test(QuoteIndex::class)
            ->set('status', 'draft')
            ->assertSee('2025-0001')
            ->assertDontSee('2025-0002');
    }

    public function test_sorting_works()
    {
        $oldQuote = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'quote_number' => '2025-0001',
            'date_entry' => now()->subDays(10),
            'date_valid' => now()->addDays(20),
        ]);

        $newQuote = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'quote_number' => '2025-0002',
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        Livewire::test(QuoteIndex::class)
            ->call('sortBy', 'date_entry')
            ->assertSeeInOrder(['2025-0001', '2025-0002'])
            ->call('sortBy', 'date_entry') // Toggle to desc
            ->assertSeeInOrder(['2025-0002', '2025-0001']);
    }

    public function test_quote_can_be_deleted()
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

        Livewire::test(QuoteIndex::class)
            ->assertSee('2025-0001')
            ->call('deleteQuote', $quote->id)
            ->assertDispatchedBrowserEvent('quote-deleted')
            ->assertDontSee('2025-0001');

        $this->assertSoftDeleted('quotes', ['id' => $quote->id]);
    }

    public function test_deleted_quotes_can_be_shown()
    {
        $activeQuote = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'quote_number' => '2025-0001',
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);

        $deletedQuote = Quote::create([
            'user_id' => $this->user->id,
            'customer_type' => 'App\Models\Airline',
            'customer_id' => $this->airline->id,
            'customer_name' => $this->airline->name,
            'quote_number' => '2025-0002',
            'date_entry' => now(),
            'date_valid' => now()->addDays(30),
        ]);
        $deletedQuote->delete();

        Livewire::test(QuoteIndex::class)
            ->assertSee('2025-0001')
            ->assertDontSee('2025-0002')
            ->set('showDeleted', true)
            ->assertSee('2025-0001')
            ->assertSee('2025-0002');
    }

    public function test_deleted_quote_can_be_restored()
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
        $quote->delete();

        Livewire::test(QuoteIndex::class)
            ->set('showDeleted', true)
            ->assertSee('2025-0001')
            ->call('restoreQuote', $quote->id)
            ->set('showDeleted', false)
            ->assertSee('2025-0001');

        $this->assertDatabaseHas('quotes', [
            'id' => $quote->id,
            'deleted_at' => null,
        ]);
    }

    public function test_pagination_works()
    {
        // Create 15 quotes (more than default 10 per page)
        for ($i = 1; $i <= 15; $i++) {
            Quote::create([
                'user_id' => $this->user->id,
                'customer_type' => 'App\Models\Airline',
                'customer_id' => $this->airline->id,
                'customer_name' => $this->airline->name,
                'quote_number' => sprintf('2025-%04d', $i),
                'date_entry' => now(),
                'date_valid' => now()->addDays(30),
            ]);
        }

        $component = Livewire::test(QuoteIndex::class);
        
        // Should see first 10 quotes on page 1
        for ($i = 1; $i <= 10; $i++) {
            $component->assertSee(sprintf('2025-%04d', $i));
        }
        
        // Should not see quotes 11-15 on page 1
        for ($i = 11; $i <= 15; $i++) {
            $component->assertDontSee(sprintf('2025-%04d', $i));
        }
    }
}