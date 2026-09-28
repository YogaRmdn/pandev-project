@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'flex items-center '.$class]) }}>
    {{ $slot }}
</div>
