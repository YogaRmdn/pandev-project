@props([
    'name',
    'title' => null,
    'description' => null,
    'width' => 'sm:max-w-lg',
])

{{-- Triggered from anywhere with: $dispatch('open-modal', 'name') --}}
<div x-data class="contents">
    <div
        x-show="$store.modals.isOpen('{{ $name }}')"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 bg-black/50"
        x-on:click="$store.modals.close('{{ $name }}')"
    ></div>

    <div
        x-show="$store.modals.isOpen('{{ $name }}')"
        x-cloak
        x-transition
        class="fixed inset-x-0 bottom-0 z-50 mx-auto flex max-h-[92dvh] w-full flex-col p-4 sm:inset-x-auto sm:bottom-auto sm:right-0 sm:top-0 sm:h-full sm:max-h-full sm:translate-y-0 sm:translate-x-0 {{ $width }}"
        role="dialog"
        aria-modal="true"
    >
        <div
            x-show="$store.modals.isOpen('{{ $name }}')"
            x-transition
            class="bg-card text-card-foreground relative flex max-h-full w-full flex-col gap-4 overflow-y-auto rounded-xl border p-6 shadow-lg"
        >
            <button
                type="button"
                x-on:click="$store.modals.close('{{ $name }}')"
                class="absolute top-4 right-4 rounded-xs opacity-70 transition-opacity hover:opacity-100"
                aria-label="Tutup"
            >
                <x-lucide name="x" class="size-4" />
            </button>

            @if ($title)
                <div class="flex flex-col gap-1.5 pr-8 text-center sm:text-left">
                    <h2 class="text-lg leading-none font-semibold">{{ $title }}</h2>
                    @if ($description)
                        <p class="text-muted-foreground text-sm">{{ $description }}</p>
                    @endif
                </div>
            @endif

            <div class="grid gap-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
