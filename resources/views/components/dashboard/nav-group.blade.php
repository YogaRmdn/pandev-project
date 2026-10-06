@props(['label', 'items', 'activePath'])
<div class="px-2 py-3">
    <div class="text-sidebar-foreground/60 px-2 pb-2 text-xs font-medium tracking-wide uppercase"
        x-show="!$store.sidebar.collapsed">{{ $label }}</div>
    <nav class="space-y-1">
        @foreach ($items as $item)
            <a href="{{ $item['href'] }}" title="{{ $item['title'] }}"
                @class([
                    'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground flex h-12 items-center gap-2 rounded-lg px-2 text-sm',
                    "bg-primary! text-white! hover:bg-primary/90! hover:text-white!" => $item['href'] === $activePath
                ])
                {{-- @if ($item['href'] === $activePath) bg-primary! text-white! hover:bg-primary/90! hover:text-white! @endif --}}>
                <x-lucide :name="$item['icon']" class="size-5 shrink-0" />
                <span x-show="!$store.sidebar.collapsed" x-transition.opacity.duration.150ms>{{ $item['title'] }}</span>
            </a>
        @endforeach
    </nav>
</div>
