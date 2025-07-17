<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Airlines
        </h2>
    </x-slot>
    <div class="py-4 max-w-4xl mx-auto">
        @if(session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        <!-- Unified Search/Create Panel -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    @if($editing)
                        Edit Airline
                    @else
                        Search Airlines
                    @endif
                </h3>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="showDeleted" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Show deleted airlines</span>
                    </label>
                </div>
            </div>
            
            @if($editing)
                <form wire:submit.prevent="save" class="flex gap-4 items-end">
                    <div>
                        <label class="block font-semibold mb-1">Airline Name</label>
                        <input type="text" wire:model.live="name" class="rounded border-gray-300" required>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Region</label>
                        <select wire:model.live="region" class="rounded border-gray-300" required>
                            <option value="">Select Region...</option>
                            @foreach($availableRegions as $regionOption)
                                <option value="{{ $regionOption }}">{{ $regionOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Account Executive</label>
                        <select wire:model.live="account_executive_id" class="rounded border-gray-300">
                            <option value="">Select Account Executive...</option>
                            @foreach($salesUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2">
                            Update Airline
                        </button>
                        <button type="button" wire:click="cancelEdit" class="ml-2 text-gray-500 underline">Cancel</button>
                    </div>
                </form>
            @else
                <div class="flex gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Airline Name</label>
                        <input type="text" wire:model.live="name" 
                               class="rounded border-gray-300" 
                               placeholder="Search or enter new airline name...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                        <select wire:model.live="region" class="rounded border-gray-300">
                            <option value="">All Regions</option>
                            @foreach($availableRegions as $regionOption)
                                <option value="{{ $regionOption }}">{{ $regionOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Executive</label>
                        <select wire:model.live="account_executive_id" class="rounded border-gray-300">
                            <option value="">All Account Executives</option>
                            @foreach($salesUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button wire:click="clearFilters" 
                                class="bg-gray-500 text-white rounded px-4 py-2">
                            Clear Search
                        </button>
                    </div>
                </div>
            @endif
        </div>
        <!-- End Unified Search/Create Panel -->
        
        @if($showCreateOption)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-medium text-blue-900">No airlines found</h4>
                        <p class="text-sm text-blue-700">Would you like to create a new airline with the name "<strong>{{ $name }}</strong>" in "<strong>{{ $region }}</strong>"?</p>
                    </div>
                    <div>
                        <button wire:click="createFromSearch" 
                                class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700">
                            Create New Airline
                        </button>
                    </div>
                </div>
            </div>
        @endif
        
        <table class="min-w-full border rounded shadow bg-white">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border">Name</th>
                    <th class="px-3 py-2 border">Region</th>
                    <th class="px-3 py-2 border">Account Executive</th>
                    @if($showDeleted)
                        <th class="px-3 py-2 border">Status</th>
                    @endif
                    <th class="px-3 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($airlines as $airline)
                <tr class="{{ $airline->trashed() ? 'bg-red-50' : '' }}">
                    <td class="px-3 py-2 border">
                        {{ $airline->name }}
                        @if($airline->trashed())
                            <span class="ml-2 inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Deleted</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 border">{{ $airline->region }}</td>
                    <td class="px-3 py-2 border">{{ $airline->accountExecutive?->name ?? 'Not assigned' }}</td>
                    @if($showDeleted)
                        <td class="px-3 py-2 border">
                            @if($airline->trashed())
                                <span class="text-red-600 text-sm">Deleted {{ $airline->deleted_at->diffForHumans() }}</span>
                            @else
                                <span class="text-green-600 text-sm">Active</span>
                            @endif
                        </td>
                    @endif
                    <td class="px-3 py-2 border">
                        @if($airline->trashed())
                            <button wire:click="restore({{ $airline->id }})" class="text-green-600 underline">Restore</button>
                        @else
                            <button wire:click="edit({{ $airline->id }})" class="text-blue-600 underline mr-2">Edit</button>
                            <button wire:click="delete({{ $airline->id }})" 
                                    class="text-red-600 underline"
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
