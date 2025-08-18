@props(['message' => ''])

@if($message)
    <p {{ $attributes->merge(['class' => 'text-red-500 text-xs mt-1']) }}>
        {{ $message }}
    </p>
@endif