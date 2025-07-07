@props([
    'name' => '',
    'description' => null,
    'attachmentCount' => 0,
    'descriptionLimit' => 50,
])

<div class="flex items-center space-x-2">
    <div class="flex-1">
        <div class="font-medium text-gray-900">
            {{ $name ?: 'Untitled Opportunity' }}
        </div>
        @if($description)
            <div class="text-sm text-gray-500 truncate max-w-xs">
                {{ Str::limit($description, $descriptionLimit) }}
            </div>
        @endif
    </div>
    @if($attachmentCount > 0)
        <div class="flex items-center">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
            </svg>
            <span class="text-xs text-blue-500 ml-1">{{ $attachmentCount }}</span>
        </div>
    @endif
</div>