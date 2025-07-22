<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
    <!-- Header with Search and Filters -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex-1 flex items-center space-x-4">
                <!-- Search -->
                <div class="flex-1 max-w-lg">
                    <input wire:model.live="search" type="text" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                           placeholder="Search by company, contact, or quote number...">
                </div>
                
                <!-- Status Filter -->
                <div class="min-w-0 flex-1 max-w-xs">
                    <select wire:model.live="status" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="sent">Sent</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <a href="{{ route('quotes.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                    New Quote
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
            {{ session('message') }}
        </div>
    @endif

    <!-- Quotes Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" 
                        wire:click="sortBy('quote_number')">
                        Quote #
                        @if($sortBy === 'quote_number')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Customer
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Airline
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" 
                        wire:click="sortBy('date_entry')">
                        Date
                        @if($sortBy === 'date_entry')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Valid Until
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($quotes as $quote)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <a href="{{ route('quotes.show', $quote) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $quote->quote_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $quote->customer->company_name }}</div>
                            <div class="text-sm text-gray-500">{{ $quote->customer->contact_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $quote->airline?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $quote->date_entry->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="{{ $quote->date_valid->isPast() ? 'text-red-600' : 'text-gray-500' }}">
                                {{ $quote->date_valid->format('M j, Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $quote->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $quote->status === 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $quote->status === 'accepted' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $quote->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($quote->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $quote->total_amount_formatted }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('quotes.show', $quote) }}" 
                               class="text-blue-600 hover:text-blue-800">View</a>
                            <a href="{{ route('quotes.edit', $quote) }}" 
                               class="text-indigo-600 hover:text-indigo-800">Edit</a>
                            <a href="{{ route('quotes.preview', $quote) }}" 
                               class="text-green-600 hover:text-green-800" target="_blank">PDF</a>
                            <button wire:click="deleteQuote({{ $quote->id }})" 
                                    wire:confirm="Are you sure you want to delete this quote?"
                                    class="text-red-600 hover:text-red-800">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No quotes found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($quotes->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $quotes->links() }}
        </div>
    @endif
</div>