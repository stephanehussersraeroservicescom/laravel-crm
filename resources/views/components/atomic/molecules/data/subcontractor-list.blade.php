@props([
    'mainSubcontractor' => null,
    'supportingSubcontractors' => collect(),
    'limit' => 3,
])

<div>
    <!-- Main Subcontractor (Bold) -->
    <div class="font-bold text-gray-900 mb-1">
        {{ $mainSubcontractor ?? 'Unknown Subcontractor' }}
    </div>
    
    <!-- Supporting Subcontractors (Standard) -->
    @if($supportingSubcontractors->count() > 0)
        <div class="space-y-1">
            @foreach($supportingSubcontractors->take($limit) as $supporting)
                <div class="text-sm text-gray-600">{{ $supporting->name ?? $supporting }}</div>
            @endforeach
            @if($supportingSubcontractors->count() > $limit)
                <div class="text-xs text-gray-400">
                    +{{ $supportingSubcontractors->count() - $limit }} more
                </div>
            @endif
        </div>
    @endif
</div>