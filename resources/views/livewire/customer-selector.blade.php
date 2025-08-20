<div class="relative">
    <!-- Search Input -->
    <div class="relative">
        <input
            type="text"
            wire:model.live="search"
            wire:focus="showDropdown = true"
            placeholder="Search for customer (airline, subcontractor, or existing)..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            autocomplete="off"
        />
        
        @if($selectedCustomer)
            <div class="absolute right-2 top-2">
                <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
                    {{ $selectedCustomer['type_label'] }}
                </span>
            </div>
        @endif
    </div>

    <!-- Dropdown Results -->
    @if($showDropdown && !$showNewCustomerForm)
        <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg">
            @if($customers->isNotEmpty())
                <ul class="py-1 overflow-auto max-h-60">
                    @foreach($customers as $customer)
                        <li>
                            <button
                                type="button"
                                wire:click="selectCustomer({{ $customer['id'] }}, '{{ $customer['type'] }}')"
                                class="flex items-center justify-between w-full px-4 py-2 text-left hover:bg-gray-50 focus:outline-none focus:bg-gray-50"
                            >
                                <div>
                                    <div class="font-medium text-gray-900">
                                        {{ $customer['name'] }}
                                    </div>
                                    @if(isset($customer['region']))
                                        <div class="text-sm text-gray-500">
                                            Region: {{ $customer['region'] }}
                                        </div>
                                    @endif
                                    @if(isset($customer['contact']))
                                        <div class="text-sm text-gray-500">
                                            Contact: {{ $customer['contact'] }}
                                        </div>
                                    @endif
                                </div>
                                <span class="ml-2 text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded">
                                    {{ $customer['type_label'] }}
                                </span>
                            </button>
                        </li>
                    @endforeach
                </ul>
                
                <!-- Option to add new customer -->
                <div class="border-t border-gray-200">
                    <button
                        type="button"
                        wire:click="showNewCustomer"
                        class="flex items-center w-full px-4 py-3 text-left text-blue-600 hover:bg-blue-50 focus:outline-none focus:bg-blue-50"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add "{{ $search }}" as new external customer
                    </button>
                </div>
            @else
                <div class="px-4 py-3">
                    <p class="text-gray-500">No customers found matching "{{ $search }}"</p>
                    <button
                        type="button"
                        wire:click="showNewCustomer"
                        class="mt-2 text-blue-600 hover:text-blue-800 font-medium"
                    >
                        + Add as new external customer
                    </button>
                </div>
            @endif
        </div>
    @endif

    <!-- New Customer Form -->
    @if($showNewCustomerForm)
        <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Add New External Customer</h3>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Company Name *</label>
                    <input
                        type="text"
                        wire:model="newCustomerName"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                    @error('newCustomerName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Contact Name</label>
                    <input
                        type="text"
                        wire:model="newCustomerContact"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            type="email"
                            wire:model="newCustomerEmail"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        />
                        @error('newCustomerEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input
                            type="text"
                            wire:model="newCustomerPhone"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea
                        wire:model="newCustomerAddress"
                        rows="2"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Payment Terms</label>
                    <select
                        wire:model="newCustomerPaymentTerms"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    >
                        <option value="Pro Forma">Pro Forma</option>
                        <option value="Net 30">Net 30</option>
                        <option value="Net 60">Net 60</option>
                        <option value="Net 90">Net 90</option>
                        <option value="Due on Receipt">Due on Receipt</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea
                        wire:model="newCustomerNotes"
                        rows="2"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    ></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t">
                    <button
                        type="button"
                        wire:click="cancelNewCustomer"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        wire:click="createNewCustomer"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Create Customer
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Hidden inputs to store the selected customer data -->
    @if($selectedCustomer)
        <input type="hidden" name="customer_type" value="{{ $selectedCustomerType }}" />
        <input type="hidden" name="customer_id" value="{{ $selectedCustomerId }}" />
        <input type="hidden" name="customer_name" value="{{ $selectedCustomerName }}" />
    @endif
</div>