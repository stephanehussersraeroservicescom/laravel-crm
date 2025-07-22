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
            <h3 class="text-lg font-medium text-gray-900">Products & Pricing Management</h3>
            <p class="mt-1 text-sm text-gray-600">Manage products with inline price editing and history tracking</p>
        </div>
        <div class="flex space-x-2">
            @if(count($selectedProducts) > 0)
                <button wire:click="showBulkUpdate" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    Update Prices ({{ count($selectedProducts) }})
                </button>
            @endif
            <button wire:click="create" 
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Add New Product
            </button>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <input type="text" 
               wire:model.live="search" 
               placeholder="Search by root code, name, or category..."
               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <!-- Table -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" 
                                   wire:model="selectAll" 
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Root Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Root Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Features</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Pricing</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MOQ (LY)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($roots as $root)
                        <tr class="hover:bg-gray-50">
                            @if($editingId === $root->root_code)
                                <!-- Edit Mode -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" 
                                           wire:model="editForm.root_code"
                                           disabled
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm bg-gray-100 cursor-not-allowed">
                                    <div class="text-xs text-gray-500 mt-1">Cannot change root code</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" 
                                           wire:model="editForm.root_name"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    @error('editForm.root_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" 
                                           wire:model="editForm.part_number_prefix"
                                           placeholder="Leave empty for root code"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" 
                                           wire:model="editForm.category"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    @error('editForm.category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                                           wire:model="editForm.allowed_extensions_string"
                                           placeholder="e.g., 924, 936, 974"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <div class="text-xs text-gray-500 mt-1">Comma-separated</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" 
                                           wire:model="editForm.moq_ly"
                                           min="1"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    @error('editForm.moq_ly') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" 
                                           wire:model="editForm.lead_time_weeks"
                                           placeholder="e.g., 4-6 weeks"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="editForm.is_active" class="mr-2">
                                        <span class="text-sm">Active</span>
                                    </label>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button wire:click="save" class="text-green-600 hover:text-green-900">Save</button>
                                    <button wire:click="cancel" class="text-gray-600 hover:text-gray-900">Cancel</button>
                                </td>
                            @elseif($editingId === 'new')
                                <!-- Create Mode -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" 
                                           wire:model="editForm.root_code"
                                           placeholder="Root code"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    @error('editForm.root_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" 
                                           wire:model="editForm.root_name"
                                           placeholder="Root name"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    @error('editForm.root_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" 
                                           wire:model="editForm.part_number_prefix"
                                           placeholder="Part # prefix (optional)"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" 
                                           wire:model="editForm.category"
                                           placeholder="Category"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    @error('editForm.category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                                           wire:model="editForm.allowed_extensions_string"
                                           placeholder="e.g., 924, 936, 974"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <div class="text-xs text-gray-500 mt-1">Comma-separated</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" 
                                           wire:model="editForm.moq_ly"
                                           min="1"
                                           placeholder="MOQ"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    @error('editForm.moq_ly') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" 
                                           wire:model="editForm.lead_time_weeks"
                                           placeholder="Lead time"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="editForm.is_active" class="mr-2">
                                        <span class="text-sm">Active</span>
                                    </label>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button wire:click="store" class="text-green-600 hover:text-green-900">Create</button>
                                    <button wire:click="cancel" class="text-gray-600 hover:text-gray-900">Cancel</button>
                                </td>
                            @else
                                <!-- View Mode -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $root->root_code }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $root->root_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($root->part_number_prefix && $root->part_number_prefix !== $root->root_code)
                                        {{ $root->part_number_prefix }}
                                        <div class="text-xs text-gray-400">Custom prefix</div>
                                    @else
                                        <span class="text-gray-400">{{ $root->root_code }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $root->category }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="space-y-1">
                                        @if($root->has_ink_resist)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Ink Resist
                                            </span>
                                        @endif
                                        @if($root->is_bio)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Bio
                                            </span>
                                        @endif
                                        @if(!$root->has_ink_resist && !$root->is_bio)
                                            <span class="text-gray-400">Standard</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($root->allowed_extensions && count($root->allowed_extensions) > 0)
                                        {{ implode(', ', $root->allowed_extensions) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($root->moq_ly) }} LY
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $root->lead_time_weeks ?: '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($root->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button wire:click="edit('{{ $root->root_code }}')" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                    <button wire:click="delete('{{ $root->root_code }}')" 
                                            onclick="return confirm('Are you sure you want to delete this product root?')"
                                            class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                @if($search)
                                    No product roots found matching "{{ $search }}"
                                @else
                                    No product roots found. <button wire:click="create" class="text-indigo-600 hover:text-indigo-900">Add the first one</button>
                                @endif
                            </td>
                        </tr>
                    @endforelse

                    @if($editingId === 'new' && $roots->count() > 0)
                        <!-- New Row -->
                        <tr class="bg-gray-50 border-2 border-indigo-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" 
                                       wire:model="editForm.root_code"
                                       placeholder="Root code"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                @error('editForm.root_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" 
                                       wire:model="editForm.root_name"
                                       placeholder="Root name"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                @error('editForm.root_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" 
                                       wire:model="editForm.part_number_prefix"
                                       placeholder="Part # prefix (optional)"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" 
                                       wire:model="editForm.category"
                                       placeholder="Category"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                @error('editForm.category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
                                       wire:model="editForm.allowed_extensions_string"
                                       placeholder="e.g., 924, 936, 974"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <div class="text-xs text-gray-500 mt-1">Comma-separated</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="number" 
                                       wire:model="editForm.moq_ly"
                                       min="1"
                                       placeholder="MOQ"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                @error('editForm.moq_ly') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" 
                                       wire:model="editForm.lead_time_weeks"
                                       placeholder="Lead time"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="editForm.is_active" class="mr-2">
                                    <span class="text-sm">Active</span>
                                </label>
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
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $roots->links() }}
    </div>

    <!-- Description Section -->
    @if($editingId)
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Product Description</h4>
            <textarea wire:model="editForm.description"
                      rows="4"
                      placeholder="Enter product description..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            @error('editForm.description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    @endif
</div>