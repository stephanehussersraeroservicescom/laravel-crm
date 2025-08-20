<div class="space-y-6">
    <!-- Header -->
    <x-atomic.molecules.navigation.page-header title="Subcontractors Management">
        <x-slot name="actions">
            <x-atomic.atoms.buttons.primary-button wire:click="openCreateModal">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Subcontractor
            </x-atomic.atoms.buttons.primary-button>
        </x-slot>
    </x-atomic.molecules.navigation.page-header>

    <!-- Page Description -->
    <p class="text-gray-600 -mt-2 mb-6 text-center">Manage subcontractor entities and their hierarchical relationships. Create parent-child relationships between companies for organizational structure.</p>

    <!-- Flash Messages -->
    <x-atomic.atoms.feedback.flash-message type="success" :message="session('message')" />
    

    <!-- Filter Panel -->
    <x-atomic.organisms.filters.filter-panel>
        <x-slot name="search">
            <!-- Search -->
            <x-atomic.molecules.forms.search-field 
                span="wide"
                label="Search"
                placeholder="Search subcontractors..."
                wire:model.live.debounce.300ms="search"
            />
            
            <!-- Comment Filter -->
            <x-atomic.molecules.forms.form-field-group label="Comment Filter">
                <x-atomic.atoms.forms.form-input 
                    wire:model.live.debounce.300ms="commentFilter" 
                    placeholder="Filter by comment..."
                />
            </x-atomic.molecules.forms.form-field-group>
        </x-slot>

        <x-slot name="filters">
            <!-- Show Deleted -->
            <x-atomic.molecules.forms.form-field-group label="Status">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="showDeleted" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Show deleted</span>
                </label>
            </x-atomic.molecules.forms.form-field-group>
            
            <!-- Per Page Selection -->
            <x-atomic.molecules.forms.form-field-group label="Per Page">
                <x-atomic.atoms.forms.form-select wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
        </x-slot>

        <x-slot name="actions">
            @if($search || $commentFilter || $showDeleted)
                <x-atomic.atoms.buttons.secondary-button wire:click="clearFilters">
                    Clear Filters
                </x-atomic.atoms.buttons.secondary-button>
            @endif
        </x-slot>
    </x-atomic.organisms.filters.filter-panel>

    <!-- Subcontractors Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('name')">
                            <div class="flex items-center space-x-1">
                                <span>Name</span>
                                @if($sortField === 'name')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        @endif
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell cursor-pointer hover:bg-gray-100" wire:click="sortBy('comment')">
                            <div class="flex items-center space-x-1">
                                <span>Comment</span>
                                @if($sortField === 'comment')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        @endif
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Parent Companies</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacts</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subcontractors->items() as $subcontractor)
                        <tr class="{{ $subcontractor->trashed() ? 'bg-red-50' : 'hover:bg-gray-50' }} transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <div class="text-sm font-medium text-gray-900">{{ $subcontractor->name }}</div>
                                    @if($subcontractor->trashed())
                                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit">
                                            Deleted {{ $subcontractor->deleted_at->diffForHumans() }}
                                        </span>
                                    @endif
                                    <!-- Mobile-only info -->
                                    <div class="mt-1 md:hidden">
                                        @if($subcontractor->comment)
                                            <div class="text-xs text-gray-500">{{ $subcontractor->comment }}</div>
                                        @endif
                                        @if($subcontractor->parents->count() > 0)
                                            <div class="text-xs text-gray-500 mt-1">
                                                Parents: {{ $subcontractor->parents->pluck('name')->join(', ') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 hidden md:table-cell">
                                <div class="text-sm text-gray-900">{{ $subcontractor->comment ?? '' }}</div>
                            </td>
                            
                            <td class="px-6 py-4 hidden lg:table-cell">
                                @if($subcontractor->parents->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($subcontractor->parents as $parent)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $parent->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-blue-600 font-medium">
                                    {{ $subcontractor->contacts->count() }} contact{{ $subcontractor->contacts->count() === 1 ? '' : 's' }}
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-sm font-medium space-x-2">
                                @if($subcontractor->trashed())
                                    <button wire:click="restore({{ $subcontractor->id }})" 
                                            class="text-green-600 hover:text-green-900 transition-colors">
                                        Restore
                                    </button>
                                @else
                                    <button wire:click="openEditModal({{ $subcontractor->id }})" 
                                            class="text-blue-600 hover:text-blue-900 transition-colors">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $subcontractor->id }})" 
                                            onclick="return confirm('Are you sure you want to delete this subcontractor?')"
                                            class="text-red-600 hover:text-red-900 transition-colors">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No subcontractors found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="mt-6">
        {{ $subcontractors->links() }}
    </div>

    <!-- Create/Edit Modal -->
    <x-atomic.organisms.modals.form-modal 
        :show="$showModal"
        :title="$editingSubcontractor ? 'Edit Subcontractor' : 'Create New Subcontractor'"
        :submitText="$editingSubcontractor ? 'Update' : 'Create'"
        wire:submit.prevent="save">
        
        <!-- Name Field -->
        <x-atomic.molecules.forms.form-field-group label="Subcontractor Name" required>
            <x-atomic.atoms.forms.form-input 
                wire:model="form.name" 
                placeholder="Enter subcontractor name"
                required />
            @error('form.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </x-atomic.molecules.forms.form-field-group>

        <!-- Comment Field -->
        <x-atomic.molecules.forms.form-field-group label="Comment">
            <x-atomic.atoms.forms.form-textarea 
                wire:model="form.comment" 
                placeholder="Optional comment about this subcontractor"
                rows="3" />
            @error('form.comment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </x-atomic.molecules.forms.form-field-group>

        <!-- Parent Companies -->
        <x-atomic.molecules.forms.form-field-group label="Parent Companies">
            <x-atomic.atoms.forms.form-select 
                wire:model="form.selectedParents"
                multiple
                class="h-32">
                @foreach($availableParents as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endforeach
            </x-atomic.atoms.forms.form-select>
            @error('form.selectedParents') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            <div class="text-sm text-gray-500 mt-1">
                Select parent companies if this subcontractor is a subsidiary of others (hold Ctrl/Cmd for multiple)
            </div>
        </x-atomic.molecules.forms.form-field-group>
    </x-atomic.organisms.modals.form-modal>
</div>