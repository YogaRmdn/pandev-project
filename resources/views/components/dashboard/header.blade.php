@props(['title', 'description' => null])

<header class="flex h-16 w-full shrink-0 items-center gap-2 border-b px-4">
    <button
        type="button"
        x-on:click="$store.sidebar.toggle()"
        class="btn btn-ghost btn-sm -ml-1 btn-square"
        aria-label="Toggle Sidebar"
    >
        <x-lucide name="panel-left" class="size-5" />
    </button>

    <div class="bg-base-300 mx-2 h-14 w-px" aria-hidden="true"></div>

    <div>
        <div class="text-xl font-bold">{{ $title }}</div>
        @if ($description)
            <div class="text-base-content/60 text-sm">{{ $description }}</div>
        @endif
    </div>
</header>
