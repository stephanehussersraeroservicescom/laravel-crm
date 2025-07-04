<div class="space-y-1">
    @if($label)
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    @if($slot->isNotEmpty())
        {{ $slot }}
    @elseif($type === 'select')
        <select wire:model.live="{{ $name }}" 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('{{ $name }}') border-red-500 ring-red-500 @enderror"
                @if($required) required @endif>
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach($options as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    @elseif($type === 'textarea')
        <textarea wire:model.live="{{ $name }}" 
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('{{ $name }}') border-red-500 ring-red-500 @enderror"
                  rows="{{ $rows }}"
                  @if($placeholder) placeholder="{{ $placeholder }}" @endif
                  @if($required) required @endif></textarea>
    @else
        <input type="{{ $type }}" 
               wire:model.live="{{ $name }}" 
               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('{{ $name }}') border-red-500 ring-red-500 @enderror"
               @if($placeholder) placeholder="{{ $placeholder }}" @endif
               @if($required) required @endif>
    @endif
    
    @error($name)
        <div class="text-red-600 text-sm">{{ $message }}</div>
    @enderror
    
    @if($help)
        <div class="text-xs text-gray-500">{{ $help }}</div>
    @endif
</div>