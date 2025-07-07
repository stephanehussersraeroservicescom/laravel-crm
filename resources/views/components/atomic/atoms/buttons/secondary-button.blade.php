@props([
    'type' => 'button',
    'size' => 'default', // small, default, large
    'disabled' => false,
    'variant' => 'default', // default, gray
])

@php
$sizeClasses = match($size) {
    'small' => 'px-3 py-1 text-sm',
    'large' => 'px-6 py-3 text-base',
    default => 'px-4 py-2',
};

$variantClasses = match($variant) {
    'gray' => 'bg-gray-500 hover:bg-gray-600 text-white',
    default => 'bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300',
};

$baseClasses = 'rounded-lg transition-colors inline-flex items-center font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2';

$disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';

$classes = trim("{$baseClasses} {$sizeClasses} {$variantClasses} {$disabledClasses}");
@endphp

<button 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => $classes]) }}
    @if($disabled) disabled @endif
>
    {{ $slot }}
</button>