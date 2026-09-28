@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'animate-pulse rounded-md bg-muted '.$class]) }}></div>
