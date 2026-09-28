@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-1.5 '.$class]) }}>
    {{ $slot }}
</div>
