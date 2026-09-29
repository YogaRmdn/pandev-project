@props(['label', 'value', 'icon'])

<x-ui.card class="h-full">
    <x-ui.card-content class="flex h-full items-center gap-4">
        <span class="bg-primary/10 text-primary inline-flex size-12 shrink-0 items-center justify-center rounded-lg">
            <x-lucide :name="$icon" class="size-6" />
        </span>
        <div class="min-w-0">
            <div class="text-muted-foreground truncate text-xs font-semibold tracking-wider uppercase">
                {{ $label }}
            </div>
            <div class="truncate font-heading text-2xl font-bold tabular-nums">{{ $value }}</div>
        </div>
    </x-ui.card-content>
</x-ui.card>
