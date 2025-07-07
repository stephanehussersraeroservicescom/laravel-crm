@props([
    'tag' => 'h1',
    'size' => 'default', // default, large, small
])

@php
$classes = match($size) {
    'large' => 'text-3xl font-bold text-gray-900',
    'small' => 'text-xl font-bold text-gray-900',
    default => 'text-2xl font-bold text-gray-900',
};
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $tag }}>