@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'mt-2 text-sm font-medium text-red-600']) }}>
        @foreach ((array) $messages as $message)
            <p>{{ $message }}</p>
        @endforeach
    </div>
@endif
