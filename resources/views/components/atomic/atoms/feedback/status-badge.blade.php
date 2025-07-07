@props([
    'variant' => 'default', // default, primary, success, warning, danger, info
    'size' => 'default', // small, default, large
])

@php
$sizeClasses = match($size) {
    'small' => 'px-1.5 py-0.5 text-xs',
    'large' => 'px-3 py-1.5 text-sm',
    default => 'px-2 py-1 text-xs',
};

$variantClasses = match($variant) {
    'primary' => 'bg-blue-100 text-blue-800',
    'success' => 'bg-green-100 text-green-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'danger' => 'bg-red-100 text-red-800',
    'info' => 'bg-blue-50 text-blue-700',
    'gray' => 'bg-gray-100 text-gray-800',
    default => 'bg-blue-100 text-blue-800',
};

$classes = "inline-flex items-center font-semibold rounded-full {$sizeClasses} {$variantClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>