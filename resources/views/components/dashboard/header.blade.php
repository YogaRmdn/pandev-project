@props(['title', 'description' => null])

<header class="flex h-16 w-full shrink-0 items-center gap-2 border-b px-4">
    <button
        type="button"
        x-on:click="$store.sidebar.toggle()"
        class="hover:bg-accent hover:text-accent-foreground -ml-1 inline-flex size-8 items-center justify-center rounded-md"
        aria-label="Toggle Sidebar"
    >
        <x-lucide name="panel-left" class="size-5" />
        <span class="sr-only">Toggle Sidebar</span>
    </button>

    <div class="bg-border mx-2 h-14 w-px" aria-hidden="true"></div>

    <div>
        <div class="text-xl font-bold">{{ $title }}</div>
        @if ($description)
            <div class="text-muted-foreground text-sm">{{ $description }}</div>
        @endif
    </div>
</header>
