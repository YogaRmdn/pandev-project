@props([
    'name',
    'title' => 'Hapus data ini?',
    'description' => 'Tindakan ini tidak dapat dibatalkan.',
    'confirmLabel' => 'Hapus',
])

<div x-data class="contents whitespace-normal" x-on:keydown.escape.window="$store.modals.close('{{ $name }}')">
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
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="alertdialog"
        aria-modal="true"
    >
        <div
            x-show="$store.modals.isOpen('{{ $name }}')"
            x-transition
            class="bg-base-100 text-base-content relative flex w-full max-w-lg flex-col gap-4 rounded-xl border p-6 shadow-lg"
        >
            <button
                type="button"
                x-on:click="$store.modals.close('{{ $name }}')"
                class="absolute top-4 right-4 rounded-xs opacity-70 transition-opacity hover:opacity-100"
                aria-label="Tutup"
            >
                <x-lucide name="x" class="size-4" />
            </button>

            <div class="flex flex-col gap-2 pr-8 text-center sm:text-left">
                <h2 class="text-lg font-semibold">{{ $title }}</h2>
                <p class="text-base-content/60 text-sm">{{ $description }}</p>
            </div>

            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button type="button" class="btn btn-outline" x-on:click="$store.modals.close('{{ $name }}')">
                    Batal
                </button>
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
