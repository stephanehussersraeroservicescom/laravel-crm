@props([
    'type' => 'success', // success, error, warning, info
    'message' => null,
    'dismissible' => false,
])

@php
$typeClasses = match($type) {
    'success' => 'bg-green-100 border-green-400 text-green-700',
    'error' => 'bg-red-100 border-red-400 text-red-700',
    'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
    'info' => 'bg-blue-100 border-blue-400 text-blue-700',
    default => 'bg-green-100 border-green-400 text-green-700',
};

$classes = "border px-4 py-3 rounded mb-4 {$typeClasses}";
@endphp

@if($message || $slot->isNotEmpty())
<div {{ $attributes->merge(['class' => $classes]) }} @if($dismissible) x-data="{ show: true }" x-show="show" @endif>
    <div class="flex items-center justify-between">
        <div class="flex-1">
            {{ $message ?? $slot }}
        </div>
        @if($dismissible)
            <button @click="show = false" class="ml-4 text-current hover:text-opacity-75">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        @endif
    </div>
</div>
@endif