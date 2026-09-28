@extends('layouts.dashboard')

@section('content')
    <div class="space-y-4">
        <x-dashboard.header title="Tambah Portfolio" />

        <x-ui.card class="mt-4">
            <x-ui.card-header>
                <div class="flex items-center gap-2">
                    <x-ui.button
                        href="{{ route('dashboard.portfolio.index') }}"
                        variant="ghost"
                        size="icon"
                        aria-label="Kembali"
                    >
                        <x-lucide name="arrow-left" />
                    </x-ui.button>
                    <div>
                        <h1 class="text-lg font-semibold">Form Tambah Portfolio</h1>
                        <p class="text-muted-foreground text-sm">Isi form dibawah ini untuk menambahkan portfolio.</p>
                    </div>
                </div>
            </x-ui.card-header>

            <x-ui.card-content>
                <x-dashboard.portfolio-form
                    :action="route('dashboard.portfolio.store')"
                    method="POST"
                />
            </x-ui.card-content>
        </x-ui.card>
    </div>
@endsection
