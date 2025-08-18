@props(['highlighted' => false, 'deleted' => false])

@php
    $classes = '';
    if ($deleted) {
        $classes = 'bg-red-50';
    } elseif ($highlighted) {
        $classes = 'bg-blue-50';
    }
@endphp

<tr {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</tr>