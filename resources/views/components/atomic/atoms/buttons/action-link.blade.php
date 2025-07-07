@props([
    'variant' => 'primary', // primary, danger, success, secondary
])

@php
$variantClasses = match($variant) {
    'primary' => 'text-blue-600 hover:text-blue-900',
    'danger' => 'text-red-600 hover:text-red-900',
    'success' => 'text-green-600 hover:text-green-900',
    'secondary' => 'text-gray-600 hover:text-gray-900',
    default => 'text-blue-600 hover:text-blue-900',
};

$classes = "transition-colors {$variantClasses}";
@endphp

<button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
    {{ $slot }}
</button>