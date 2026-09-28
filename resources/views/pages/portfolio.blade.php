@extends('layouts.main')

@section('title', 'Portofolio | PanDev')

@section('description', 'Lihat proyek dan karya terbaik yang telah dikerjakan oleh PanDev.')

@section('content')
    @php
        $featured = [
            ['id' => 1, 'title' => 'Proyek Web Application'],
            ['id' => 2, 'title' => 'Proyek Mobile App'],
            ['id' => 3, 'title' => 'Proyek IoT'],
            ['id' => 4, 'title' => 'Proyek Data Analytics'],
            ['id' => 5, 'title' => 'Proyek Cyber Security'],
        ];
    @endphp

    <main class="flex-1 space-y-4">
        <section class="flex w-full flex-1 flex-col items-center py-16">
            <h1 class="font-heading text-primary text-center text-3xl font-bold tracking-tight uppercase sm:text-4xl">
                Project showcase
            </h1>

            @include('pages.partials.carousel')

            <p class="text-muted-foreground mt-4 max-w-4xl text-center text-base lg:text-xl">
                Kumpulan proyek aplikasi dalam bentuk website, mobile, dan desktop
                serta design yang telah kami kerjakan dan bangun
            </p>

            <x-ui.button href="{{ route('portfolio.index') }}" class="mt-4 h-14 rounded-2xl px-4">
                Lihat Selengkapnya
                <x-lucide name="move-up-right" />
            </x-ui.button>
        </section>

        <section class="space-y-8 px-4">
            <div class="text-primary text-4xl font-bold uppercase">Recent Project</div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach (array_slice($featured, 0, 2) as $project)
                    <a href="{{ route('portfolio.index') }}" class="group bg-muted relative aspect-video overflow-hidden rounded-xl">
                        <img
                            src="{{ asset('assets/common/hero-image.svg') }}"
                            alt="{{ $project['title'] }}"
                            loading="lazy"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                        <div class="absolute bottom-0 left-0 p-4 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                            <h3 class="text-lg font-semibold text-white">{{ $project['title'] }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                @foreach (array_slice($featured, 2) as $project)
                    <a href="{{ route('portfolio.index') }}" class="group bg-muted relative aspect-square overflow-hidden rounded-xl">
                        <img
                            src="{{ asset('assets/common/hero-image.svg') }}"
                            alt="{{ $project['title'] }}"
                            loading="lazy"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                        <div class="absolute bottom-0 left-0 p-4 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                            <h3 class="text-lg font-semibold text-white">{{ $project['title'] }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        @include('pages.partials.cta')
    </main>
@endsection
