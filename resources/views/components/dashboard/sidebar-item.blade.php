@props([
    'href' => '#',
    'label',
    'active' => false,
    'class' => '',
    'icon' => null,
])
<li class="list-none">
    <a href="{{ $href }}" @class([
        'group relative flex items-center gap-3 rounded-xl px-2.5 py-2 text-sm font-medium transition-colors',
        'is-drawer-close:tooltip is-drawer-close:tooltip-right is-drawer-close:justify-center is-drawer-close:px-0',
        $class,
        'bg-primary/10 text-primary' => $active,
        'text-base-content/70 hover:bg-base-200/80 hover:text-base-content' => ! $active,
    ]) data-tip="{{ $label }}" @if ($active) aria-current="page" @endif>
        @if ($icon)
            <span @class([
                'grid size-8 shrink-0 place-items-center rounded-lg transition-colors',
                'bg-primary text-primary-content shadow-sm shadow-primary/30' => $active,
                'text-base-content/45 group-hover:text-primary' => ! $active,
            ])>
                <x-lucide :name="$icon" class="size-[18px]" />
            </span>
        @elseif (! $slot->isEmpty())
            <span class="grid size-8 shrink-0 place-items-center">{{ $slot }}</span>
        @endif

        <span class="is-drawer-close:hidden truncate">{{ $label }}</span>
    </a>
</li>
