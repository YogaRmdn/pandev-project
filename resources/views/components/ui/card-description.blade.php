@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'text-muted-foreground text-sm '.$class]) }}>
    {{ $slot }}
</div>
