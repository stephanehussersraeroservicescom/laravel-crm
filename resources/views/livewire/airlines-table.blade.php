<div class="space-y-6">
    <!-- Header -->
    <x-atomic.molecules.navigation.page-header title="Airlines Management">
        <x-slot name="actions">
            @if($editing)
                <x-atomic.atoms.buttons.secondary-button variant="gray" wire:click="cancelEdit">
                    Cancel
                </x-atomic.atoms.buttons.secondary-button>
            @else
                <x-atomic.atoms.buttons.primary-button wire:click="openAddForm">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Airline
                </x-atomic.atoms.buttons.primary-button>
            @endif
        </x-slot>
    </x-atomic.molecules.navigation.page-header>

    <!-- Flash Messages -->
    <x-atomic.atoms.feedback.flash-message type="success" :message="session('message')" />
    <x-atomic.atoms.feedback.flash-message type="error" :message="session('error')" />

    <!-- Add/Edit Form -->
    @if($editing)
        <div class="w-full mx-auto bg-white p-8 rounded-lg shadow-sm border-2 border-gray-400 md:max-w-[90%]">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">{{ $editId ? 'Edit Airline' : 'Add New Airline' }}</h2>
            
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-atomic.molecules.forms.form-field-group 
                        label="Airline Name" 
                        name="name" 
                        :required="true"
                    >
                        <x-atomic.atoms.forms.form-input 
                            type="text" 
                            wire:model.live="name" 
                            required 
                        />
                    </x-atomic.molecules.forms.form-field-group>
                    
                    <x-atomic.molecules.forms.form-field-group 
                        label="Region" 
                        name="region" 
                        :required="true"
                    >
                        <x-atomic.atoms.forms.form-select wire:model.live="region" required>
                            <option value="">Select Region...</option>
                            @foreach($availableRegions as $regionOption)
                                <option value="{{ $regionOption }}">{{ $regionOption }}</option>
                            @endforeach
                        </x-atomic.atoms.forms.form-select>
                    </x-atomic.molecules.forms.form-field-group>
                    
                    <x-atomic.molecules.forms.form-field-group 
                        label="Account Executive" 
                        name="account_executive_id"
                    >
                        <x-atomic.atoms.forms.form-select wire:model.live="account_executive_id">
                            <option value="">Select Account Executive...</option>
                            @foreach($salesUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </x-atomic.atoms.forms.form-select>
                    </x-atomic.molecules.forms.form-field-group>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <x-atomic.atoms.buttons.secondary-button type="button" wire:click="cancelEdit">
                        Cancel
                    </x-atomic.atoms.buttons.secondary-button>
                    <x-atomic.atoms.buttons.primary-button type="submit">
                        {{ $editId ? 'Update' : 'Add' }} Airline
                    </x-atomic.atoms.buttons.primary-button>
                </div>
            </form>
        </div>
    @endif

    <!-- Filter Panel -->
    <div class="w-full mx-auto bg-white p-8 rounded-lg shadow-sm border-2 border-gray-400 md:max-w-[90%]">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
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
        </div>

        <div class="flex justify-between items-center">
            <!-- Show Deleted Checkbox -->
            <label class="flex items-center">
                <input type="checkbox" wire:model.live="showDeleted" 
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Show deleted airlines</span>
            </label>
            
            <x-atomic.atoms.buttons.secondary-button variant="gray" wire:click="clearFilters">
                Clear Filters
            </x-atomic.atoms.buttons.secondary-button>
        </div>
    </div>

    <!-- Airlines Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Account Executive</th>
                        @if($showDeleted)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Status</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($airlines as $airline)
                        <tr class="hover:bg-gray-300 {{ $airline->trashed() ? 'bg-red-50 opacity-75' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $airline->name }}</div>
                                @if($airline->trashed())
                                    <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Deleted
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $airline->region }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                <div class="text-sm text-gray-900">{{ $airline->accountExecutive?->name ?: 'Not assigned' }}</div>
                            </td>
                            @if($showDeleted)
                                <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                    @if($airline->trashed())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Deleted {{ $airline->deleted_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                @if($airline->trashed())
                                    <x-atomic.atoms.buttons.action-link 
                                        variant="success" 
                                        wire:click="restore({{ $airline->id }})" 
                                        onclick="return confirm('Are you sure you want to restore this airline?')"
                                    >
                                        Restore
                                    </x-atomic.atoms.buttons.action-link>
                                @else
                                    <x-atomic.atoms.buttons.action-link 
                                        variant="primary" 
                                        wire:click="edit({{ $airline->id }})"
                                    >
                                        Edit
                                    </x-atomic.atoms.buttons.action-link>
                                    <x-atomic.atoms.buttons.action-link 
                                        variant="danger" 
                                        wire:click="delete({{ $airline->id }})" 
                                        onclick="return confirm('Are you sure you want to delete this airline?')"
                                    >
                                        Delete
                                    </x-atomic.atoms.buttons.action-link>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No airlines found. 
                                <x-atomic.atoms.buttons.action-link variant="primary" wire:click="$toggle('editing')">
                                    Create your first airline
                                </x-atomic.atoms.buttons.action-link>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>