@props([
    'type' => 'info', // info, warning, success, error
    'icon' => null,
    'title' => null,
])

@php
$classes = match($type) {
    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'error' => 'bg-red-50 border-red-200 text-red-800',
    default => 'bg-blue-50 border-blue-200 text-blue-900',
};

$defaultIcon = match($type) {
    'warning' => '⚠',
    'success' => '✓',
    'error' => '✕',
    default => '📊',
};
@endphp

<div {{ $attributes->merge(['class' => "p-4 rounded-md border {$classes}"]) }}>
    @if($title)
        <div class="text-sm font-medium flex items-center">
            <span class="mr-2">{{ $icon ?? $defaultIcon }}</span>
            {{ $title }}
        </div>
        @if($slot->isNotEmpty())
            <div class="mt-2">
                {{ $slot }}
            </div>
        @endif
    @else
        <div class="text-sm font-medium">
            <span class="mr-2">{{ $icon ?? $defaultIcon }}</span>
            {{ $slot }}
        </div>
    @endif
</div>