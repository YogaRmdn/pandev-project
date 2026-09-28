@php
    $navItems = [
        ['href' => '/portfolio', 'label' => 'Portofolio'],
        ['href' => '/tentang', 'label' => 'Tentang'],
        ['href' => '/kontak', 'label' => 'Kontak'],
    ];
@endphp

<header class="bg-background/80 supports-backdrop-filter:bg-background/60 sticky top-0 z-50 w-full border-b backdrop-blur-md">
    <div class="mx-auto flex h-16 max-w-screen-2xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="font-heading flex items-center gap-2 text-lg font-semibold tracking-tight" aria-label="PanDev - Beranda">
            <span class="text-primary-foreground inline-flex size-6 items-center justify-center overflow-hidden rounded-md">
                <img src="{{ asset('assets/common/logo.png') }}" width="50" height="50" alt="PanDev Logo" class="size-full object-contain" />
            </span>
            PANDEV
        </a>

        <nav class="hidden items-center md:flex" aria-label="Navigasi utama">
            @foreach ($navItems as $item)
                <a
                    href="{{ url($item['href']) }}"
                    @if (request()->is(ltrim($item['href'], '/').'*') || request()->is(ltrim($item['href'], '/'))) aria-current="page" @endif
                    class="relative rounded-md px-3 py-2 text-sm font-medium {{ request()->is(ltrim($item['href'], '/').'*') || request()->is(ltrim($item['href'], '/')) ? 'text-primary' : 'text-foreground' }}"
                >{{ $item['label'] }}</a>
            @endforeach

            @auth
                <div class="ml-4" x-data="{ open: false }">
                    <x-ui.button
                        variant="ghost"
                        size="icon"
                        x-on:click="open = !open"
                        aria-label="Menu akun"
                    >
                        @if (auth()->user()->image)
                            <img src="{{ auth()->user()->image }}" alt="{{ auth()->user()->fullname }}" class="size-full rounded-full object-cover" />
                        @else
                            <x-lucide name="user" />
                        @endif
                    </x-ui.button>

                    <div
                        x-show="open"
                        x-cloak
                        x-on:click.outside="open = false"
                        x-transition
                        class="bg-popover text-popover-foreground absolute right-0 z-50 mt-2 flex w-fit flex-col items-start gap-1 rounded-md border p-1 shadow-md"
                    >
                        <a href="{{ route('dashboard') }}">
                            <x-ui.button variant="outline" size="sm" class="w-full justify-start">
                                <x-lucide name="layout-dashboard" /> Dashboard
                            </x-ui.button>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <x-ui.button type="submit" variant="outline" size="sm" class="w-full justify-start">
                                <x-lucide name="log-out" /> Logout
                            </x-ui.button>
                        </form>
                    </div>
                </div>
            @else
                <a class="ml-4" href="{{ route('login') }}">
                    <x-ui.button>Login</x-ui.button>
                </a>
            @endauth
        </nav>

        {{-- Mobile drawer --}}
        <div x-data="{ open: false }" class="md:hidden">
            <x-ui.button variant="ghost" size="icon" x-on:click="open = true" aria-label="Buka menu navigasi">
                <x-lucide name="menu" />
            </x-ui.button>

            <div
                x-show="open"
                x-cloak
                x-transition.opacity
                class="fixed inset-0 z-50 bg-black/50"
                x-on:click="open = false"
            ></div>

            <div
                x-show="open"
                x-cloak
                x-transition
                class="bg-background fixed inset-y-0 right-0 z-50 w-3/4 max-w-sm border-l p-6 shadow-lg"
            >
                <div class="flex items-center justify-between">
                    <span class="font-heading text-lg font-semibold tracking-tight">PANDEV</span>
                    <x-ui.button variant="ghost" size="icon" x-on:click="open = false" aria-label="Tutup menu">
                        <x-lucide name="x" />
                    </x-ui.button>
                </div>

                <nav class="mt-6 flex flex-col gap-1" aria-label="Navigasi utama">
                    @foreach ($navItems as $item)
                        <a
                            href="{{ url($item['href']) }}"
                            class="rounded-md px-3 py-2.5 text-sm font-medium transition-colors {{ request()->is(ltrim($item['href'], '/').'*') || request()->is(ltrim($item['href'], '/')) ? 'bg-muted text-foreground' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}"
                        >{{ $item['label'] }}</a>
                    @endforeach

                    @auth
                        <a href="{{ route('dashboard') }}" class="text-muted-foreground hover:bg-muted hover:text-foreground rounded-md px-3 py-2.5 text-sm font-medium transition-colors">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-muted-foreground hover:bg-muted hover:text-foreground w-full rounded-md px-3 py-2.5 text-left text-sm font-medium transition-colors">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-muted-foreground hover:bg-muted hover:text-foreground rounded-md px-3 py-2.5 text-sm font-medium transition-colors">
                            Login
                        </a>
                    @endauth
                </nav>
            </div>
        </div>
    </div>
</header>
