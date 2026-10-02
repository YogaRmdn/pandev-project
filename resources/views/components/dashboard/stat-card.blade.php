@props(['label', 'value', 'icon'])

<div class="card gap-6 border p-6 shadow-sm h-full">
    <div class="flex h-full items-center gap-4">
        <span class="bg-primary/10 text-primary inline-flex size-12 shrink-0 items-center justify-center rounded-lg">
            <x-lucide :name="$icon" class="size-6" />
        </span>
        <div class="min-w-0">
            <div class="text-base-content/60 truncate text-xs font-semibold tracking-wider uppercase">
                {{ $label }}
            </div>
            <div class="truncate font-heading text-2xl font-bold tabular-nums">{{ $value }}</div>
        </div>
    </div>
</div>
