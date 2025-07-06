<div class="space-y-6">
    <!-- Header -->
    <div class="w-full mx-auto md:max-w-[90%] pt-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Airlines Management</h1>
            @if($editing)
                <button wire:click="cancelEdit" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    Cancel
                </button>
            @else
                <button wire:click="openAddForm" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Airline
                </button>
            @endif
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Add/Edit Form -->
    @if($editing)
        <div class="w-full mx-auto bg-white p-8 rounded-lg shadow-sm border-2 border-gray-400 md:max-w-[90%]">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">{{ $editId ? 'Edit Airline' : 'Add New Airline' }}</h2>
            
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Airline Name *</label>
                        <input type="text" wire:model.live="name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Region *</label>
                        <select wire:model.live="region" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <option value="">Select Region...</option>
                            @foreach($availableRegions as $regionOption)
                                <option value="{{ $regionOption }}">{{ $regionOption }}</option>
                            @endforeach
                        </select>
                        @error('region') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Executive</label>
                        <select wire:model.live="account_executive_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Account Executive...</option>
                            @foreach($salesUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('account_executive_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="cancelEdit" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        {{ $editId ? 'Update' : 'Add' }} Airline
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Filter Panel -->
    <div class="w-full mx-auto bg-white p-8 rounded-lg shadow-sm border-2 border-gray-400 md:max-w-[90%]">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- Region Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                <select wire:model.live="filterRegion" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Regions</option>
                    @foreach($availableRegions as $region)
                        <option value="{{ $region }}">{{ $region }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Account Executive Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Account Executive</label>
                <select wire:model.live="filterAccountExecutive" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Account Executives</option>
                    @foreach($salesUsers as $user)
                        <option value="{{ $user->name }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <!-- Show Deleted Checkbox -->
            <label class="flex items-center">
                <input type="checkbox" wire:model.live="showDeleted" 
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Show deleted airlines</span>
            </label>
            
            <button wire:click="clearFilters" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                Clear Filters
            </button>
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
                                    <button wire:click="restore({{ $airline->id }})" 
                                            onclick="return confirm('Are you sure you want to restore this airline?')"
                                            class="text-green-600 hover:text-green-900 transition-colors">
                                        Restore
                                    </button>
                                @else
                                    <button wire:click="edit({{ $airline->id }})" 
                                            class="text-blue-600 hover:text-blue-900 transition-colors">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $airline->id }})" 
                                            onclick="return confirm('Are you sure you want to delete this airline?')"
                                            class="text-red-600 hover:text-red-900 transition-colors">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No airlines found. 
                                <button wire:click="$toggle('editing')" class="text-blue-600 hover:text-blue-800">
                                    Create your first airline
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>