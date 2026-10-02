@php
    use App\Support\SiteContent;

    $navItems = SiteContent::navigation();
    $isActive = fn (array $item): bool => request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*');
@endphp

<header
    x-data="{ open: false, scrolled: false }"
    x-bind:class="{ 'is-scrolled': scrolled }"
    x-on:scroll.window.throttle.50ms="scrolled = window.scrollY > 8"
    x-on:keydown.escape.window="open = false"
    x-effect="document.body.classList.toggle('overflow-hidden', open)"
    class="sticky top-0 z-50 w-full"
>
    <div class="nav-shell relative flex items-stretch">
        {{-- Kartu putih 80% di atas band tinted; isinya (logo) ikut container
             halaman. --}}
        <div class="nav-panel w-[80%] min-w-0 pt-1.5">
            <div class="mx-auto flex w-full max-w-6xl items-stretch px-4">
                <a
                    href="{{ route('home') }}"
                    class="my-5 flex shrink-0 items-center gap-2.5 font-heading text-lg font-bold tracking-tight"
                    aria-label="PanDev - Beranda"
                >
                    <span class="bg-primary text-primary-content inline-flex size-8 shrink-0 items-center justify-center overflow-hidden rounded-full">
                        <img
                            src="{{ asset('assets/common/logo.png') }}"
                            width="50"
                            height="50"
                            alt="PanDev Logo"
                            class="size-full object-contain"
                        />
                    </span>
                    <span class="text-base-content">PANDEV</span>
                </a>
            </div>
        </div>

        {{-- Menu ditengahkan terhadap lebar halaman (selaras dengan hero),
             bukan terhadap kartu 80%: start-1/2 + -translate-x-1/2. --}}
        <nav class="absolute start-1/2 top-0 hidden h-full -translate-x-1/2 items-stretch lg:flex" aria-label="Main navigation">
            @foreach ($navItems as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @if ($isActive($item)) aria-current="page" @endif
                    class="nav-pill"
                >{{ $item['label'] }}</a>

                @unless ($loop->last)
                    <span class="nav-sep self-center px-1" aria-hidden="true">|</span>
                @endunless
            @endforeach
        </nav>

        {{-- Ikon sosial menggantikan tombol login, disejajarkan dengan tinggi
             navbar lewat py-6/py-4 yang sama dengan sel ini. --}}
        <div class="ms-auto hidden shrink-0 items-center gap-4 py-6 pe-4 sm:pe-6 lg:pe-8 lg:flex">
            <x-social-links size="size-6" class="gap-4" />

            @auth
                <div class="relative" x-data="{ account: false }" x-on:click.outside="account = false">
                    <button
                        type="button"
                        class="icon-circle text-base-content"
                        x-on:click="account = !account"
                        x-bind:aria-expanded="account"
                        aria-haspopup="menu"
                        aria-label="Account menu"
                    >
                        @if (auth()->user()->image)
                            <img
                                src="{{ auth()->user()->image }}"
                                alt="{{ auth()->user()->fullname }}"
                                class="size-full rounded-full object-cover"
                            />
                        @else
                            <x-lucide name="user" class="size-5" />
                        @endif
                    </button>

                    <div
                        x-show="account"
                        x-cloak
                        x-transition.origin.top.right
                        role="menu"
                        class="bg-base-100 text-base-content absolute end-0 z-50 mt-3 w-56 rounded-lg border p-1.5 shadow-lg"
                    >
                        <a
                            href="{{ route('dashboard') }}"
                            role="menuitem"
                            class="hover:bg-base-200 hover:text-base-content flex items-center gap-2.5 rounded-md px-3 py-2.5 text-sm font-medium"
                        >
                            <x-lucide name="layout-dashboard" class="size-4" /> Dashboard
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                role="menuitem"
                                class="hover:bg-base-200 hover:text-base-content flex w-full items-center gap-2.5 rounded-md px-3 py-2.5 text-sm font-medium"
                            >
                                <x-lucide name="log-out" class="size-4" /> Logout
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>

        {{-- Mobile: circular accent toggle, matching the reference bar. Ikon
             sosial disembunyikan di bawah sm supaya kartu putih 80% tetap
             menyisakan ruang untuk logo + tombol menu tanpa meluber; socials
             tetap tersedia di dalam drawer. --}}
        <div class="ms-auto flex shrink-0 items-center gap-4 py-4 pe-4 lg:hidden">
            <x-social-links size="size-5" class="hidden gap-3 sm:flex" />

            <button
                type="button"
                class="icon-circle bg-primary text-primary-content border-transparent"
                x-on:click="open = true"
                aria-label="Open navigation menu"
            >
                <x-lucide name="menu" class="size-5" />
            </button>
        </div>
    </div>

    {{-- Off-canvas drawer --}}
    <div
        x-show="open"
        x-cloak
        x-transition.opacity.duration.300ms
        class="fixed inset-0 z-[9998] bg-black/50"
        x-on:click="open = false"
        aria-hidden="true"
    ></div>

    <div
        class="nav-offcanvas"
        x-bind:class="{ 'is-open': open }"
        x-bind:inert="!open"
        x-bind:aria-hidden="(!open).toString()"
    >
        <div class="flex items-center justify-between">
            <span class="font-heading flex items-center gap-2.5 text-lg font-bold tracking-tight">
                <span class="bg-primary text-primary-content inline-flex size-8 items-center justify-center overflow-hidden rounded-full">
                    <img src="{{ asset('assets/common/logo.png') }}" width="50" height="50" alt="PanDev Logo" class="size-full object-contain" />
                </span>
                <span class="text-base-content">PANDEV</span>
            </span>

            <button
                type="button"
                class="icon-circle text-base-content"
                x-on:click="open = false"
                aria-label="Close menu"
            >
                <x-lucide name="x" class="size-5" />
            </button>
        </div>

        <nav class="mt-6" aria-label="Main navigation">
            @foreach ($navItems as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @if ($isActive($item)) aria-current="page" @endif
                    class="nav-offcanvas-item"
                >{{ $item['label'] }}</a>
            @endforeach

            @auth
                <a href="{{ route('dashboard') }}" class="nav-offcanvas-item">Dashboard</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-offcanvas-item">Logout</button>
                </form>
            @endauth
        </nav>

        <div class="mt-auto border-t pt-6">
            <div class="text-base-content/60 text-xs font-semibold tracking-wider uppercase">
                Follow us
            </div>
            <x-social-links size="size-5" class="mt-3 gap-4" />
        </div>
    </div>
</header>
