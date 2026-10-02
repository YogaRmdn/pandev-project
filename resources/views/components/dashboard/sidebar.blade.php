@php
    /**
     * Active state follows the original: only the first two path segments
     * count, so /dashboard/portfolio/create still highlights "Portfolio".
     */
    $segments = array_slice(array_values(array_filter(explode('/', trim(request()->path(), '/')))), 0, 2);
    $activePath = '/'.implode('/', $segments);

    $mainItems = [
        ['title' => 'Dashboard', 'href' => route('dashboard'), 'icon' => 'layout-dashboard'],
        ['title' => 'Portfolio', 'href' => route('dashboard.portfolio.index'), 'icon' => 'briefcase'],
    ];

    $adminItems = [
        ['title' => 'Manajemen User', 'href' => route('dashboard.users.index'), 'icon' => 'users'],
        ['title' => 'Keuangan', 'href' => route('dashboard.finance.index'), 'icon' => 'wallet'],
        ['title' => 'Faktur', 'href' => route('dashboard.invoice.index'), 'icon' => 'receipt-text'],
    ];

    $secondaryItems = [
        ['title' => 'Pengaturan', 'href' => route('settings.edit'), 'icon' => 'settings'],
    ];
@endphp

<aside
    class="fixed inset-y-0 left-0 z-30 hidden h-svh flex-col overflow-y-auto border-r bg-sidebar md:flex"
    x-bind:class="$store.sidebar.collapsed ? 'w-12' : 'w-64'"
>
    <a
        href="{{ route('dashboard') }}"
        class="flex h-16 items-center gap-2 border-b px-2"
        aria-label="PanDev"
    >
        <img src="{{ asset('assets/common/logo-mark.png') }}" alt="PanDev Logo" width="32" height="32" class="size-8 shrink-0 object-contain" />
        <span
            class="truncate font-semibold"
            x-show="!$store.sidebar.collapsed"
            x-transition.opacity.duration.150ms
        >PanDev</span>
    </a>

    <x-dashboard.nav-group label="Menu" :items="$mainItems" :active-path="$activePath" />
    @if (auth()->user()->isAdmin())
        <x-dashboard.nav-group label="Admin" :items="$adminItems" :active-path="$activePath" />
    @endif
    <x-dashboard.nav-group label="Akun" :items="$secondaryItems" :active-path="$activePath" />

    <x-dashboard.nav-user class="mt-auto border-t" />
</aside>

{{-- Off-canvas drawer below md --}}
<div
    x-show="$store.sidebar.mobileOpen"
    x-on:keydown.escape.window="$store.sidebar.close()"
    class="fixed inset-0 z-50 md:hidden"
    x-cloak
>
    <div
        x-show="$store.sidebar.mobileOpen"
        x-transition.opacity
        x-on:click="$store.sidebar.close()"
        class="absolute inset-0 bg-black/50"
        aria-hidden="true"
    ></div>

    <aside
        x-show="$store.sidebar.mobileOpen"
        x-transition:enter="transition ease-in-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="absolute inset-y-0 left-0 flex h-full w-72 flex-col overflow-y-auto border-r bg-sidebar"
    >
        <a
            href="{{ route('dashboard') }}"
            x-on:click="$store.sidebar.close()"
            class="flex h-16 items-center gap-2 border-b px-2"
            aria-label="PanDev"
        >
            <img src="{{ asset('assets/common/logo-mark.png') }}" alt="PanDev Logo" width="32" height="32" class="size-8 shrink-0 object-contain" />
            <span class="truncate font-semibold">PanDev</span>
        </a>

        <x-dashboard.nav-group label="Menu" :items="$mainItems" :active-path="$activePath" />
        @if (auth()->user()->isAdmin())
            <x-dashboard.nav-group label="Admin" :items="$adminItems" :active-path="$activePath" />
        @endif
        <x-dashboard.nav-group label="Akun" :items="$secondaryItems" :active-path="$activePath" />

        <x-dashboard.nav-user class="mt-auto border-t" />
    </aside>
</div>
