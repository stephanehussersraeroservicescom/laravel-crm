<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use App\Models\User;
use App\Models\Subcontractor;
use App\Livewire\SubcontractorsTable;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubcontractorsTableTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_subcontractors_table_component_renders()
    {
        Livewire::test(SubcontractorsTable::class)
            ->assertStatus(200)
            ->assertSee('Subcontractors');
    }

    public function test_subcontractors_are_displayed()
    {
        $sub1 = Subcontractor::create([
            'name' => 'ABC Manufacturing',
            'contact_name' => 'John Smith',
            'email' => 'john@abc.com',
            'phone' => '+1-555-0100',
        ]);

        $sub2 = Subcontractor::create([
            'name' => 'XYZ Industries',
            'contact_name' => 'Jane Doe',
            'email' => 'jane@xyz.com',
            'phone' => '+1-555-0200',
        ]);

        Livewire::test(SubcontractorsTable::class)
            ->assertSee('ABC Manufacturing')
            ->assertSee('John Smith')
            ->assertSee('john@abc.com')
            ->assertSee('XYZ Industries')
            ->assertSee('Jane Doe')
            ->assertSee('jane@xyz.com');
    }

    public function test_search_filters_subcontractors()
    {
        Subcontractor::create([
            'name' => 'Alpha Company',
            'contact_name' => 'Alice Anderson',
            'email' => 'alice@alpha.com',
        ]);

        Subcontractor::create([
            'name' => 'Beta Corporation',
            'contact_name' => 'Bob Brown',
            'email' => 'bob@beta.com',
        ]);

        Livewire::test(SubcontractorsTable::class)
            ->set('search', 'Alpha')
            ->assertSee('Alpha Company')
            ->assertSee('Alice Anderson')
            ->assertDontSee('Beta Corporation')
            ->assertDontSee('Bob Brown');
    }

    public function test_create_modal_opens()
    {
        Livewire::test(SubcontractorsTable::class)
            ->call('openCreateModal')
            ->assertSet('showModal', true)
            ->assertSet('editingSubcontractor', null);
    }

    public function test_edit_modal_opens_with_data()
    {
        $subcontractor = Subcontractor::create([
            'name' => 'Edit Test Company',
            'contact_name' => 'Edit Contact',
            'email' => 'edit@test.com',
            'comment' => 'Test comment',
        ]);

        Livewire::test(SubcontractorsTable::class)
            ->call('openEditModal', $subcontractor->id)
            ->assertSet('showModal', true)
            ->assertSet('editingSubcontractor.id', $subcontractor->id)
            ->assertSet('form.name', 'Edit Test Company')
            ->assertSet('form.contact_name', 'Edit Contact')
            ->assertSet('form.email', 'edit@test.com')
            ->assertSet('form.comment', 'Test comment');
    }

    public function test_subcontractor_can_be_created()
    {
        Livewire::test(SubcontractorsTable::class)
            ->call('openCreateModal')
            ->set('form.name', 'New Subcontractor')
            ->set('form.contact_name', 'New Contact')
            ->set('form.email', 'new@subcontractor.com')
            ->set('form.phone', '+1-555-9999')
            ->set('form.address', '123 New Street')
            ->set('form.payment_terms', 'Net 30')
            ->set('form.comment', 'New subcontractor comment')
            ->call('save')
            ->assertSet('showModal', false)
            ->assertDispatchedBrowserEvent('subcontractor-saved');

        $this->assertDatabaseHas('subcontractors', [
            'name' => 'New Subcontractor',
            'contact_name' => 'New Contact',
            'email' => 'new@subcontractor.com',
            'phone' => '+1-555-9999',
            'address' => '123 New Street',
            'payment_terms' => 'Net 30',
            'comment' => 'New subcontractor comment',
        ]);
    }

    public function test_subcontractor_can_be_updated()
    {
        $subcontractor = Subcontractor::create([
            'name' => 'Original Name',
            'contact_name' => 'Original Contact',
            'email' => 'original@email.com',
        ]);

        Livewire::test(SubcontractorsTable::class)
            ->call('openEditModal', $subcontractor->id)
            ->set('form.name', 'Updated Name')
            ->set('form.contact_name', 'Updated Contact')
            ->set('form.email', 'updated@email.com')
            ->call('save')
            ->assertSet('showModal', false)
            ->assertDispatchedBrowserEvent('subcontractor-saved');

        $this->assertDatabaseHas('subcontractors', [
            'id' => $subcontractor->id,
            'name' => 'Updated Name',
            'contact_name' => 'Updated Contact',
            'email' => 'updated@email.com',
        ]);
    }

    public function test_subcontractor_can_be_deleted()
    {
        $subcontractor = Subcontractor::create([
            'name' => 'To Be Deleted',
            'email' => 'delete@test.com',
        ]);

        Livewire::test(SubcontractorsTable::class)
            ->assertSee('To Be Deleted')
            ->call('deleteSubcontractor', $subcontractor->id)
            ->assertDontSee('To Be Deleted');

        $this->assertSoftDeleted('subcontractors', ['id' => $subcontractor->id]);
    }

    public function test_deleted_subcontractors_can_be_shown()
    {
        $active = Subcontractor::create([
            'name' => 'Active Subcontractor',
        ]);

        $deleted = Subcontractor::create([
            'name' => 'Deleted Subcontractor',
        ]);
        $deleted->delete();

        Livewire::test(SubcontractorsTable::class)
            ->assertSee('Active Subcontractor')
            ->assertDontSee('Deleted Subcontractor')
            ->set('showDeleted', true)
            ->assertSee('Active Subcontractor')
            ->assertSee('Deleted Subcontractor');
    }

    public function test_deleted_subcontractor_can_be_restored()
    {
        $subcontractor = Subcontractor::create([
            'name' => 'To Be Restored',
        ]);
        $subcontractor->delete();

        Livewire::test(SubcontractorsTable::class)
            ->set('showDeleted', true)
            ->assertSee('To Be Restored')
            ->call('restoreSubcontractor', $subcontractor->id)
            ->set('showDeleted', false)
            ->assertSee('To Be Restored');

        $this->assertDatabaseHas('subcontractors', [
            'id' => $subcontractor->id,
            'deleted_at' => null,
        ]);
    }

    public function test_validation_errors_are_shown()
    {
        Livewire::test(SubcontractorsTable::class)
            ->call('openCreateModal')
            ->set('form.name', '') // Required field
            ->set('form.email', 'invalid-email') // Invalid email format
            ->call('save')
            ->assertHasErrors(['form.name' => 'required'])
            ->assertHasErrors(['form.email' => 'email']);
    }

    public function test_unique_name_validation()
    {
        Subcontractor::create([
            'name' => 'Existing Company',
        ]);

        Livewire::test(SubcontractorsTable::class)
            ->call('openCreateModal')
            ->set('form.name', 'Existing Company')
            ->call('save')
            ->assertHasErrors(['form.name' => 'unique']);
    }

    public function test_sorting_works()
    {
        Subcontractor::create(['name' => 'Charlie Company']);
        Subcontractor::create(['name' => 'Alpha Company']);
        Subcontractor::create(['name' => 'Beta Company']);

        Livewire::test(SubcontractorsTable::class)
            ->call('sortBy', 'name')
            ->assertSeeInOrder(['Alpha Company', 'Beta Company', 'Charlie Company'])
            ->call('sortBy', 'name') // Toggle to desc
            ->assertSeeInOrder(['Charlie Company', 'Beta Company', 'Alpha Company']);
    }

    public function test_parent_subcontractor_relationships()
    {
        $parent1 = Subcontractor::create(['name' => 'Parent Company 1']);
        $parent2 = Subcontractor::create(['name' => 'Parent Company 2']);
        $child = Subcontractor::create(['name' => 'Child Company']);

        // Attach parents to child
        $child->parents()->attach([$parent1->id, $parent2->id]);

        Livewire::test(SubcontractorsTable::class)
            ->call('openEditModal', $child->id)
            ->assertSet('form.selectedParents', [$parent1->id, $parent2->id]);
    }
}