@props([
    'projectName' => null,
    'airlineName' => null,
])

<div>
    <div class="font-medium text-gray-900">{{ $projectName ?? 'No Project' }}</div>
    <div class="text-sm text-gray-500">{{ $airlineName ?? 'No Airline' }}</div>
</div>