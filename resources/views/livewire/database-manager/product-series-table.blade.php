<div>
    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
            <p class="text-green-800">{{ session('message') }}</p>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Product Series Management</h3>
            <p class="mt-1 text-sm text-gray-600">Manage product series mappings and variations</p>
        </div>
        <button wire:click="create" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
            Add New Series
        </button>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <input type="text" 
               wire:model.live="search" 
               placeholder="Search by series code, name, or root code..."
               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <!-- Table -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Series Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Root Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Series Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Features</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Series</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($series as $item)
                    <tr class="hover:bg-gray-50">
                        @if($editingId === $item->id)
                            <!-- Edit Mode -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" 
                                       wire:model="editForm.series_code"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                @error('editForm.series_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select wire:model="editForm.root_code"
                                        class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="">Select Root</option>
                                    @foreach($productRoots as $root)
                                        <option value="{{ $root->root_code }}">{{ $root->root_code }} - {{ $root->root_name }}</option>
                                    @endforeach
                                </select>
                                @error('editForm.root_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" 
                                       wire:model="editForm.series_name"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                @error('editForm.series_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="editForm.has_ink_resist" class="mr-2">
                                        <span class="text-sm">Ink Resist</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="editForm.is_bio" class="mr-2">
                                        <span class="text-sm">Bio</span>
                                    </label>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" 
                                       wire:model="editForm.base_series"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="save" class="text-green-600 hover:text-green-900">Save</button>
                                <button wire:click="cancel" class="text-gray-600 hover:text-gray-900">Cancel</button>
                            </td>
                        @elseif($editingId === 'new')
                            <!-- Create Mode -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" 
                                       wire:model="editForm.series_code"
                                       placeholder="Series code"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                @error('editForm.series_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select wire:model="editForm.root_code"
                                        class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="">Select Root</option>
                                    @foreach($productRoots as $root)
                                        <option value="{{ $root->root_code }}">{{ $root->root_code }} - {{ $root->root_name }}</option>
                                    @endforeach
                                </select>
                                @error('editForm.root_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" 
                                       wire:model="editForm.series_name"
                                       placeholder="Series name"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                @error('editForm.series_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="editForm.has_ink_resist" class="mr-2">
                                        <span class="text-sm">Ink Resist</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="editForm.is_bio" class="mr-2">
                                        <span class="text-sm">Bio</span>
                                    </label>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" 
                                       wire:model="editForm.base_series"
                                       placeholder="Base series"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="store" class="text-green-600 hover:text-green-900">Create</button>
                                <button wire:click="cancel" class="text-gray-600 hover:text-gray-900">Cancel</button>
                            </td>
                        @else
                            <!-- View Mode -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->series_code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->root_code }}
                                @if($item->productRoot)
                                    <div class="text-xs text-gray-400">{{ $item->productRoot->root_name }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->series_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="space-y-1">
                                    @if($item->has_ink_resist)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Ink Resist
                                        </span>
                                    @endif
                                    @if($item->is_bio)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Bio
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->base_series ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="edit({{ $item->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button wire:click="delete({{ $item->id }})" 
                                        onclick="return confirm('Are you sure you want to delete this series?')"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            @if($search)
                                No series found matching "{{ $search }}"
                            @else
                                No product series found. <button wire:click="create" class="text-indigo-600 hover:text-indigo-900">Add the first one</button>
                            @endif
                        </td>
                    </tr>
                @endforelse

                @if($editingId === 'new' && $series->count() > 0)
                    <!-- New Row -->
                    <tr class="bg-gray-50 border-2 border-indigo-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="text" 
                                   wire:model="editForm.series_code"
                                   placeholder="Series code"
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            @error('editForm.series_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select wire:model="editForm.root_code"
                                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="">Select Root</option>
                                @foreach($productRoots as $root)
                                    <option value="{{ $root->root_code }}">{{ $root->root_code }} - {{ $root->root_name }}</option>
                                @endforeach
                            </select>
                            @error('editForm.root_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="text" 
                                   wire:model="editForm.series_name"
                                   placeholder="Series name"
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            @error('editForm.series_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="space-y-1">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="editForm.has_ink_resist" class="mr-2">
                                    <span class="text-sm">Ink Resist</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="editForm.is_bio" class="mr-2">
                                    <span class="text-sm">Bio</span>
                                </label>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="text" 
                                   wire:model="editForm.base_series"
                                   placeholder="Base series"
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <button wire:click="store" class="text-green-600 hover:text-green-900">Create</button>
                            <button wire:click="cancel" class="text-gray-600 hover:text-gray-900">Cancel</button>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $series->links() }}
    </div>
</div>