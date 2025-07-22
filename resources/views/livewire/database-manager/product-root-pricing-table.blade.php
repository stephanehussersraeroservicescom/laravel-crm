<div>
    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
            <p class="text-green-800">{{ session('message') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
            <p class="text-red-800">{{ session('error') }}</p>
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
                                   wire:click="toggleSelectAll"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Features</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Standard Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MOQ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($roots as $root)
                        <tr class="hover:bg-gray-50">
                            <!-- Selection Checkbox -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" 
                                       wire:click="toggleProductSelection('{{ $root->root_code }}')"
                                       @if(in_array($root->root_code, $selectedProducts)) checked @endif
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </td>

                            <!-- Product Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $root->root_code }}</div>
                                    <div class="text-sm text-gray-500">{{ $root->root_name }}</div>
                                    <div class="text-xs text-gray-400">{{ $root->category }}</div>
                                </div>
                            </td>

                            <!-- Features -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex flex-wrap gap-1">
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
                                </div>
                            </td>

                            <!-- Current Pricing -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $currentPrice = $root->priceLists->first();
                                @endphp
                                
                                @if($editingPriceId === $root->root_code)
                                    <div class="flex items-center space-x-1">
                                        <span class="text-sm text-gray-600">$</span>
                                        <input type="number" 
                                               wire:model="priceForm.price_ly"
                                               step="0.01"
                                               class="w-24 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        <button wire:click="savePrice" class="text-green-600 hover:text-green-900">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button wire:click="cancelPrice" class="text-gray-600 hover:text-gray-900">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <button wire:click="editPrice('{{ $root->root_code }}')" 
                                            class="text-sm font-mono hover:bg-gray-100 px-3 py-1 rounded transition-colors">
                                        @if($currentPrice)
                                            ${{ number_format($currentPrice->price_ly, 2) }}
                                        @else
                                            <span class="text-gray-400">Set price</span>
                                        @endif
                                    </button>
                                @endif
                            </td>

                            <!-- MOQ -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($root->moq_ly) }} LY
                            </td>


                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="showHistory('{{ $root->root_code }}')" 
                                        class="text-blue-600 hover:text-blue-900">History</button>
                                <button wire:click="edit('{{ $root->root_code }}')" 
                                        class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button wire:click="delete('{{ $root->root_code }}')" 
                                        onclick="return confirm('Are you sure you want to delete this product?')"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                @if($search)
                                    No products found matching "{{ $search }}"
                                @else
                                    No products found. <button wire:click="create" class="text-indigo-600 hover:text-indigo-900">Add the first one</button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $roots->links() }}
    </div>

    <!-- Bulk Price Update Modal -->
    @if($showBulkPriceUpdate)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 text-center">Bulk Price Update</h3>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Price Increase (%)</label>
                        <input type="number" 
                               wire:model="bulkPriceIncrease"
                               step="0.1"
                               placeholder="e.g., 5.5 for 5.5% increase"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        @error('bulkPriceIncrease') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            This will apply to all active prices for the {{ count($selectedProducts) }} selected products.
                        </p>
                    </div>
                    <div class="flex space-x-4 mt-4">
                        <button wire:click="applyBulkPriceIncrease"
                                class="flex-1 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            Apply Increase
                        </button>
                        <button wire:click="cancelBulkUpdate"
                                class="flex-1 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Price History Modal -->
    @if($showPriceHistory)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Price History - {{ $historyRootCode }}</h3>
                        <button wire:click="closeHistory" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price (LY)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Change Source</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($priceHistory as $price)
                                    <tr class="{{ $price->is_active ? 'bg-green-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-medium text-gray-900">
                                            ${{ number_format($price->price_ly, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $price->effective_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($price->expiry_date)
                                                {{ $price->expiry_date->format('M d, Y') }}
                                            @else
                                                <span class="text-green-600">Active</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($price->is_active && $price->isCurrentlyValid())
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Current
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Archived
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $price->imported_from ?: 'Manual' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Product Create/Edit Modal -->
    @if($editingId)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-4/5 max-w-2xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $editingId === 'new' ? 'Add New Product' : 'Edit Product - ' . ($editForm['root_code'] ?? '') }}
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        @if($editingId === 'new')
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Root Code</label>
                                <input type="text" wire:model="editForm.root_code" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                                @error('editForm.root_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Root Name</label>
                            <input type="text" wire:model="editForm.root_name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                            @error('editForm.root_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Part Number Prefix</label>
                            <input type="text" wire:model="editForm.part_number_prefix" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                            @error('editForm.part_number_prefix') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">MOQ (LY)</label>
                            <input type="number" wire:model="editForm.moq_ly" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                            @error('editForm.moq_ly') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lead Time</label>
                            <input type="text" wire:model="editForm.lead_time_weeks" placeholder="e.g., 4-6 weeks" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Features</label>
                            <div class="flex space-x-6">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="editForm.has_ink_resist" class="mr-2">
                                    <span class="text-sm">Ink Resist</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="editForm.is_bio" class="mr-2">
                                    <span class="text-sm">Bio</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea wire:model="editForm.description" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                        @error('editForm.description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex space-x-4 mt-6">
                        <button wire:click="{{ $editingId === 'new' ? 'store' : 'save' }}" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            {{ $editingId === 'new' ? 'Create Product' : 'Save Changes' }}
                        </button>
                        <button wire:click="cancel" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>