@props([
    'title' => null,
    'maxWidth' => true, // true for md:max-w-[90%], false for full width
])

@php
$containerClasses = $maxWidth 
    ? 'w-full mx-auto md:max-w-[90%] pt-6'
    : 'w-full pt-6';
@endphp

<div {{ $attributes->merge(['class' => $containerClasses]) }}>
    <div class="flex justify-between items-center">
        @if($title)
            <x-atomic.atoms.typography.page-title>
                {{ $title }}
            </x-atomic.atoms.typography.page-title>
        @else
            <div>
                {{ $title ?? $slot }}
            </div>
        @endif
        
        @isset($actions)
            <div class="flex items-center space-x-4">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>