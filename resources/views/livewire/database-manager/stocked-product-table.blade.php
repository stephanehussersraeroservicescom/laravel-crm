<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Stocked Products</h1>
            <p class="text-gray-600">Manage list of stocked part numbers (MOQ: 5 LY for all stocked items)</p>
        </div>
        <button wire:click="create" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Add Stocked Product
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search -->
    <div class="mb-6">
        <div class="max-w-md">
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" 
                   wire:model.live.debounce.300ms="search"
                   placeholder="Search part numbers, root codes, product names..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Root Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MOQ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr>
                            @if($editingId === $product->id)
                                <!-- Edit Form Row -->
                                <td class="px-6 py-4">
                                    <input type="text" 
                                           wire:model="editForm.full_part_number"
                                           class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('editForm.full_part_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-6 py-4">
                                    <select wire:model="editForm.root_code" 
                                            class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Root</option>
                                        @foreach($productRoots as $root)
                                            <option value="{{ $root->root_code }}">{{ $root->root_code }}</option>
                                        @endforeach
                                    </select>
                                    @error('editForm.root_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $product->productRoot->root_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">5 LY</td>
                                <td class="px-6 py-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               wire:model="editForm.is_exotic"
                                               class="mr-2">
                                        <span class="text-sm">Exotic</span>
                                    </label>
                                </td>
                                <td class="px-6 py-4">
                                    <textarea wire:model="editForm.notes"
                                              rows="2"
                                              placeholder="Optional notes"
                                              class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <button wire:click="save" 
                                                class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                            Save
                                        </button>
                                        <button wire:click="cancel" 
                                                class="px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                            Cancel
                                        </button>
                                    </div>
                                </td>
                            @else
                                <!-- Display Row -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $product->full_part_number }}
                                    @if($product->is_exotic)
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Exotic
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $product->root_code }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $product->productRoot->root_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        5 LY
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $product->is_exotic ? 'Exotic' : 'Standard' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $product->notes ?: '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button wire:click="edit({{ $product->id }})" 
                                                class="text-blue-600 hover:text-blue-900">
                                            Edit
                                        </button>
                                        <button wire:click="delete({{ $product->id }})" 
                                                wire:confirm="Are you sure you want to remove this part number from the stocked list?"
                                                class="text-red-600 hover:text-red-900">
                                            Remove
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        @if($editingId === 'new')
                            <tr>
                                <td class="px-6 py-4">
                                    <input type="text" 
                                           wire:model="editForm.full_part_number"
                                           placeholder="Enter full part number"
                                           class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('editForm.full_part_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-6 py-4">
                                    <select wire:model="editForm.root_code" 
                                            class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Root</option>
                                        @foreach($productRoots as $root)
                                            <option value="{{ $root->root_code }}">{{ $root->root_code }}</option>
                                        @endforeach
                                    </select>
                                    @error('editForm.root_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">-</td>
                                <td class="px-6 py-4 text-sm text-gray-500">5 LY</td>
                                <td class="px-6 py-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               wire:model="editForm.is_exotic"
                                               class="mr-2">
                                        <span class="text-sm">Exotic</span>
                                    </label>
                                </td>
                                <td class="px-6 py-4">
                                    <textarea wire:model="editForm.notes"
                                              rows="2"
                                              placeholder="Optional notes"
                                              class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <button wire:click="store" 
                                                class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                            Add
                                        </button>
                                        <button wire:click="cancel" 
                                                class="px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                            Cancel
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-500 mb-4">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-lg font-medium">No stocked products defined yet</p>
                                        <p class="text-sm">Start building your stocked inventory list to enable 5 LY MOQ for specific part numbers</p>
                                    </div>
                                    <button wire:click="create" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        Add First Stocked Product
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($products->hasPages())
        <!-- Pagination -->
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif

    <!-- Info Box -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Stocked Products Information</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>• All stocked products automatically have a MOQ of 5 LY</p>
                    <p>• This overrides the standard MOQ from the product root pricing</p>
                    <p>• Use this list to track which part numbers are kept in inventory</p>
                </div>
            </div>
        </div>
    </div>
</div>