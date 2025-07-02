<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Airlines
        </h2>
    </x-slot>
    <div class="py-4 max-w-4xl mx-auto">
        <!-- Management Panel -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">{{ $editing ? 'Edit Airline' : 'Add New Airline' }}</h3>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="showDeleted" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Show deleted airlines</span>
                    </label>
                </div>
            </div>
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
                <select wire:model.live="account_executive" class="rounded border-gray-300">
                    <option value="">Select Account Executive...</option>
                    @foreach($salesUsers as $user)
                        <option value="{{ $user->name }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2">
                    {{ $editing ? 'Update' : 'Add Airline' }}
                </button>
                @if($editing)
                    <button type="button" wire:click="cancelEdit" class="ml-2 text-gray-500 underline">Cancel</button>
                @endif
            </div>
            </form>
        </div>
        <!-- End Management Panel -->
        
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
                    <td class="px-3 py-2 border">{{ $airline->account_executive }}</td>
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
                            <button wire:click="restore({{ $airline->id }})" class="text-green-600 underline mr-2">Restore</button>
                            <button wire:click="forceDelete({{ $airline->id }})" 
                                    class="text-red-600 underline"
                                    onclick="return confirm('This will permanently delete this airline. Are you sure?')">
                                Delete Forever
                            </button>
                        @else
                            <button wire:click="edit({{ $airline->id }})" class="text-blue-600 underline mr-2">Edit</button>
                            <button wire:click="delete({{ $airline->id }})" 
                                    class="text-red-600 underline"
                                    onclick="return confirm('Are you sure you want to delete this airline?')">
                                Delete
                            </button>
                        @endif
                        <button wire:click="delete({{ $airline->id }})" class="text-red-600 underline">Delete</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
