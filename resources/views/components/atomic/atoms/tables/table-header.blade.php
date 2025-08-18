@props([
    'sortable' => false,
    'field' => null,
    'currentSort' => null,
    'currentDirection' => 'asc'
])

<th {{ $attributes->merge(['class' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider' . ($sortable ? ' cursor-pointer hover:bg-gray-100' : '')]) }}
    @if($sortable && $field) wire:click="sortBy('{{ $field }}')" @endif>
    <div class="flex items-center">
        {{ $slot }}
        @if($sortable && $field && $currentSort === $field)
            @if($currentDirection === 'asc')
                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
            @else
                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            @endif
        @endif
    </div>
</th>