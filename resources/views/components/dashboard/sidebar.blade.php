@php
    /**
     * Active state follows the original: only the first two path segments
     * count, so /dashboard/portfolio/create still highlights "Portfolio".
     */
    $segments = array_slice(array_values(array_filter(explode('/', trim(request()->path(), '/')))), 0, 2);
    $activePath = '/' . implode('/', $segments);

    $mainItems = [
        ['title' => 'Dashboard', 'href' => '/dashboard', 'icon' => 'lucide-layout-dashboard'],
        ['title' => 'Portfolio', 'href' => '/dashboard/portfolio', 'icon' => 'heroicon-o-briefcase'],
    ];

    $adminItems = [
        ['title' => 'Manajemen User', 'href' => '/dashboard/user-management', 'icon' => 'lucide-users'],
        ['title' => 'Keuangan', 'href' => '/dashboard/finance', 'icon' => 'heroicon-o-wallet'],
        ['title' => 'Faktur', 'href' => '/dashboard/invoice', 'icon' => 'lucide-notepad-text'],
    ];

    $secondaryItems = [['title' => 'Pengaturan', 'href' => '/dashboard/settings', 'icon' => 'hugeicons-setting-07']];
@endphp

@props([
    'id' => 'dashboard-drawer',
])
<div class="drawer-side is-drawer-close:overflow-visible">
    {{-- Overlay untuk mobile --}}
    <label for="{{ $id }}" aria-label="close sidebar" class="drawer-overlay"></label>
    <div
        class="flex min-h-full flex-col items-start bg-base-200
               is-drawer-close:w-14
               is-drawer-open:w-68">
        <ul class="menu w-full grow space-y-8">
            <x-dashboard.sidebar-item label="PanDev" class="font-semibold" href="/dashboard">
                <img src="{{ asset('assets/common/logo-mark.png') }}" class="size-6" />
            </x-dashboard.sidebar-item>
            <div>
                <li
                    class="text-sidebar-foreground/60 px-2 pb-2 text-xs font-medium tracking-wide uppercase is-drawer-close:hidden">
                    dashboard</li>
                @foreach ($mainItems as $item)
                    <x-dashboard.sidebar-item :label="$item['title']" :href="$item['href']" :icon="$item['icon']" :active="$activePath === $item['href']" />
                @endforeach
            </div>
            <div>
                <li
                    class="text-sidebar-foreground/60 px-2 pb-2 text-xs font-medium tracking-wide uppercase is-drawer-close:hidden">
                    admin</li>
                @foreach ($adminItems as $item)
                    <x-dashboard.sidebar-item :label="$item['title']" :href="$item['href']" :icon="$item['icon']" :active="$activePath === $item['href']" />
                @endforeach
            </div>
            <div>
                <li
                    class="text-sidebar-foreground/60 px-2 pb-2 text-xs font-medium tracking-wide uppercase is-drawer-close:hidden">
                    akun</li>
                @foreach ($secondaryItems as $item)
                    <x-dashboard.sidebar-item :label="$item['title']" :href="$item['href']" :icon="$item['icon']"
                        :active="$activePath === $item['href']" />
                @endforeach
            </div>
        </ul>

        <x-dashboard.nav-user class="w-full border-t" />
    </div>
</div>
