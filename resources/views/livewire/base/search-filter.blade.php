<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
    <div class="flex flex-col space-y-4">
        <!-- Main Search -->
        <div class="flex items-center space-x-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm" 
                           placeholder="{{ $placeholder }}">
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <button wire:click="toggleAdvanced" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm leading-4 font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                    </svg>
                    Advanced
                </button>
                
                @if($search || array_filter($filters))
                    <button wire:click="clearFilters" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm leading-4 font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Clear
                    </button>
                @endif
            </div>
        </div>

        <!-- Advanced Filters -->
        @if($showAdvanced && !empty($availableFilters))
            <div class="border-t pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($availableFilters as $filter)
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 mb-1">
                                {{ $filter['label'] }}
                            </label>
                            
                            @if($filter['type'] === 'select')
                                <select wire:model.live="filters.{{ $filter['key'] }}" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm leading-4 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All</option>
                                    @foreach($filter['options'] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            @elseif($filter['type'] === 'date')
                                <input wire:model.live="filters.{{ $filter['key'] }}" type="date" 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm leading-4 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            @elseif($filter['type'] === 'number')
                                <input wire:model.live="filters.{{ $filter['key'] }}" type="number" 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm leading-4 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                       min="{{ $filter['min'] ?? '' }}" max="{{ $filter['max'] ?? '' }}">
                            @else
                                <input wire:model.live="filters.{{ $filter['key'] }}" type="text" 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm leading-4 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="{{ $filter['placeholder'] ?? '' }}">
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <div class="flex justify-end mt-4 space-x-2">
                    <button wire:click="applyFilters" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Apply Filters
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>