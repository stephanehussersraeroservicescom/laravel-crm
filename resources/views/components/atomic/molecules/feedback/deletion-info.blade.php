@props([
    'deletedBy' => null,
    'deletedAt' => null,
])

@if($deletedBy || $deletedAt)
    <div class="text-xs text-gray-500 mt-1 space-y-0.5">
        @if($deletedBy)
            <div>by {{ $deletedBy }}</div>
        @endif
        @if($deletedAt)
            <div>{{ $deletedAt }}</div>
        @endif
    </div>
@endif