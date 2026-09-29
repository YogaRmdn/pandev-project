@props(['name', 'class' => 'size-4'])

@php
    $social = collect(App\Support\SiteContent::socials())->firstWhere('name', $name);
@endphp

@if ($social)
    <svg
        viewBox="0 0 24 24"
        fill="currentColor"
        aria-hidden="true"
        {{ $attributes->merge(['class' => $class]) }}
    >
        <path d="{{ $social['path'] }}" />
    </svg>
@endif
