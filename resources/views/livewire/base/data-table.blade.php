<div class="bg-white shadow-sm rounded-lg">
    <!-- Header with Search and Controls -->
    <div class="px-4 py-3 border-b border-gray-200 sm:px-6">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input wire:model.live.debounce.300ms="search" type="text" 
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm" 
                                   placeholder="Search...">
                        </div>
                    </div>

                    <!-- Filters Toggle -->
                    <button wire:click="toggleFilters" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm leading-4 font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filters
                    </button>
                </div>
            </div>

            <!-- Per Page Selector -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Show:</label>
                    <select wire:model.live="perPage" 
                            class="block w-20 px-3 py-2 border border-gray-300 rounded-md text-sm leading-4 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        @if(!empty($selectedItems))
            <div class="mt-4 flex items-center justify-between bg-blue-50 border border-blue-200 rounded-md p-3">
                <span class="text-sm text-blue-700">{{ count($selectedItems) }} item(s) selected</span>
                <div class="flex space-x-2">
                    <button wire:click="bulkDelete" 
                            class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete Selected
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" wire:model.live="selectAll" 
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </th>
                    @foreach($columns as $column)
                        <th wire:click="sortBy('{{ $column['field'] }}')" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center space-x-1">
                                <span>{{ $column['label'] }}</span>
                                @if($column['sortable'] ?? true)
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($sortField === $column['field'])
                                            @if($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            @endif
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        @endif
                                    </svg>
                                @endif
                            </div>
                        </th>
                    @endforeach
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}" 
                                   class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        </td>
                        @foreach($columns as $column)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if(isset($column['component']))
                                    @livewire($column['component'], ['item' => $item, 'field' => $column['field']], key($item->id . '-' . $column['field']))
                                @else
                                    {{ data_get($item, $column['field']) }}
                                @endif
                            </td>
                        @endforeach
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                @can('update', $item)
                                    <button wire:click="$dispatch('showModal', 'edit', {{ $item->id }})" 
                                            class="text-blue-600 hover:text-blue-900">Edit</button>
                                @endcan
                                @can('delete', $item)
                                    <button wire:click="$dispatch('showModal', 'delete', {{ $item->id }})" 
                                            class="text-red-600 hover:text-red-900">Delete</button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 2 }}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No items found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($items->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $items->links() }}
        </div>
    @endif
</div>