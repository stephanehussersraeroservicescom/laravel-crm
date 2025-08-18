@props(['variant' => 'default'])

@php
    $classes = match($variant) {
        'primary' => 'px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900',
        'secondary' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-500',
        'action' => 'px-6 py-4 whitespace-nowrap text-right text-sm font-medium',
        default => 'px-6 py-4 whitespace-nowrap text-sm text-gray-900'
    };
@endphp

<td {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</td>