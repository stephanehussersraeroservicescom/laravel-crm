@props([
    'value' => 0,
    'currency' => '$',
    'decimals' => 0,
    'size' => 'default', // small, default, large
])

@php
$sizeClasses = match($size) {
    'small' => 'text-xs',
    'large' => 'text-lg font-bold',
    default => 'text-sm font-medium',
};
@endphp

<div {{ $attributes->merge(['class' => "text-gray-900 {$sizeClasses}"]) }}>
    {{ $currency }}{{ number_format($value, $decimals) }}
</div>