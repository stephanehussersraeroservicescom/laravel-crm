@props([
    'opportunityName' => null,
    'opportunityType' => null,
    'cabinClass' => null,
])

<div>
    <div class="font-medium text-gray-900">
        {{ $opportunityName ?: 'Untitled Opportunity' }}
    </div>
    <div class="text-sm text-gray-500">
        {{ $opportunityType ?? 'Unknown Type' }} - 
        @if($cabinClass)
            {{ $cabinClass }}
        @else
            All Classes
        @endif
    </div>
</div>