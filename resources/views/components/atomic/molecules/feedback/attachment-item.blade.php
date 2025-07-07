@props([
    'name' => '',
    'size' => null,
    'downloadUrl' => null,
    'canDelete' => false,
    'deleteAction' => null,
    'variant' => 'existing', // existing, preview
])

@php
$bgClasses = match($variant) {
    'preview' => 'bg-blue-50 border-blue-200',
    default => 'bg-gray-50 border-gray-200',
};
@endphp

<div class="flex items-center justify-between p-2 {{ $bgClasses }} rounded border">
    <div class="flex items-center space-x-2">
        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
        </svg>
        <span class="text-sm text-gray-700">{{ $name }}</span>
        @if($size)
            <span class="text-xs text-gray-500">({{ $size }})</span>
        @endif
    </div>
    <div class="flex items-center space-x-2">
        @if($downloadUrl)
            <a href="{{ $downloadUrl }}" target="_blank" 
               class="text-blue-600 hover:text-blue-800 text-xs">
                Download
            </a>
        @endif
        @if($canDelete && $deleteAction)
            <button type="button" 
                    {{ $attributes->merge(['class' => 'text-red-600 hover:text-red-800 text-xs']) }}
                    wire:click="{{ $deleteAction }}">
                {{ $variant === 'preview' ? 'Remove' : 'Delete' }}
            </button>
        @endif
    </div>
</div>