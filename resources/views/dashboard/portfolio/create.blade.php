@extends('layouts.dashboard')

@section('content')
    <div class="space-y-4">
        <x-dashboard.header title="Tambah Portfolio" />

        <div class="card gap-6 border p-6 shadow-sm mt-4">
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard.portfolio.index') }}" class="btn btn-ghost btn-square" aria-label="Kembali">
                        <x-lucide name="arrow-left" />
                    </a>
                    <div>
                        <h1 class="text-lg font-semibold">Form Tambah Portfolio</h1>
                        <p class="text-base-content/60 text-sm">Isi form dibawah ini untuk menambahkan portfolio.</p>
                    </div>
                </div>
            </div>

            <div class="">
                <x-dashboard.portfolio-form
                    :action="route('dashboard.portfolio.store')"
                    method="POST"
                />
            </div>
        </div>
    </div>
@endsection
