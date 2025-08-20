{{-- Example of how to use the CustomerSelector component in a quote form --}}
<form wire:submit.prevent="saveQuote" class="space-y-6">
    
    {{-- Customer Selection --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Select Customer *
            </label>
            
            {{-- The CustomerSelector component --}}
            <livewire:customer-selector 
                :quote-id="$quote->id ?? null"
                :customer-type="$quote->customer_type ?? null"
                :customer-id="$quote->customer_id ?? null" 
                :customer-name="$quote->customer_name ?? null"
                wire:key="customer-selector-{{ $quote->id ?? 'new' }}"
            />
            
            {{-- Listen for customer selection events --}}
            <script>
                document.addEventListener('livewire:initialized', () => {
                    @this.on('customerSelected', (event) => {
                        // Update the parent form with selected customer data
                        @this.set('customerType', event.customer_type);
                        @this.set('customerId', event.customer_id);
                        @this.set('customerName', event.customer_name);
                        
                        // Optionally update payment terms based on customer
                        if (event.customer_type === 'App\\Models\\ExternalCustomer') {
                            @this.set('paymentTerms', 'Pro Forma');
                        } else {
                            @this.set('paymentTerms', 'Net 30');
                        }
                    });
                });
            </script>
        </div>
        
        {{-- Display selected customer info --}}
        @if($customerName)
            <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-medium text-green-800">
                        Selected: {{ $customerName }}
                        <span class="ml-2 text-xs text-green-600">
                            ({{ str_replace('App\\Models\\', '', $customerType ?? '') }})
                        </span>
                    </span>
                </div>
            </div>
        @endif
    </div>

    {{-- Rest of quote form --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quote Details</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Quote Number</label>
                <input type="text" wire:model="quoteNumber" readonly 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500 sm:text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Valid Until</label>
                <input type="date" wire:model="dateValid" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Payment Terms</label>
                <select wire:model="paymentTerms" 
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="Pro Forma">Pro Forma</option>
                    <option value="Net 30">Net 30</option>
                    <option value="Net 60">Net 60</option>
                    <option value="Net 90">Net 90</option>
                    <option value="Due on Receipt">Due on Receipt</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Shipping Terms</label>
                <input type="text" wire:model="shippingTerms" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
        </div>
    </div>

    {{-- Save button --}}
    <div class="flex justify-end">
        <button type="submit" 
                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                :disabled="!customerName">
            Save Quote
        </button>
    </div>
</form>

{{-- 
How this works:

1. The CustomerSelector component provides a searchable dropdown that finds:
   - Airlines from the airlines table
   - Subcontractors from the subcontractors table  
   - External customers from the external_customers table
   - Option to add new external customers on-the-fly

2. When a customer is selected, the component emits a 'customerSelected' event with:
   - customer_type: The model class (App\Models\Airline, App\Models\Subcontractor, or App\Models\ExternalCustomer)
   - customer_id: The ID from the respective table
   - customer_name: The display name for storage/history

3. The parent form listens for this event and updates its properties accordingly

4. The quote model stores:
   - customer_type: For polymorphic relationship
   - customer_id: For polymorphic relationship  
   - customer_name: For display and historical reference

5. Later you can access the customer via: $quote->customer (polymorphic relationship)
   Or get all quotes for a customer: $airline->quotes(), $subcontractor->quotes(), $externalCustomer->quotes()
--}}