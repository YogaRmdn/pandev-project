@extends('layouts.guest')

@section('title', 'Buat Portfolio | PanDev')

@section('content')
    <header class="border-b border-base-300">
        <div class="flex items-center gap-3 px-4 py-3 sm:px-6">
<a href="{{ route('dashboard.portfolio.index') }}" class="btn btn-circle btn-sm btn-ghost"
    aria-label="Kembali ke daftar portfolio"
    @click="cleanupOrphans">
    <x-lucide name="arrow-left" class="size-4" />
</a>
            <div>
                <h1 class="text-lg font-bold">Buat Portfolio</h1>
                <p class="text-base-content/60 text-sm">Isi form dibawah untuk menambahkan portfolio</p>
            </div>
        </div>
    </header>

    <main class="flex-1 px-4 py-6 sm:px-6">
        <div class="card border bg-base-100 p-6 shadow-sm">
            <x-dashboard.portfolio-form :action="route('dashboard.portfolio.store')" method="POST" />
        </div>
    </main>
@endsection
