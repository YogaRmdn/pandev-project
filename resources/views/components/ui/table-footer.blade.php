@props(['class' => ''])

<tfoot {{ $attributes->merge(['class' => 'bg-muted/50 border-t font-medium '.$class]) }}>{{ $slot }}</tfoot>
