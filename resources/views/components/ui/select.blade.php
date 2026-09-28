@props(['class' => ''])

<select {{ $attributes->merge(['class' => 'flex h-9 w-full items-center justify-between gap-2 rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 '.$class]) }}>
    {{ $slot }}
</select>
