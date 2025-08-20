<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
    <!-- Error Messages -->
    @if (session()->has('error'))
        <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
            {{ session('error') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="save">
        <div class="p-6">
            <!-- Customer Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Contact Search/Autocomplete -->
                    <div class="lg:col-span-2 relative">
                        <label for="contact_search" class="block text-sm font-medium text-gray-700">Search Existing Customer</label>
                        <input type="text" 
                               wire:model.live="contact_search"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Type airline, subcontractor, or customer name...">
                        
                        @if($show_contact_dropdown && count($filtered_contacts) > 0)
                            <div class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none">
                                @foreach($filtered_contacts as $contact)
                                    <div wire:click="selectContact({{ json_encode($contact) }})" 
                                         class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100">
                                        <div class="font-medium">{{ $contact['display_name'] }}</div>
                                        <div class="text-gray-500 text-sm">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $contact['display_type'] }}
                                            </span>
                                            @if($contact['contact_name'])
                                                {{ $contact['contact_name'] }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name*</label>
                        <input type="text" 
                               wire:model="company_name"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                        @error('company_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Contact Name -->
                    <div>
                        <label for="contact_name" class="block text-sm font-medium text-gray-700">Contact Name*</label>
                        <input type="text" 
                               wire:model="contact_name"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                        @error('contact_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" 
                               wire:model="email"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" 
                               wire:model="phone"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Quote Details -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quote Details</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Date Entry -->
                    <div>
                        <label for="date_entry" class="block text-sm font-medium text-gray-700">Quote Date*</label>
                        <input type="date" 
                               wire:model="date_entry"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                        @error('date_entry') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Valid Until -->
                    <div>
                        <label for="date_valid" class="block text-sm font-medium text-gray-700">Valid Until*</label>
                        <input type="date" 
                               wire:model="date_valid"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                        @error('date_valid') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Payment Terms -->
                    <div>
                        <label for="payment_terms" class="block text-sm font-medium text-gray-700">Payment Terms</label>
                        <select wire:model="payment_terms"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="Pro Forma">Pro Forma</option>
                            <option value="Net 30">Net 30</option>
                            <option value="Net 60">Net 60</option>
                            <option value="Net 90">Net 90</option>
                        </select>
                    </div>

                    <!-- Shipping Terms -->
                    <div class="lg:col-span-3">
                        <label for="shipping_terms" class="block text-sm font-medium text-gray-700">Shipping Terms</label>
                        <input type="text" 
                               wire:model="shipping_terms"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Ex Works Dallas Texas">
                    </div>

                    <!-- Comments -->
                    <div class="lg:col-span-3">
                        <label for="comments" class="block text-sm font-medium text-gray-700">Internal Comments</label>
                        <textarea wire:model="comments"
                                  rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="These comments are for internal use only"></textarea>
                    </div>
                </div>
            </div>

            <!-- Quote Lines -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Quote Lines</h3>
                    <button type="button" 
                            wire:click="addQuoteLine"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Add Line
                    </button>
                </div>

                <div class="space-y-4">
                    @foreach($quote_lines as $index => $line)
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="flex justify-between items-start mb-4">
                                <h4 class="text-sm font-medium text-gray-900">Line {{ $index + 1 }}</h4>
                                @if(count($quote_lines) > 1)
                                    <button type="button" 
                                            wire:click="removeQuoteLine({{ $index }})"
                                            class="text-red-600 hover:text-red-900 text-sm">
                                        Remove
                                    </button>
                                @endif
                            </div>

                            <!-- Product Search -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search Existing Product (Optional)</label>
                                <div class="relative">
                                    <input type="text" 
                                           wire:model.live="product_searches.{{ $index }}"
                                           class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="Search by part number or color...">
                                    
                                    @if(isset($show_product_dropdowns[$index]) && $show_product_dropdowns[$index] && isset($filtered_products[$index]))
                                        <div class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none">
                                            @foreach($filtered_products[$index] as $product)
                                                <div wire:click="selectProduct({{ $index }}, {{ $product->id }})" 
                                                     class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100">
                                                    <div class="flex justify-between">
                                                        <span class="font-medium">{{ $product->part_number }} - {{ $product->color_name }}</span>
                                                        <span class="text-gray-500 text-sm">${{ number_format($product->price, 2) }}</span>
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $product->productClass->root_name ?? '' }} | MOQ: {{ $product->moq }} {{ $product->uom }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Product Class Selection -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Class</label>
                                <div class="relative">
                                    <input type="text" 
                                           wire:model.live="root_searches.{{ $index }}"
                                           class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="Search by product code or name (e.g., ULFR, UltraLeather)">
                                    
                                    @if(isset($show_root_dropdowns[$index]) && $show_root_dropdowns[$index] && isset($filtered_roots[$index]))
                                        <div class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none">
                                            @foreach($filtered_roots[$index] as $root)
                                                <div wire:click="selectRoot({{ $index }}, '{{ $root->root_code }}', {{ $root->has_ink_resist ? 'true' : 'false' }}, {{ $root->is_bio ? 'true' : 'false' }})" 
                                                     class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100">
                                                    <div class="flex justify-between">
                                                        <span class="font-medium">{{ $root->root_code }} - {{ $root->root_name }}</span>
                                                        <span class="text-gray-500 text-sm">MOQ: {{ $root->moq_ly }} LY</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-6 gap-4">
                                <!-- Part Number -->
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Part Number*</label>
                                    <input type="text" 
                                           wire:model.live="quote_lines.{{ $index }}.part_number"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="e.g., ULFR924-5991.BC3"
                                           required>
                                    @error("quote_lines.{$index}.part_number") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Color Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Color Name</label>
                                    <input type="text" 
                                           wire:model.live="quote_lines.{{ $index }}.color_name"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="e.g., Navy Blue">
                                </div>

                                <!-- Color Code -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Color Code</label>
                                    <input type="text" 
                                           wire:model.live="quote_lines.{{ $index }}.color_code"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="e.g., 924">
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Quantity*</label>
                                    <input type="number" 
                                           wire:model.live="quote_lines.{{ $index }}.quantity"
                                           min="1"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           required>
                                    @error("quote_lines.{$index}.quantity") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    @if(isset($line['quantity']) && isset($line['moq']) && $line['quantity'] !== '' && $line['quantity'] !== null && is_numeric($line['quantity']) && $line['quantity'] < $line['moq'] && !($line['moq_waived'] ?? false))
                                        <span class="text-amber-600 text-xs">Below MOQ of {{ $line['moq'] }}</span>
                                    @endif
                                </div>

                                <!-- Unit -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Unit*</label>
                                    <select wire:model="quote_lines.{{ $index }}.unit"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required>
                                        <option value="LY">LY</option>
                                        <option value="UNIT">UNIT</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mt-4">
                                <!-- Description -->
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Description*</label>
                                    <input type="text" 
                                           wire:model.live="quote_lines.{{ $index }}.description"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           required>
                                    @error("quote_lines.{$index}.description") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Price -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Price (USD)*
                                        @if($line['pricing_source'] !== 'manual')
                                            <span class="text-xs text-gray-500">({{ ucfirst($line['pricing_source']) }})</span>
                                        @endif
                                    </label>
                                    <input type="number" 
                                           step="0.01"
                                           wire:model.live="quote_lines.{{ $index }}.final_price"
                                           min="0"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           required>
                                    @error("quote_lines.{$index}.final_price") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Line Total -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Line Total</label>
                                    <input type="text" 
                                           value="${{ number_format(((float)($line['quantity'] ?: 0)) * ((float)($line['final_price'] ?: 0)), 2) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50"
                                           readonly>
                                </div>
                            </div>

                            <!-- MOQ and Lead Time Info -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">MOQ</label>
                                    <input type="number" 
                                           wire:model.live="quote_lines.{{ $index }}.moq"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           min="1">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Lead Time</label>
                                    <input type="text" 
                                           wire:model.live="quote_lines.{{ $index }}.lead_time"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="e.g., 6-8 weeks">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                                    <input type="text" 
                                           wire:model.live="quote_lines.{{ $index }}.notes"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Quote Total -->
                <div class="mt-6 bg-gray-100 p-4 rounded-lg">
                    <div class="flex justify-between items-center text-lg font-medium">
                        <span>Quote Total:</span>
                        <span>${{ number_format(collect($quote_lines)->sum(function($line) { return ((float)($line['quantity'] ?: 0)) * ((float)($line['final_price'] ?: 0)); }), 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Create Quote
                </button>
            </div>
        </div>
    </form>
</div>

<!-- MOQ Warning Modal -->
<script>
    window.addEventListener('confirm-moq-waiver', event => {
        if (confirm(`The quantity (${event.detail.quantity}) is below the MOQ of ${event.detail.moq}. Do you want to proceed?`)) {
            @this.confirmMOQWaiver(event.detail.index);
        }
    });
</script>