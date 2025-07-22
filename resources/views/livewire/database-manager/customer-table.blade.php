<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
    <!-- Header with Search and Actions -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex-1 flex items-center space-x-4">
                <div class="flex-1 max-w-lg">
                    <input wire:model.live="search" type="text" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                           placeholder="Search customers...">
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <button wire:click="create" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    Add Customer
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
            {{ session('message') }}
        </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subcontractor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- New Record Form -->
                @if($editingId === 'new')
                    <tr class="bg-blue-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">NEW</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input wire:model="editForm.company_name" type="text" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                   placeholder="Company Name">
                            @error('editForm.company_name') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input wire:model="editForm.contact_name" type="text" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                   placeholder="Contact Name">
                            @error('editForm.contact_name') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input wire:model="editForm.email" type="email" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                   placeholder="Email">
                            @error('editForm.email') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input wire:model="editForm.phone" type="text" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                   placeholder="Phone">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input wire:model="editForm.is_subcontractor" type="checkbox" 
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <button wire:click="store" class="text-green-600 hover:text-green-800">Save</button>
                            <button wire:click="cancel" class="text-gray-600 hover:text-gray-800">Cancel</button>
                        </td>
                    </tr>
                @endif

                @forelse($customers as $customer)
                    <tr class="{{ $editingId === $customer->id ? 'bg-yellow-50' : 'hover:bg-gray-50' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->id }}</td>
                        
                        <!-- Company Name -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($editingId === $customer->id)
                                <input wire:model="editForm.company_name" type="text" 
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @error('editForm.company_name') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                            @else
                                <div class="text-sm text-gray-900">{{ $customer->company_name }}</div>
                            @endif
                        </td>

                        <!-- Contact Name -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($editingId === $customer->id)
                                <input wire:model="editForm.contact_name" type="text" 
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @error('editForm.contact_name') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                            @else
                                <div class="text-sm text-gray-900">{{ $customer->contact_name }}</div>
                            @endif
                        </td>

                        <!-- Email -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($editingId === $customer->id)
                                <input wire:model="editForm.email" type="email" 
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @error('editForm.email') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                            @else
                                <div class="text-sm text-gray-500">{{ $customer->email ?? '-' }}</div>
                            @endif
                        </td>

                        <!-- Phone -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($editingId === $customer->id)
                                <input wire:model="editForm.phone" type="text" 
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @else
                                <div class="text-sm text-gray-500">{{ $customer->phone ?? '-' }}</div>
                            @endif
                        </td>

                        <!-- Subcontractor -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($editingId === $customer->id)
                                <input wire:model="editForm.is_subcontractor" type="checkbox" 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $customer->is_subcontractor ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $customer->is_subcontractor ? 'Yes' : 'No' }}
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            @if($editingId === $customer->id)
                                <button wire:click="save" class="text-green-600 hover:text-green-800">Save</button>
                                <button wire:click="cancel" class="text-gray-600 hover:text-gray-800">Cancel</button>
                            @else
                                <button wire:click="edit({{ $customer->id }})" class="text-indigo-600 hover:text-indigo-800">Edit</button>
                                <button wire:click="delete({{ $customer->id }})" 
                                        wire:confirm="Are you sure you want to delete this customer?"
                                        class="text-red-600 hover:text-red-800">Delete</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No customers found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($customers->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $customers->links() }}
        </div>
    @endif

    <!-- Expandable Details Section -->
    @if($editingId && $editingId !== 'new')
        <div class="border-t border-gray-200 bg-gray-50 p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Additional Customer Details</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Address -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea wire:model="editForm.address" rows="3" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                </div>

                <!-- Tax ID -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tax ID</label>
                    <input wire:model="editForm.tax_id" type="text" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <!-- Payment Terms -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Payment Terms</label>
                    <input wire:model="editForm.payment_terms" type="text" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <!-- Credit Limit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Credit Limit</label>
                    <input wire:model="editForm.credit_limit" type="number" step="0.01" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <!-- Account Manager -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Account Manager</label>
                    <input wire:model="editForm.account_manager" type="text" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <!-- Checkboxes -->
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input wire:model="editForm.has_blanket_po" type="checkbox" 
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-900">Has Blanket PO</label>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea wire:model="editForm.notes" rows="3" 
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
            </div>
        </div>
    @endif
</div>