@php
    $user = auth()->user();

    $groups = [
        [
            'label' => 'Menu Utama',
            'items' => [
                ['title' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard', 'patterns' => ['dashboard']],
                ['title' => 'Portfolio', 'route' => 'dashboard.portfolio.index', 'icon' => 'briefcase', 'patterns' => ['dashboard.portfolio.*']],
            ],
        ],
    ];

    // Layar administrasi hanya relevan (dan hanya bisa diakses) oleh admin.
    if ($user?->isAdmin()) {
        $groups[] = [
            'label' => 'Administrasi',
            'items' => [
                ['title' => 'Manajemen User', 'route' => 'dashboard.users.index', 'icon' => 'users', 'patterns' => ['dashboard.users.*']],
                ['title' => 'Keuangan', 'route' => 'dashboard.finance.index', 'icon' => 'wallet', 'patterns' => ['dashboard.finance.*']],
                ['title' => 'Faktur', 'route' => 'dashboard.invoice.index', 'icon' => 'notepad-text', 'patterns' => ['dashboard.invoice.*']],
            ],
        ];
    }

    $accountItems = [
        ['title' => 'Pengaturan', 'route' => 'settings.edit', 'icon' => 'settings', 'patterns' => ['settings.*']],
    ];
@endphp

@props([
    'id' => 'dashboard-drawer',
])

<div class="drawer-side z-40 is-drawer-close:overflow-visible">
    {{-- Overlay untuk mobile --}}
    <label for="{{ $id }}" aria-label="Tutup menu" class="drawer-overlay"></label>

    <aside
        class="flex min-h-full flex-col border-e border-base-300/80 bg-base-100
               is-drawer-close:w-16
               is-drawer-open:w-72">
        {{-- Brand --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 border-b border-base-300/80 px-3 py-4 is-drawer-close:justify-center is-drawer-close:px-0">
            <span class="grid size-9 shrink-0 place-items-center rounded-xl bg-primary/10">
                <img src="{{ asset('assets/common/logo-mark.png') }}" alt="PanDev" class="size-6" />
            </span>
            <span class="min-w-0 is-drawer-close:hidden">
                <span class="block font-heading text-base font-bold leading-tight text-base-content">PanDev</span>
                <span class="block text-[11px] uppercase tracking-wider text-base-content/40">Admin Panel</span>
            </span>
        </a>

        {{-- Navigation --}}
        <nav class="flex-1 space-y-6 px-3 py-4 is-drawer-close:px-2">
            @foreach ($groups as $group)
                <div class="space-y-1">
                    <p
                        class="px-2.5 pb-1 text-[11px] font-semibold uppercase tracking-wider text-base-content/35 is-drawer-close:hidden">
                        {{ $group['label'] }}
                    </p>
                    <ul class="space-y-0.5">
                        @foreach ($group['items'] as $item)
                            <x-dashboard.sidebar-item :label="$item['title']" :href="route($item['route'])"
                                :icon="$item['icon']" :active="request()->routeIs($item['patterns'])" />
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        {{-- Account --}}
        <div class="space-y-1 border-t border-base-300/80 px-3 py-4 is-drawer-close:px-2">
            <ul class="space-y-0.5">
                @foreach ($accountItems as $item)
                    <x-dashboard.sidebar-item :label="$item['title']" :href="route($item['route'])"
                        :icon="$item['icon']" :active="request()->routeIs($item['patterns'])" />
                @endforeach
            </ul>

            <x-dashboard.nav-user class="mt-2 w-full" />
        </div>
    </aside>
</div>
