<div class="mb-4 flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
    @if($showSearch)
        <div class="flex-1 max-w-md">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="{{ $searchPlaceholder }}" 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    @endif
    
    <div class="flex items-center gap-4">
        @if($showDeleted)
            <label class="flex items-center">
                <input type="checkbox" 
                       wire:model.live="showDeleted" 
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-600">Show deleted</span>
            </label>
        @endif
        
        {{ $slot }}
    </div>
</div>