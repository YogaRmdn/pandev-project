@props(['class' => ''])

<th {{ $attributes->merge(['class' => 'text-muted-foreground h-10 px-2 text-left align-middle font-medium whitespace-nowrap '.$class]) }}>{{ $slot }}</th>
