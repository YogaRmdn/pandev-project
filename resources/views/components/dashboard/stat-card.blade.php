@props([
    'label',
    'value',
    'icon',
    'tone' => 'primary',
    'hint' => null,
    'href' => null,
])

@php
    $tones = [
        'primary' => ['bg' => 'bg-primary/10', 'text' => 'text-primary', 'glow' => 'bg-primary/25'],
        'success' => ['bg' => 'bg-success/10', 'text' => 'text-success', 'glow' => 'bg-success/25'],
        'error' => ['bg' => 'bg-error/10', 'text' => 'text-error', 'glow' => 'bg-error/25'],
        'warning' => ['bg' => 'bg-warning/10', 'text' => 'text-warning', 'glow' => 'bg-warning/25'],
        'info' => ['bg' => 'bg-info/10', 'text' => 'text-info', 'glow' => 'bg-info/25'],
    ];
    $t = $tones[$tone] ?? $tones['primary'];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    class="group relative block h-full overflow-hidden rounded-2xl border border-base-300/70 bg-base-100 p-5 shadow-sm transition-shadow hover:shadow-md">
    <div class="pointer-events-none absolute -right-7 -top-7 size-24 rounded-full {{ $t['glow'] }} blur-2xl"></div>

    <div class="relative flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="truncate text-xs font-semibold uppercase tracking-wider text-base-content/50">{{ $label }}</p>
            <p class="mt-2 truncate font-heading text-2xl font-bold tabular-nums text-base-content">{{ $value }}</p>
            @if ($hint)
                <p class="mt-1 truncate text-xs text-base-content/45">{{ $hint }}</p>
            @endif
        </div>

        <span class="grid size-12 shrink-0 place-items-center rounded-xl {{ $t['bg'] }} {{ $t['text'] }}">
            <x-lucide :name="$icon" class="size-6" />
        </span>
    </div>
</{{ $tag }}>
