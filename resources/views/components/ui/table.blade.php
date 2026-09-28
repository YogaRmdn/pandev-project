@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'relative w-full overflow-x-auto '.$class]) }}>
    <table {{ $attributes->only('style')->merge(['class' => 'w-full caption-bottom text-sm']) }}>
        {{ $slot }}
    </table>
</div>
