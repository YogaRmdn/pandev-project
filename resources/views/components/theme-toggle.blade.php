@props(['class' => 'icon-circle text-base-content'])

{{-- Tombol tukar mode terang/gelap. State-nya ada di store `theme` (lihat
     resources/js/app.js); komponen ini hanya menampilkan ikon yang sesuai. --}}
<button
    type="button"
    x-data
    x-on:click="$store.theme.toggle()"
    x-bind:aria-label="$store.theme.dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'"
    x-bind:title="$store.theme.dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'"
    aria-pressed="false"
    x-bind:aria-pressed="$store.theme.dark ? 'true' : 'false'"
    class="{{ $class }}"
    {{ $attributes }}
>
    <span class="inline-flex" x-show="! $store.theme.dark">
        <x-lucide name="sun" class="size-5" />
    </span>
    <span class="inline-flex" x-show="$store.theme.dark" x-cloak>
        <x-lucide name="moon" class="size-5" />
    </span>
</button>
