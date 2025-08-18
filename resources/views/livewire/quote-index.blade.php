<div class="space-y-6">
    <!-- Header -->
    <x-atomic.molecules.navigation.page-header title="Quotes">
        <x-slot name="actions">
            <x-atomic.atoms.buttons.primary-button onclick="window.location.href='{{ route('quotes.create') }}'">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Quote
            </x-atomic.atoms.buttons.primary-button>
        </x-slot>
    </x-atomic.molecules.navigation.page-header>

    <!-- Flash Messages -->
    <x-atomic.atoms.feedback.flash-message type="success" :message="session('message')" />

    <!-- Filter Panel -->
    <x-atomic.organisms.filters.filter-panel>
        <x-slot name="search">
            <!-- Search -->
            <x-atomic.molecules.forms.search-field 
                span="wide"
                label="Search"
                placeholder="Search by company, contact, or quote number..."
                wire:model.live.debounce.300ms="search"
            />

            <!-- Status Filter -->
            <x-atomic.molecules.forms.form-field-group label="Status">
                <x-atomic.atoms.forms.form-select wire:model.live="status">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Rejected</option>
                </x-atomic.atoms.forms.form-select>
            </x-atomic.molecules.forms.form-field-group>
        </x-slot>

        <x-slot name="filters">
            <!-- Additional filters can be added here if needed -->
        </x-slot>

        <x-slot name="actions">
            <!-- No additional actions needed for now -->
        </x-slot>
    </x-atomic.organisms.filters.filter-panel>

    <!-- Quotes Table -->
    <x-atomic.molecules.tables.data-table>
        <x-slot name="head">
            <tr>
                <x-atomic.atoms.tables.table-header 
                    sortable 
                    field="quote_number" 
                    :currentSort="$sortBy" 
                    :currentDirection="$sortDirection">
                    Quote #
                </x-atomic.atoms.tables.table-header>
                
                <x-atomic.atoms.tables.table-header>
                    Customer
                </x-atomic.atoms.tables.table-header>
                
                <x-atomic.atoms.tables.table-header>
                    Airline
                </x-atomic.atoms.tables.table-header>
                
                <x-atomic.atoms.tables.table-header 
                    sortable 
                    field="date_entry" 
                    :currentSort="$sortBy" 
                    :currentDirection="$sortDirection">
                    Date
                </x-atomic.atoms.tables.table-header>
                
                <x-atomic.atoms.tables.table-header>
                    Valid Until
                </x-atomic.atoms.tables.table-header>
                
                <x-atomic.atoms.tables.table-header>
                    Status
                </x-atomic.atoms.tables.table-header>
                
                <x-atomic.atoms.tables.table-header>
                    Total
                </x-atomic.atoms.tables.table-header>
                
                <x-atomic.atoms.tables.table-header class="text-right">
                    Actions
                </x-atomic.atoms.tables.table-header>
            </tr>
        </x-slot>

        @forelse($quotes as $quote)
            <x-atomic.molecules.tables.table-row>
                <x-atomic.atoms.tables.table-cell variant="primary">
                    <a href="{{ route('quotes.show', $quote) }}" class="text-blue-600 hover:text-blue-800">
                        {{ $quote->quote_number }}
                    </a>
                </x-atomic.atoms.tables.table-cell>
                
                <x-atomic.atoms.tables.table-cell>
                    <div class="text-sm text-gray-900">{{ $quote->customer->company_name }}</div>
                    <div class="text-sm text-gray-500">{{ $quote->customer->contact_name }}</div>
                </x-atomic.atoms.tables.table-cell>
                
                <x-atomic.atoms.tables.table-cell variant="secondary">
                    {{ $quote->airline?->name ?? '-' }}
                </x-atomic.atoms.tables.table-cell>
                
                <x-atomic.atoms.tables.table-cell variant="secondary">
                    {{ $quote->date_entry->format('M j, Y') }}
                </x-atomic.atoms.tables.table-cell>
                
                <x-atomic.atoms.tables.table-cell variant="secondary">
                    <span class="{{ $quote->date_valid->isPast() ? 'text-red-600' : 'text-gray-500' }}">
                        {{ $quote->date_valid->format('M j, Y') }}
                    </span>
                </x-atomic.atoms.tables.table-cell>
                
                <x-atomic.atoms.tables.table-cell>
                    <x-atomic.atoms.feedback.status-badge 
                        :status="match($quote->status) {
                            'draft' => 'draft',
                            'sent' => 'info', 
                            'accepted' => 'success',
                            'rejected' => 'danger',
                            default => 'default'
                        }">
                        {{ ucfirst($quote->status) }}
                    </x-atomic.atoms.feedback.status-badge>
                </x-atomic.atoms.tables.table-cell>
                
                <x-atomic.atoms.tables.table-cell variant="primary">
                    {{ $quote->total_amount_formatted }}
                </x-atomic.atoms.tables.table-cell>
                
                <x-atomic.atoms.tables.table-cell variant="action">
                    <div class="flex space-x-2">
                        <a href="{{ route('quotes.show', $quote) }}" 
                           class="text-blue-600 hover:text-blue-900">View</a>
                        <a href="{{ route('quotes.edit', $quote) }}" 
                           class="text-indigo-600 hover:text-indigo-900">Edit</a>
                        <a href="{{ route('quotes.preview', $quote) }}" 
                           class="text-green-600 hover:text-green-900" target="_blank">PDF</a>
                        <button wire:click="deleteQuote({{ $quote->id }})" 
                                wire:confirm="Are you sure you want to delete this quote?"
                                class="text-red-600 hover:text-red-900">Delete</button>
                    </div>
                </x-atomic.atoms.tables.table-cell>
            </x-atomic.molecules.tables.table-row>
        @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                    No quotes found matching your criteria.
                </td>
            </tr>
        @endforelse

        <x-slot name="pagination">
            @if($quotes->hasPages())
                {{ $quotes->links() }}
            @endif
        </x-slot>
    </x-atomic.molecules.tables.data-table>
</div>