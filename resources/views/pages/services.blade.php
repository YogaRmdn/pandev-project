@extends('layouts.main')

@section('title', 'Layanan | PanDev')

@section('description', 'Jelajahi layanan PanDev untuk bisnis dan individu, mulai dari aplikasi web hingga proyek IoT.')

@section('content')
    <main class="mx-auto flex w-full max-w-4xl flex-1 flex-col px-4 py-16 sm:px-6 lg:px-8">
        <h1 class="font-heading text-3xl font-bold tracking-tight sm:text-4xl">Layanan</h1>
        <p class="text-muted-foreground mt-4 max-w-2xl">
            PanDev menawarkan solusi aplikasi dan layanan teknologi untuk membantu
            bisnis dan individu mewujudkan ide menjadi nyata.
        </p>

        <div class="mt-10 grid gap-4 sm:grid-cols-2">
            @foreach ($services as $service)
                <x-ui.card>
                    <x-ui.card-header>
                        <x-ui.card-title>{{ $service['title'] }}</x-ui.card-title>
                        <x-ui.card-description>{{ $service['description'] }}</x-ui.card-description>
                    </x-ui.card-header>
                </x-ui.card>
            @endforeach
        </div>
    </main>
@endsection
