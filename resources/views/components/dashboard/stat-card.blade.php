@props(['label', 'value', 'icon'])

<x-ui.card>
    <x-ui.card-content class="flex items-center gap-4">
        <div class="bg-primary/10 flex size-12 items-center justify-center rounded-lg">
            <x-lucide :name="$icon" class="text-primary size-6" />
        </div>
        <div class="min-w-0">
            <div class="text-muted-foreground truncate text-sm uppercase">{{ $label }}</div>
            <div class="truncate text-2xl font-bold">{{ $value }}</div>
        </div>
    </x-ui.card-content>
</x-ui.card>
