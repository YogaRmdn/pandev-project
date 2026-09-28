@props(['class' => ''])

<span {{ $attributes->merge(['class' => 'inline-flex w-fit shrink-0 items-center justify-center gap-1 overflow-hidden rounded-md border px-2 py-0.5 text-xs font-medium whitespace-nowrap transition-[color,box-shadow] '.$class]) }}>{{ $slot }}</span>
