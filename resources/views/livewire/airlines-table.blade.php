<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Airlines
        </h2>
    </x-slot>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <!-- Management Panel -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900">{{ $editing ? 'Edit Airline' : 'Add New Airline' }}</h3>
            </div>
            <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">Airline Name</label>
                    <input type="text" wire:model.live="name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">Region</label>
                    <select wire:model.live="region" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Select Region...</option>
                        @foreach($availableRegions as $regionOption)
                            <option value="{{ $regionOption }}">{{ $regionOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">Account Executive</label>
                    <select wire:model.live="account_executive" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Account Executive...</option>
                        @foreach($salesUsers as $user)
                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md px-4 py-2 transition-colors duration-200">
                        {{ $editing ? 'Update' : 'Add Airline' }}
                    </button>
                    @if($editing)
                        <button type="button" wire:click="cancelEdit" class="text-gray-500 hover:text-gray-700 font-medium underline transition-colors duration-200">Cancel</button>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- Table Controls -->
        <div class="mb-4 flex justify-end">
            <label class="flex items-center">
                <input type="checkbox" wire:model.live="showDeleted" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-600">Show deleted airlines</span>
            </label>
        </div>
        <!-- End Management Panel -->
        
        <!-- Airlines Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Account Executive</th>
                            @if($showDeleted)
                                <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Status</th>
                            @endif
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Edit</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delete</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($airlines as $airline)
                            <tr class="{{ $airline->trashed() ? 'bg-red-50' : 'hover:bg-gray-50' }} transition-colors duration-150">
                                <td class="px-3 sm:px-6 py-4">
                                    <div class="flex flex-col">
                                        <div class="text-sm font-medium text-gray-900">{{ $airline->name }}</div>
                                        @if($airline->trashed())
                                            <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit">
                                                Deleted
                                            </span>
                                        @endif
                                        <!-- Mobile-only info -->
                                        <div class="mt-1 md:hidden">
                                            @if($airline->account_executive)
                                                <div class="text-xs text-gray-500">AE: {{ $airline->account_executive }}</div>
                                            @endif
                                            @if($showDeleted)
                                                <div class="text-xs text-gray-500">
                                                    @if($airline->trashed())
                                                        Status: Deleted {{ $airline->deleted_at->diffForHumans() }}
                                                    @else
                                                        Status: Active
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $airline->region }}</div>
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                    <div class="text-sm text-gray-900">{{ $airline->account_executive ?: 'Not assigned' }}</div>
                                </td>
                                @if($showDeleted)
                                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
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
                                <td class="px-3 sm:px-6 py-4 text-sm font-medium">
                                    @if($airline->trashed())
                                        <button wire:click="restore({{ $airline->id }})" 
                                                class="text-green-600 hover:text-green-900 font-medium transition-colors duration-200">
                                            Restore
                                        </button>
                                    @else
                                        <button wire:click="edit({{ $airline->id }})" 
                                                class="text-blue-600 hover:text-blue-900 font-medium transition-colors duration-200">
                                            Edit
                                        </button>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-6 py-4 text-sm font-medium">
                                    @if($airline->trashed())
                                        <button wire:click="forceDelete({{ $airline->id }})" 
                                                class="text-red-600 hover:text-red-900 font-medium transition-colors duration-200"
                                                onclick="return confirm('This will permanently delete this airline. Are you sure?')">
                                            Delete Forever
                                        </button>
                                    @else
                                        <button wire:click="delete({{ $airline->id }})" 
                                                class="text-red-600 hover:text-red-900 font-medium transition-colors duration-200"
                                                onclick="return confirm('Are you sure you want to delete this airline?')">
                                            Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
