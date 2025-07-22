<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Quote') }} #{{ $quote->quote_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('quotes.edit', $quote) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('quotes.preview', $quote) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                    {{ __('PDF Preview') }}
                </a>
                <a href="{{ route('quotes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                    {{ __('Back to Quotes') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Quote Header -->
                    <div class="mb-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                                <div class="space-y-2">
                                    <p><span class="font-medium">Company:</span> {{ $quote->customer->company_name }}</p>
                                    <p><span class="font-medium">Contact:</span> {{ $quote->customer->contact_name }}</p>
                                    @if($quote->customer->email)
                                        <p><span class="font-medium">Email:</span> {{ $quote->customer->email }}</p>
                                    @endif
                                    @if($quote->customer->phone)
                                        <p><span class="font-medium">Phone:</span> {{ $quote->customer->phone }}</p>
                                    @endif
                                    @if($quote->airline)
                                        <p><span class="font-medium">Airline:</span> {{ $quote->airline->name }}</p>
                                    @endif
                                    @if($quote->is_subcontractor)
                                        <p><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Subcontractor</span></p>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Quote Details</h3>
                                <div class="space-y-2">
                                    <p><span class="font-medium">Quote #:</span> {{ $quote->quote_number }}</p>
                                    <p><span class="font-medium">Date:</span> {{ $quote->date_entry->format('M j, Y') }}</p>
                                    <p><span class="font-medium">Valid Until:</span> 
                                        <span class="{{ $quote->date_valid->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $quote->date_valid->format('M j, Y') }}
                                        </span>
                                    </p>
                                    <p><span class="font-medium">Status:</span> 
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $quote->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $quote->status === 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $quote->status === 'accepted' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $quote->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($quote->status) }}
                                        </span>
                                    </p>
                                    <p><span class="font-medium">Created by:</span> {{ $quote->user->name }}</p>
                                    <p><span class="font-medium">Total:</span> <span class="text-lg font-bold">{{ $quote->total_amount_formatted }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="mb-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-gray-900">Shipping Terms</h4>
                                <p class="text-gray-600">{{ $quote->shipping_terms }}</p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Payment Terms</h4>
                                <p class="text-gray-600">{{ $quote->payment_terms }}</p>
                            </div>
                        </div>
                        
                        @if($quote->comments)
                            <div class="mt-4">
                                <h4 class="font-medium text-gray-900">Comments</h4>
                                <p class="text-gray-600">{{ $quote->comments }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Quote Lines -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Quote Lines</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MOQ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead Time</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($quote->quoteLines as $line)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $line->part_number }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $line->description }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $line->quantity }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $line->unit }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $line->final_price_formatted }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $line->line_total_formatted }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $line->moq }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $line->lead_time ?: '-' }}</td>
                                        </tr>
                                        @if($line->notes)
                                            <tr>
                                                <td colspan="8" class="px-6 py-2 text-sm text-gray-500 italic">Notes: {{ $line->notes }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="5" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Total:</td>
                                        <td class="px-6 py-3 text-sm font-bold text-gray-900">{{ $quote->total_amount_formatted }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>