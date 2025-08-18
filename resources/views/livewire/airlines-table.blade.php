<div class="space-y-6">
    <!-- Header -->
    <x-atomic.molecules.navigation.page-header title="Airline Management">
        <x-slot name="actions">
            <x-atomic.atoms.buttons.primary-button wire:click="openCreateModal">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Airline
            </x-atomic.atoms.buttons.primary-button>
        </x-slot>
    </x-atomic.molecules.navigation.page-header>

    <!-- Flash Messages -->
    <x-atomic.atoms.feedback.flash-message type="success" :message="session('message')" />

    <!-- Filter Panel -->
    <x-atomic.organisms.filters.filter-panel>
        <x-slot name="search">
            <!-- Search -->
            <x-atomic.molecules.forms.search-field 
                span="wide"
                label="Search"
                placeholder="Search airlines..."
                wire:model.live.debounce.300ms="search"
            />

            <!-- Per Page -->
            <x-atomic.molecules.forms.form-field-group label="Show">
                <x-atomic.atoms.forms.form-select wire:model.live="perPage">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
        </x-slot>

        <x-slot name="filters">
            <!-- Region Filter -->
            <x-atomic.molecules.forms.form-field-group label="Region">
                <x-atomic.atoms.forms.form-select wire:model.live="filterRegion">
                    <option value="">All Regions</option>
                    @foreach($availableRegions as $region)
                        <option value="{{ $region }}">{{ $region }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>

            <!-- Account Executive Filter -->
            <x-atomic.molecules.forms.form-field-group label="Account Executive">
                <x-atomic.atoms.forms.form-select wire:model.live="filterAccountExecutive">
                    <option value="">All Account Executives</option>
                    @foreach($salesUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
        </x-slot>

        <x-slot name="actions">
            <!-- Show Deleted Checkbox -->
            <x-atomic.atoms.forms.form-checkbox 
                wire:model.live="showDeleted"
                label="Show deleted airlines"
            />
            
            <x-atomic.atoms.buttons.secondary-button variant="gray" wire:click="clearFilters">
                Clear Filters
            </x-atomic.atoms.buttons.secondary-button>
        </x-slot>
    </x-atomic.organisms.filters.filter-panel>

    <!-- Airlines Table -->
    <x-atomic.molecules.tables.data-table>
        <x-slot name="head">
            <tr>
                <x-atomic.atoms.tables.table-header 
                    sortable 
                    field="name" 
                    :currentSort="$sortBy" 
                    :currentDirection="$sortDirection">
                    Name
                </x-atomic.atoms.tables.table-header>
                
                <x-atomic.atoms.tables.table-header 
                    sortable 
                    field="region" 
                    :currentSort="$sortBy" 
                    :currentDirection="$sortDirection">
                    Region
                </x-atomic.atoms.tables.table-header>
                
                <x-atomic.atoms.tables.table-header>
                    Account Executive
                </x-atomic.atoms.tables.table-header>
                
                @if($showDeleted)
                    <x-atomic.atoms.tables.table-header>
                        Status
                    </x-atomic.atoms.tables.table-header>
                @endif
                
                <x-atomic.atoms.tables.table-header class="text-right">
                    Actions
                </x-atomic.atoms.tables.table-header>
            </tr>
        </x-slot>

        @forelse($airlines as $airline)
            <x-atomic.molecules.tables.table-row :deleted="$airline->trashed()">
                <x-atomic.atoms.tables.table-cell variant="primary">
                    {{ $airline->name }}
                </x-atomic.atoms.tables.table-cell>
                
                <x-atomic.atoms.tables.table-cell variant="secondary">
                    {{ $airline->region }}
                </x-atomic.atoms.tables.table-cell>
                
                <x-atomic.atoms.tables.table-cell variant="secondary">
                    {{ $airline->accountExecutive?->name ?? 'Not assigned' }}
                </x-atomic.atoms.tables.table-cell>
                
                @if($showDeleted)
                    <x-atomic.atoms.tables.table-cell>
                        @if($airline->trashed())
                            <x-atomic.atoms.feedback.status-badge status="deleted">
                                Deleted {{ $airline->deleted_at->diffForHumans() }}
                            </x-atomic.atoms.feedback.status-badge>
                        @else
                            <x-atomic.atoms.feedback.status-badge status="active">
                                Active
                            </x-atomic.atoms.feedback.status-badge>
                        @endif
                    </x-atomic.atoms.tables.table-cell>
                @endif
                
                <x-atomic.atoms.tables.table-cell variant="action">
                    @if($airline->trashed())
                        <button wire:click="delete({{ $airline->id }})" 
                                class="text-indigo-600 hover:text-indigo-900">
                            Restore
                        </button>
                    @else
                        <button wire:click="openEditModal({{ $airline->id }})" 
                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                            Edit
                        </button>
                        <button wire:click="delete({{ $airline->id }})" 
                                onclick="return confirm('Are you sure you want to delete this airline?')"
                                class="text-red-600 hover:text-red-900">
                            Delete
                        </button>
                    @endif
                </x-atomic.atoms.tables.table-cell>
            </x-atomic.molecules.tables.table-row>
        @empty
            <tr>
                <td colspan="{{ $showDeleted ? 5 : 4 }}" class="px-6 py-4 text-center text-gray-500">
                    No airlines found
                </td>
            </tr>
        @endforelse

        <x-slot name="pagination">
            @if($airlines->hasPages())
                {{ $airlines->links() }}
            @endif
        </x-slot>
    </x-atomic.molecules.tables.data-table>

    <!-- Create/Edit Modal -->
    <x-atomic.organisms.modals.form-modal 
        :show="$showModal"
        :title="$modalMode === 'create' ? 'Create New Airline' : 'Edit Airline'"
        :submitText="$modalMode === 'create' ? 'Create' : 'Update'"
        wire:submit.prevent="save">
        
        <!-- Name -->
        <x-atomic.molecules.forms.form-field-group label="Airline Name" required>
            <x-atomic.atoms.forms.form-input 
                wire:model="name" 
                placeholder="Enter airline name"
                required
            />
            @error('name') 
                <x-atomic.atoms.feedback.error-message :message="$message" />
            @enderror
        </x-atomic.molecules.forms.form-field-group>

        <!-- Region -->
        <x-atomic.molecules.forms.form-field-group label="Region" required>
            <x-atomic.atoms.forms.form-select wire:model="region" required>
                <option value="">Select Region...</option>
                @foreach($availableRegions as $regionOption)
                    <option value="{{ $regionOption }}">{{ $regionOption }}</option>
                @endforeach
            </x-atomic.atoms.forms.form-select>
            @error('region') 
                <x-atomic.atoms.feedback.error-message :message="$message" />
            @enderror
        </x-atomic.molecules.forms.form-field-group>

        <!-- Account Executive -->
        <x-atomic.molecules.forms.form-field-group label="Account Executive">
            <x-atomic.atoms.forms.form-select wire:model="account_executive_id">
                <option value="">Select Account Executive...</option>
                @foreach($salesUsers as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </x-atomic.atoms.forms.form-select>
            @error('account_executive_id') 
                <x-atomic.atoms.feedback.error-message :message="$message" />
            @enderror
        </x-atomic.molecules.forms.form-field-group>
    </x-atomic.organisms.modals.form-modal>
</div>