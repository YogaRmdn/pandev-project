@php
    $user = auth()->user();
    $pageTitle = trim((string) $__env->yieldContent('page-title'));
    $pageDescription = trim((string) $__env->yieldContent('page-description'));
@endphp

<header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-base-300/80 bg-base-100/85 px-4 backdrop-blur md:px-6 lg:px-8">
    {{-- Mobile drawer toggle --}}
    <label for="dashboard-drawer" class="btn btn-square btn-ghost btn-sm lg:hidden" aria-label="Buka menu">
        <x-lucide name="menu" class="size-5" />
    </label>

    <div class="min-w-0 flex-1">
        @if ($pageTitle !== '')
            <nav class="flex items-center gap-1 text-xs text-base-content/50">
                <a href="{{ route('dashboard') }}" class="transition-colors hover:text-primary">Dashboard</a>
                <x-lucide name="chevron-right" class="size-3" />
                <span class="truncate">{{ $pageTitle }}</span>
            </nav>
        @endif
        <h1 class="truncate font-heading text-lg font-semibold leading-tight text-base-content">
            {{ $pageTitle !== '' ? $pageTitle : 'Dashboard' }}
        </h1>
        @if ($pageDescription !== '')
            <p class="truncate text-xs text-base-content/50">{{ $pageDescription }}</p>
        @endif
    </div>

    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn btn-ghost btn-sm hidden gap-2 sm:inline-flex">
        <x-lucide name="external-link" class="size-4" />
        <span class="hidden md:inline">Lihat Situs</span>
    </a>

    @if ($user)
        <div class="relative" x-data="{ open: false }" x-on:click.outside="open = false"
            x-on:keydown.escape.window="open = false">
            <button type="button" x-on:click="open = !open"
                class="flex items-center gap-2 rounded-full border border-base-300/70 bg-base-100 py-1 pe-2 ps-1 text-left transition-colors hover:border-base-300 hover:bg-base-200"
                aria-haspopup="menu" x-bind:aria-expanded="open">
                @if ($user->image)
                    <img src="{{ $user->image }}" alt="{{ $user->fullname }}"
                        class="size-8 shrink-0 rounded-full object-cover" />
                @else
                    <span
                        class="grid size-8 shrink-0 place-items-center rounded-full bg-primary/10 text-xs font-semibold text-primary">
                        {{ $user->initials }}
                    </span>
                @endif
                <span class="hidden pe-1 text-start text-sm leading-tight md:block">
                    <span class="block max-w-32 truncate font-semibold">{{ $user->fullname }}</span>
                    <span class="block text-[11px] text-base-content/50">{{ $user->role?->label() ?? 'Pengguna' }}</span>
                </span>
                <x-lucide name="chevron-down" class="hidden size-4 text-base-content/40 md:block" />
            </button>

            <div x-show="open" x-cloak x-transition.origin.top.right role="menu"
                class="absolute end-0 z-50 mt-2 w-60 rounded-xl border border-base-300/70 bg-base-100 p-1.5 shadow-lg">
                <div class="flex items-center gap-2.5 rounded-lg bg-base-200/60 p-2.5">
                    @if ($user->image)
                        <img src="{{ $user->image }}" alt="" class="size-9 shrink-0 rounded-full object-cover" />
                    @else
                        <span
                            class="grid size-9 shrink-0 place-items-center rounded-full bg-primary/10 text-sm font-semibold text-primary">
                            {{ $user->initials }}
                        </span>
                    @endif
                    <div class="grid min-w-0 flex-1 text-sm leading-tight">
                        <span class="truncate font-semibold">{{ $user->fullname }}</span>
                        <span class="truncate text-xs text-base-content/50">{{ $user->email }}</span>
                    </div>
                </div>

                <div class="my-1.5 h-px bg-base-300/70"></div>

                <a href="{{ route('settings.edit') }}" role="menuitem"
                    class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm text-base-content/80 transition-colors hover:bg-base-200 hover:text-base-content">
                    <x-lucide name="user" class="size-4" /> Akun saya
                </a>

                <div class="my-1.5 h-px bg-base-300/70"></div>

                <form method="POST" action="{{ route('logout') }}" role="none">
                    <button type="submit" role="menuitem"
                        class="flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm text-error transition-colors hover:bg-error/10">
                        <x-lucide name="log-out" class="size-4" /> Keluar
                    </button>
                </form>
            </div>
        </div>
    @endif
</header>
