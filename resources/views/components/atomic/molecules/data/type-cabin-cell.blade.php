@props([
    'type' => null,
    'cabinClass' => null,
])

<div>
    <div class="font-medium text-gray-900">{{ ucfirst($type ?? 'Unknown') }}</div>
    @if($cabinClass)
        <div class="text-sm text-gray-500">
            {{ str_replace('_', ' ', ucwords($cabinClass, '_')) }}
        </div>
    @endif
</div>