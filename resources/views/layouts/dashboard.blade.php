<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="panev-light" class="h-full antialiased">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.theme')

    <title>@yield('title', 'Dashboard | ' . config('app.name'))</title>

    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" href="{{ asset('assets/common/logo-mark.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&family=Montserrat:wght@100..900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body x-data class="bg-base-200 font-sans text-base-content">
    <div class="drawer lg:drawer-open">
        {{-- Drawer toggle. Lebar sidebar (rail w-16 vs terbuka w-72) ditentukan
             checkbox ini, dan checkbox reset ke unchecked setiap pindah halaman —
             tanpa pemulihan state, sidebar selalu jadi mini setelah navigasi. --}}
        <input id="dashboard-drawer" type="checkbox" class="drawer-toggle"
            x-init="$store.sidebar.collapsed = window.matchMedia('(min-width: 1024px)').matches && ! $el.checked"
            x-on:change="$store.sidebar.collapsed = window.matchMedia('(min-width: 1024px)').matches && ! $el.checked" />
        <script>
            (function () {
                var el = document.getElementById('dashboard-drawer');
                if (!el) return;
                // Desktop: pulihkan preferensi dari localStorage (default: terbuka).
                // Mobile: drawer off-canvas selalu tertutup saat load.
                el.checked = window.matchMedia('(min-width: 1024px)').matches
                    && localStorage.getItem('sidebar_state') !== 'true';
                el.addEventListener('change', function () {
                    if (window.matchMedia('(min-width: 1024px)').matches) {
                        localStorage.setItem('sidebar_state', el.checked ? 'false' : 'true');
                    }
                });
            })();
        </script>
        {{-- Main content --}}
        <div class="drawer-content flex min-h-screen flex-col">
            <x-dashboard.topbar />

            <main class="flex-1 px-4 py-5 md:px-6 md:py-6 lg:px-8">
                @yield('content')
            </main>
        </div>
        {{-- Sidebar --}}
        <x-dashboard.sidebar id="dashboard-drawer">
            @yield('sidebar')
        </x-dashboard.sidebar>
    </div>

    {{-- Logout otomatis setelah idle beberapa menit; dialog peringatan
         menampilkan hitung mundur sebelum sesi diputus. --}}
    <x-idle-timeout />
    {{-- Popup sukses: modal di tengah layar, digerakkan flash session dari
         controller (tambah/hapus/update). `modal-open` dipasang statis supaya
         langsung terlihat sebelum Alpine memuat; tombol OK/Esc/klik area luar
         menutupnya. --}}
    @php
        $flashMessage = session('success') ?? session('status');
        $flashMessage = [
            'password-updated' => 'Password berhasil diperbarui',
            'verification-link-sent' => 'Tautan verifikasi telah dikirim ke email Anda',
        ][$flashMessage] ?? $flashMessage;
    @endphp
    @if ($flashMessage)
        <div class="modal modal-open z-[1000]" x-data="{ open: true }" x-show="open" x-transition
            x-on:click.self="open = false" x-on:keydown.escape.window="open = false"
            role="dialog" aria-modal="true" aria-labelledby="success-modal-title">
            <div class="modal-box flex max-w-sm flex-col items-center gap-3 text-center">
                <span class="grid size-14 place-items-center rounded-full bg-success/15">
                    <x-lucide name="circle-check" class="size-8 text-ink-success" />
                </span>
                <h2 id="success-modal-title" class="text-lg font-semibold">Berhasil</h2>
                <p class="text-sm text-base-content/70">{{ $flashMessage }}</p>
                <div class="modal-action m-0 justify-center">
                    <button type="button" class="btn btn-primary" x-on:click="open = false">OK</button>
                </div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="toast toast-start z-100" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition role="alert">
            <div class="alert alert-error">
                <span class="font-medium">Terjadi kesalahan</span>
                <ul class="mt-1 list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @stack('scripts')
</body>

</html>
