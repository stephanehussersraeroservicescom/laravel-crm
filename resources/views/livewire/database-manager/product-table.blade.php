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
            <h3 class="text-lg font-medium text-gray-900">Products Management</h3>
            <p class="mt-1 text-sm text-gray-600">Manage individual products with part numbers, colors, and pricing</p>
        </div>
        <div class="flex space-x-2">
            <button wire:click="create" 
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Add New Product
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Search -->
        <input type="text" 
               wire:model.live="search" 
               placeholder="Search by part number, color, or description..."
               class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
        
        <!-- Product Class Filter -->
        <select wire:model.live="rootCodeFilter" 
                class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">All Product Classes</option>
            @foreach($productClasses as $class)
                <option value="{{ $class->root_code }}">{{ $class->root_code }} - {{ $class->root_name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MOQ/UOM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quotes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customers</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $product->part_number }}
                                @if($product->description)
                                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($product->description, 40) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="font-medium">{{ $product->root_code }}</span>
                                @if($product->productClass)
                                    <div class="text-xs text-gray-500">{{ $product->productClass->root_name }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $product->color_name }}
                                @if($product->color_code)
                                    <div class="text-xs text-gray-500">({{ $product->color_code }})</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($product->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($product->moq) }} {{ $product->uom }}
                                @if($product->lead_time_weeks)
                                    <div class="text-xs text-gray-400">{{ $product->lead_time_weeks }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @php
                                    $quoteLinesCount = $product->quoteLines ? $product->quoteLines->count() : 0;
                                @endphp
                                @if($quoteLinesCount > 0)
                                    <a href="{{ route('quotes.index') }}?search={{ $product->part_number }}" 
                                       class="text-indigo-600 hover:text-indigo-900">
                                        {{ $quoteLinesCount }} quotes
                                    </a>
                                @else
                                    <span class="text-gray-400">No quotes</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @php
                                    $uniqueCustomers = 0;
                                    if ($product->quoteLines && $product->quoteLines->count() > 0) {
                                        try {
                                            $uniqueCustomers = $product->quoteLines->load('quote')->pluck('quote.customer_name')->unique()->filter()->count();
                                        } catch (Exception $e) {
                                            $uniqueCustomers = 0;
                                        }
                                    }
                                @endphp
                                @if($uniqueCustomers > 0)
                                    <span class="text-gray-900">{{ $uniqueCustomers }} customers</span>
                                @else
                                    <span class="text-gray-400">No customers</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="edit({{ $product->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button wire:click="toggleStatus({{ $product->id }})" 
                                        class="text-blue-600 hover:text-blue-900">
                                    {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                @php
                                    $canDelete = $product->quoteLines ? $product->quoteLines->count() === 0 : true;
                                @endphp
                                @if($canDelete)
                                    <button wire:click="delete({{ $product->id }})" 
                                            onclick="return confirm('Are you sure you want to delete this product?')"
                                            class="text-red-600 hover:text-red-900">Delete</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                @if($search || $rootCodeFilter)
                                    No products found matching your criteria
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
        {{ $products->links() }}
    </div>

    <!-- Edit/Create Modal -->
    @if($editingId)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="modal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $editingId === 'new' ? 'Create New Product' : 'Edit Product' }}
                    </h3>
                    
                    <form wire:submit.prevent="save" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Part Number *</label>
                                <input type="text" 
                                       wire:model="editForm.part_number"
                                       placeholder="{{ !empty($editForm['root_code']) ? 'Must start with prefix' : 'Select product class first' }}"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('editForm.part_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Product Class *</label>
                                <select wire:model.live="editForm.root_code" 
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select Product Class</option>
                                    @foreach($productClasses as $class)
                                        <option value="{{ $class->root_code }}">{{ $class->root_code }} - {{ $class->root_name }}</option>
                                    @endforeach
                                </select>
                                @error('editForm.root_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Color Name *</label>
                                <input type="text" 
                                       wire:model="editForm.color_name"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('editForm.color_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Color Code</label>
                                <input type="text" 
                                       wire:model="editForm.color_code"
                                       placeholder="e.g., #924, BLK"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Price ($) *</label>
                                <input type="number" 
                                       step="0.01"
                                       wire:model="editForm.price"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('editForm.price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">MOQ *</label>
                                <input type="number" 
                                       wire:model="editForm.moq"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('editForm.moq') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Unit of Measure</label>
                                <select wire:model="editForm.uom" 
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="LY">LY (Linear Yards)</option>
                                    <option value="UNIT">UNIT</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lead Time</label>
                                <input type="text" 
                                       wire:model="editForm.lead_time_weeks"
                                       placeholder="e.g., 6-8 weeks"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea wire:model="editForm.description" 
                                      rows="3"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" 
                                   wire:model="editForm.is_active" 
                                   class="mr-2 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label class="text-sm font-medium text-gray-700">Active</label>
                        </div>

                        <div class="flex justify-end space-x-2 pt-4 border-t">
                            <button type="button"
                                    wire:click="cancel"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ $editingId === 'new' ? 'Create' : 'Update' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>