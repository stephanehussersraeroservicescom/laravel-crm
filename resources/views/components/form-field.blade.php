<div class="space-y-1">
    @if($label)
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    {{ $slot }}
    
    @if($help)
        <div class="text-xs text-gray-500">{{ $help }}</div>
    @endif
</div>