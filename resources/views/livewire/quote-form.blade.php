<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
    <form wire:submit="save">
        <div class="p-6">
            <!-- Customer Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Contact Search/Autocomplete -->
                    <div class="lg:col-span-2 relative">
                        <label for="contact_search" class="block text-sm font-medium text-gray-700">Search Existing Contact</label>
                        <input type="text" 
                               wire:model.live="contact_search"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Type company or contact name...">
                        
                        @if($show_contact_dropdown && count($filtered_contacts) > 0)
                            <div class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none">
                                @foreach($filtered_contacts as $contact)
                                    <div wire:click="selectContact({{ $contact->id }})" 
                                         class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100">
                                        <div class="font-medium">{{ $contact->company_name }}</div>
                                        <div class="text-gray-500 text-sm">{{ $contact->contact_name }}</div>
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
                        <input type="text" 
                               wire:model="phone"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Airline (Optional) -->
                    <div>
                        <label for="airline_id" class="block text-sm font-medium text-gray-700">Associated Airline (Optional)</label>
                        <select wire:model="airline_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select an airline...</option>
                            @foreach($airlines as $airline)
                                <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subcontractor Toggle -->
                    <div class="flex items-center">
                        <input type="checkbox" 
                               wire:model="is_subcontractor"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_subcontractor" class="ml-2 block text-sm text-gray-900">This is a subcontractor quote</label>
                    </div>
                </div>
            </div>

            <!-- Quote Details -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quote Details</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Date Entry -->
                    <div>
                        <label for="date_entry" class="block text-sm font-medium text-gray-700">Quote Date*</label>
                        <input type="date" 
                               wire:model="date_entry"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                        @error('date_entry') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Date Valid -->
                    <div>
                        <label for="date_valid" class="block text-sm font-medium text-gray-700">Valid Until*</label>
                        <input type="date" 
                               wire:model="date_valid"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                        @error('date_valid') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Shipping Terms -->
                    <div>
                        <label for="shipping_terms" class="block text-sm font-medium text-gray-700">Shipping Terms</label>
                        <input type="text" 
                               wire:model="shipping_terms"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Payment Terms -->
                    <div>
                        <label for="payment_terms" class="block text-sm font-medium text-gray-700">Payment Terms</label>
                        <input type="text" 
                               wire:model="payment_terms"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Comments -->
                    <div class="lg:col-span-2">
                        <label for="comments" class="block text-sm font-medium text-gray-700">Comments</label>
                        <textarea wire:model="comments"
                                  rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
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
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-4">
                                <h4 class="font-medium text-gray-900">Line {{ $index + 1 }}</h4>
                                @if(count($quote_lines) > 1)
                                    <button type="button" 
                                            wire:click="removeQuoteLine({{ $index }})"
                                            class="text-red-600 hover:text-red-800">
                                        Remove
                                    </button>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                                <!-- Part Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Part Number*</label>
                                    <input type="text" 
                                           wire:model="quote_lines.{{ $index }}.part_number"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           required>
                                    @error("quote_lines.{$index}.part_number") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Quantity*</label>
                                    <input type="number" 
                                           wire:model="quote_lines.{{ $index }}.quantity"
                                           min="1"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           required>
                                    @error("quote_lines.{$index}.quantity") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Unit -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Unit*</label>
                                    <select wire:model="quote_lines.{{ $index }}.unit"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required>
                                        <option value="LY">Linear Yards (LY)</option>
                                        <option value="UNIT">Unit</option>
                                    </select>
                                    @error("quote_lines.{$index}.unit") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Price -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Price (USD)*</label>
                                    <input type="number" 
                                           wire:model="quote_lines.{{ $index }}.final_price"
                                           step="0.01"
                                           min="0"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           required>
                                    @error("quote_lines.{$index}.final_price") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Description*</label>
                                <textarea wire:model="quote_lines.{{ $index }}.description"
                                          rows="2"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          required></textarea>
                                @error("quote_lines.{$index}.description") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Additional fields in a row -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
                                <!-- MOQ -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">MOQ*</label>
                                    <input type="number" 
                                           wire:model="quote_lines.{{ $index }}.moq"
                                           min="1"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           required>
                                </div>

                                <!-- Lead Time -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Lead Time</label>
                                    <input type="text" 
                                           wire:model="quote_lines.{{ $index }}.lead_time"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                                    <input type="text" 
                                           wire:model="quote_lines.{{ $index }}.notes"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('quotes.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Quote
                </button>
            </div>
        </div>
    </form>

    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
            {{ session('error') }}
        </div>
    @endif
</div>