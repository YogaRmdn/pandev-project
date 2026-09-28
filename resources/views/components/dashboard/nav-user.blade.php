@props(['class' => ''])

@php
    $user = auth()->user();
@endphp

@if ($user)
    <div
        class="relative p-2 {{ $class }}"
        x-data="{ open: false }"
        x-on:click.outside="open = false"
        x-on:keydown.escape.window="open = false"
    >
        <button
            type="button"
            x-on:click="open = !open"
            class="hover:bg-sidebar-accent flex w-full items-center gap-2 rounded-lg p-2 text-left"
            aria-haspopup="menu"
            x-bind:aria-expanded="open"
        >
            @if ($user->image)
                <img
                    src="{{ $user->image }}"
                    alt="{{ $user->fullname }}"
                    class="size-8 shrink-0 rounded-lg object-cover"
                />
            @else
                <span class="bg-muted text-muted-foreground flex size-8 shrink-0 items-center justify-center rounded-lg">
                    <x-lucide name="user" class="size-4" />
                </span>
            @endif

            <span class="grid flex-1 text-sm leading-tight" x-show="!$store.sidebar.collapsed">
                <span class="truncate font-medium">{{ $user->fullname }}</span>
                <span class="text-muted-foreground truncate text-xs">{{ $user->email }}</span>
            </span>

            <x-lucide name="ellipsis-vertical" class="ml-auto size-4 shrink-0" />
        </button>

        <div
            x-show="open"
            x-cloak
            x-transition.origin.bottom
            role="menu"
            class="bg-popover text-popover-foreground absolute bottom-full left-0 z-50 mb-2 w-56 min-w-56 rounded-lg border p-1 shadow-lg"
            x-bind:class="$store.sidebar.collapsed ? 'left-12' : 'left-0'"
        >
            <div class="flex items-center gap-2 p-2">
                @if ($user->image)
                    <img src="{{ $user->image }}" alt="" class="size-9 rounded-full object-cover" />
                @else
                    <span class="bg-muted text-muted-foreground flex size-9 items-center justify-center rounded-full">
                        <x-lucide name="user" class="size-4" />
                    </span>
                @endif
                <div class="grid min-w-0 flex-1 text-sm leading-tight">
                    <span class="truncate font-semibold">{{ $user->fullname }}</span>
                    <span class="text-muted-foreground truncate text-xs">{{ $user->email }}</span>
                </div>
            </div>

            <div class="my-1 h-px bg-border"></div>

            <a
                href="{{ route('settings.edit') }}"
                role="menuitem"
                class="hover:bg-accent hover:text-accent-foreground flex items-center gap-2 rounded-md px-2 py-2 text-sm"
            >
                <x-lucide name="user" class="size-4" /> Akun
            </a>

            <div class="my-1 h-px bg-border"></div>

            <form method="POST" action="{{ route('logout') }}" role="none">
                <button
                    type="submit"
                    role="menuitem"
                    class="hover:bg-accent hover:text-accent-foreground flex w-full items-center gap-2 rounded-md px-2 py-2 text-sm"
                >
                    <x-lucide name="log-out" class="size-4" /> Log out
                </button>
            </form>
        </div>
    </div>
@endif
