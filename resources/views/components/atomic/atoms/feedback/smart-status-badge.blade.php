@props([
    'status' => 'unknown',
    'size' => 'default', // small, default, large
])

@php
$sizeClasses = match($size) {
    'small' => 'px-1.5 py-0.5 text-xs',
    'large' => 'px-3 py-1.5 text-sm',
    default => 'px-2 py-1 text-xs',
};

$variantClasses = match(strtolower($status)) {
    'active' => 'bg-green-100 text-green-800',
    'inactive' => 'bg-gray-100 text-gray-800',
    'pending' => 'bg-yellow-100 text-yellow-800',
    'completed' => 'bg-blue-100 text-blue-800',
    'cancelled' => 'bg-red-100 text-red-800',
    'deleted' => 'bg-red-100 text-red-800',
    default => 'bg-gray-100 text-gray-800',
};

$classes = "inline-flex items-center font-semibold rounded-full {$sizeClasses} {$variantClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ ucfirst($status) }}
</span>