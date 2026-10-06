@props(['class' => ''])

@php
    $user = auth()->user();
@endphp

@if ($user)
    <div
        class="relative p-2 is-drawer-close:p-1 {{ $class }}"
        x-data="{ open: false }"
        x-on:click.outside="open = false"
        x-on:keydown.escape.window="open = false"
    >
        <button
            type="button"
            x-on:click="open = !open"
            class="hover:bg-sidebar-accent is-drawer-close:tooltip is-drawer-close:tooltip-right flex w-full items-center gap-2 rounded-lg p-2 text-left is-drawer-close:p-1"
            data-tip="{{ $user->fullname }} — {{ $user->email }}"
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
                <span class="bg-base-200 text-base-content/60 flex size-8 shrink-0 items-center justify-center rounded-lg">
                    <x-lucide name="user" class="size-4" />
                </span>
            @endif
            <span class="grid flex-1 text-sm leading-tight">
                <span class="truncate font-medium">{{ $user->fullname }}</span>
                <span class="text-base-content/60 truncate text-xs">{{ $user->email }}</span>
            </span>
            <x-lucide name="ellipsis-vertical" class="ml-auto size-4 shrink-0 is-drawer-close:hidden" />
        </button>

        <div
            x-show="open"
            x-cloak
            x-transition.origin.bottom
            role="menu"
            class="bg-base-100 text-base-content absolute bottom-full left-0 z-50 mb-2 w-56 min-w-56 rounded-lg border p-1 shadow-lg"
            x-bind:class="$store.sidebar.collapsed ? 'left-12' : 'left-0'"
        >
            <div class="flex items-center gap-2 p-2">
                @if ($user->image)
                    <img src="{{ $user->image }}" alt="" class="size-9 rounded-full object-cover" />
                @else
                    <span class="bg-base-200 text-base-content/60 flex size-9 items-center justify-center rounded-full">
                        <x-lucide name="user" class="size-4" />
                    </span>
                @endif
                <div class="grid min-w-0 flex-1 text-sm leading-tight">
                    <span class="truncate font-semibold">{{ $user->fullname }}</span>
                    <span class="text-base-content/60 truncate text-xs">{{ $user->email }}</span>
                </div>
            </div>

            <div class="my-1 h-px bg-border"></div>

            <a
                href="{{ route('settings.edit') }}"
                role="menuitem"
                class="hover:bg-base-200 hover:text-base-content flex items-center gap-2 rounded-md px-2 py-2 text-sm"
            >
                <x-lucide name="user" class="size-4" /> Akun
            </a>

            <div class="my-1 h-px bg-border"></div>

            <form method="POST" action="{{ route('logout') }}" role="none">
                <button
                    type="submit"
                    role="menuitem"
                    class="hover:bg-base-200 hover:text-base-content flex w-full items-center gap-2 rounded-md px-2 py-2 text-sm"
                >
                    <x-lucide name="log-out" class="size-4" /> Log out
                </button>
            </form>
        </div>
    </div>
@endif
