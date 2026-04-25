@props(['active' => false])

@php
    $classes = $active
        ? 'bg-indigo-50 text-indigo-700 shadow-sm'
        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900';
@endphp

<a {{ $attributes->merge(['class' => 'rounded-xl px-4 py-3 text-sm font-semibold transition '.$classes]) }}>
    {{ $slot }}
</a>
