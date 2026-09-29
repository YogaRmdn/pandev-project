@extends('layouts.main')

@section('title', 'Portfolio | PanDev')

@section('description', 'Browse the projects and best work delivered by PanDev — web, mobile, desktop, IoT, and data solutions.')

@section('content')
    <main class="flex-1">
        <section class="mx-auto flex w-full max-w-6xl flex-col items-center px-4 py-16">
            <div class="text-primary text-center text-sm font-semibold tracking-wider uppercase">Portfolio</div>
            <h1 class="font-heading text-primary mt-2 text-center text-3xl font-bold tracking-tight sm:text-4xl">
                Our Work
            </h1>

            @include('pages.partials.carousel')

            <p class="text-muted-foreground mt-4 max-w-2xl text-center text-pretty text-base lg:text-xl">
                A collection of web, mobile, and desktop applications, plus design
                projects we have planned, built, and shipped.
            </p>

            <x-ui.button size="lg" href="{{ route('portfolio.index') }}" class="mt-8">
                View All Projects
                <x-lucide name="move-up-right" />
            </x-ui.button>
        </section>

        <section class="mx-auto w-full max-w-6xl px-4 pb-16">
            <div class="border-t pt-12">
                <h2 class="text-primary font-heading text-2xl font-bold tracking-tight sm:text-3xl">
                    Recent Projects
                </h2>
                <p class="text-muted-foreground mt-3 max-w-2xl text-pretty">
                    A closer look at the products our team has delivered recently.
                </p>
            </div>

            @if ($featured->isEmpty())
                <div class="text-muted-foreground mt-8 flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed p-16 text-center">
                    <x-lucide name="folder-open" class="text-muted-foreground/60 size-10" />
                    <p class="text-base font-medium text-foreground">No projects published yet</p>
                    <p class="text-sm">New projects are coming soon — check back shortly.</p>
                </div>
            @else
                <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featured as $project)
                        <a
                            href="{{ route('portfolio.show', $project->id) }}"
                            class="group bg-muted relative block aspect-[4/3] overflow-hidden rounded-xl border"
                        >
                            @if (filled($project->thumbnail))
                                <img
                                    src="{{ $project->thumbnail }}"
                                    alt="{{ $project->name }}"
                                    loading="lazy"
                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                />
                            @else
                                <span class="flex h-full w-full items-center justify-center">
                                    <x-lucide name="image" class="text-muted-foreground/50 size-10" />
                                </span>
                            @endif

                            {{-- Scrim + judul selalu terlihat: judul yang hanya muncul
                                 saat hover tidak terbaca di perangkat sentuh. --}}
                            <span class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></span>
                            <span class="absolute inset-x-0 bottom-0 p-4">
                                <span class="font-heading block text-base font-bold text-white">
                                    {{ $project->name }}
                                </span>
                                @if (filled($project->category))
                                    <span class="mt-1 block text-xs font-medium text-white/75">
                                        {{ $project->category }}
                                    </span>
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        @include('pages.partials.cta')
    </main>
@endsection
