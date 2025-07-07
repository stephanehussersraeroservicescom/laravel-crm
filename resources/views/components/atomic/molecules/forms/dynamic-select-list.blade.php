@props([
    'label' => '',
    'addButtonText' => '+ Add Item',
    'addAction' => 'addItem',
    'removeAction' => 'removeItem',
    'items' => [],
    'wireModel' => 'items',
    'options' => [],
    'excludeIds' => [],
    'emptyText' => 'No items added yet.',
])

<div>
    <div class="flex justify-between items-center mb-2">
        <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
        <button type="button" wire:click="{{ $addAction }}" 
                class="text-blue-600 hover:text-blue-800 text-sm">
            {{ $addButtonText }}
        </button>
    </div>
    
    @if(count($items) > 0)
        <div class="space-y-2">
            @foreach($items as $index => $itemId)
                <div class="flex items-center space-x-2">
                    <select wire:model="{{ $wireModel }}.{{ $index }}" 
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Option</option>
                        @foreach($options as $option)
                            @php
                                $optionId = data_get($option, 'id', $option);
                                $optionName = data_get($option, 'name', $option);
                                $isExcluded = in_array($optionId, $excludeIds) || in_array($optionId, array_filter($items));
                                $isSelected = $optionId == $itemId;
                            @endphp
                            @if(!$isExcluded || $isSelected)
                                <option value="{{ $optionId }}" @if($isSelected) selected @endif>
                                    {{ $optionName }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <button type="button" wire:click="{{ $removeAction }}({{ $index }})" 
                            class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-sm text-gray-500 py-2">
            {{ $emptyText }}
        </div>
    @endif
</div>