<div>
    <!-- Flash Messages -->
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
            <h3 class="text-lg font-medium text-gray-900">Contract Prices Management</h3>
            <p class="mt-1 text-sm text-gray-600">Manage customer-specific and airline contract pricing</p>
        </div>
        <button wire:click="create" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
            Add New Contract Price
        </button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-4 flex space-x-4">
        <div class="flex-1">
            <input type="text" 
                   wire:model.live="search" 
                   placeholder="Search by customer, part number, root code, contract number, or airline..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="w-48">
            <select wire:model.live="filterAirline" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Airlines</option>
                @foreach($airlines as $airline)
                    <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contract Party</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contract Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Scope</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valid Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($contracts as $item)
                        <tr class="hover:bg-gray-50">
                            @if($editingId === $item->id)
                                <!-- Edit Mode -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <input type="text" 
                                               wire:model="editForm.customer_identifier"
                                               placeholder="Customer identifier"
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        @error('editForm.customer_identifier') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        
                                        <select wire:model="editForm.airline_id"
                                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            <option value="">Select Airline (optional)</option>
                                            @foreach($airlines as $airline)
                                                <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <input type="text" 
                                               wire:model="editForm.part_number"
                                               placeholder="Part number (optional)"
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        
                                        <select wire:model="editForm.root_code"
                                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            <option value="">Select Root (optional)</option>
                                            @foreach($productRoots as $root)
                                                <option value="{{ $root->root_code }}">{{ $root->root_code }} - {{ $root->root_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" 
                                           wire:model="editForm.contract_price"
                                           min="0"
                                           step="0.01"
                                           placeholder="0.00"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    @error('editForm.contract_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <div class="text-xs text-gray-500">Enter price in dollars</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <input type="date" 
                                               wire:model="editForm.valid_from"
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        <input type="date" 
                                               wire:model="editForm.valid_to"
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        @error('editForm.valid_to') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button wire:click="save" class="text-green-600 hover:text-green-900">Save</button>
                                    <button wire:click="cancel" class="text-gray-600 hover:text-gray-900">Cancel</button>
                                </td>
                            @else
                                <!-- View Mode -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="font-medium">{{ $item->customer_identifier }}</div>
                                    @if($item->airline)
                                        <div class="text-xs text-gray-500">{{ $item->airline->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($item->part_number)
                                        <div class="font-medium">{{ $item->part_number }}</div>
                                    @endif
                                    @if($item->root_code)
                                        <div class="text-xs {{ $item->part_number ? 'text-gray-400' : 'font-medium' }}">{{ $item->root_code }}</div>
                                    @endif
                                    @if(!$item->part_number && !$item->root_code)
                                        <span class="text-gray-400 italic">All products</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    ${{ number_format($item->contract_price / 100, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($item->valid_from)
                                        <div>From: {{ $item->valid_from->format('M d, Y') }}</div>
                                    @endif
                                    @if($item->valid_to)
                                        <div>To: {{ $item->valid_to->format('M d, Y') }}</div>
                                    @elseif($item->valid_from)
                                        <div class="text-green-600 text-xs">Active</div>
                                    @else
                                        <span class="text-gray-400">Always valid</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button wire:click="edit({{ $item->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                    <button wire:click="delete({{ $item->id }})" 
                                            onclick="return confirm('Are you sure you want to delete this contract price?')"
                                            class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        @if($editingId !== 'new')
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    @if($search || $filterAirline)
                                        No contract prices found matching your search criteria
                                    @else
                                        No contract prices found. <button wire:click="create" class="text-indigo-600 hover:text-indigo-900">Add the first one</button>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforelse

                    @if($editingId === 'new')
                        <!-- New Contract Form - Row 1 -->
                        <tr class="bg-blue-50 border-2 border-indigo-300">
                            <td colspan="6" class="px-6 py-4">
                                <div class="bg-white rounded-lg p-6 shadow-sm">
                                    <h4 class="text-lg font-medium text-gray-900 mb-4">Add New Contract Price</h4>
                                    
                                    <!-- Row 1: Party Information -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Identifier</label>
                                            <input type="text" 
                                                   wire:model="editForm.customer_identifier"
                                                   placeholder="Enter customer identifier"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            @error('editForm.customer_identifier') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Airline</label>
                                            <select wire:model="editForm.airline_id"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">Select Airline (optional)</option>
                                                @foreach($airlines as $airline)
                                                    <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('editForm.airline_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                            <div class="text-xs text-gray-500 mt-1">Either customer OR airline is required</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Row 2: Product Scope -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Specific Part Number</label>
                                            <input type="text" 
                                                   wire:model="editForm.part_number"
                                                   placeholder="Enter specific part number (optional)"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            @error('editForm.part_number') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Product Class (Root)</label>
                                            <select wire:model="editForm.root_code"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">Select product class (optional)</option>
                                                @foreach($productRoots as $root)
                                                    <option value="{{ $root->root_code }}">{{ $root->root_code }} - {{ $root->root_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('editForm.root_code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                            <div class="text-xs text-gray-500 mt-1">Leave both empty for all products</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Row 3: Price -->
                                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-6">
                                        <div class="md:w-1/3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Contract Price (USD)</label>
                                            <input type="number" 
                                                   wire:model="editForm.contract_price"
                                                   min="0"
                                                   step="0.01"
                                                   placeholder="0.00"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            @error('editForm.contract_price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                            <div class="text-xs text-gray-500 mt-1">Price will be effective from today. Previous prices will be automatically terminated.</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex justify-end space-x-3">
                                        <button wire:click="cancel" 
                                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Cancel
                                        </button>
                                        <button wire:click="store" 
                                                class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Create Contract Price
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $contracts->links() }}
    </div>
</div>