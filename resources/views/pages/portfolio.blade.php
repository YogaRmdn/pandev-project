@extends('layouts.main')

@section('title', 'Portfolio | PanDev')

@section('description', 'Browse the projects and best work delivered by PanDev — web, mobile, desktop, IoT, and data solutions.')

@section('keywords', 'portofolio software house, contoh website company profile, contoh aplikasi mobile, studi kasus pengembangan software, proyek iot indonesia, aplikasi gis indonesia, hasil kerja developer indonesia, jasa pembuatan aplikasi terpercaya, project showcase indonesia')

@push('json-ld')
    <x-json-ld :data="\App\Support\Seo::graph([
        \App\Support\Seo::breadcrumbs([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Portfolio', 'url' => route('portfolio')],
        ]),
        [
            '@type' => 'CollectionPage',
            'name' => 'PanDev Portfolio',
            'url' => route('portfolio'),
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => $featured->values()->map(fn ($project, $index) => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'url' => route('portfolio.show', $project->id),
                    'name' => $project->name,
                ])->all(),
            ],
        ],
    ])" />
@endpush

@section('content')
    <main class="flex-1">
        <section class="mx-auto flex w-full max-w-6xl flex-col items-center px-4 py-16">
            <div class="text-primary text-center text-sm font-semibold tracking-wider uppercase">Portfolio</div>
            <h1 class="font-heading text-gradient mt-2 text-center text-3xl font-bold tracking-tight sm:text-4xl">
                Our Work
            </h1>

            @include('pages.partials.carousel')

            <p class="text-base-content/60 mt-4 max-w-2xl text-center text-pretty text-base lg:text-xl">
                A collection of web, mobile, and desktop applications, plus design
            projects we have planned, built, and shipped.
            </p>

            <a href="{{ route('portfolio.index') }}" class="btn btn-primary btn-lg mt-8">
                View All Projects
                <x-lucide name="move-up-right" />
            </a>
        </section>

        <section class="mx-auto w-full max-w-6xl px-4 pb-16">
            <div class="border-t pt-12">
                <h2 class="text-primary font-heading text-2xl font-bold tracking-tight sm:text-3xl">
                    Recent Projects
                </h2>
                <p class="text-base-content/60 mt-3 max-w-2xl text-pretty">
                    A closer look at the products our team has delivered recently.
                </p>
            </div>

            @if ($featured->isEmpty())
                <div class="text-base-content/60 mt-8 flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed p-16 text-center">
                    <x-lucide name="folder-open" class="text-base-content/60/60 size-10" />
                    <p class="text-base font-medium text-base-content">No projects published yet</p>
                    <p class="text-sm">New projects are coming soon — check back shortly.</p>
                </div>
            @else
                {{-- Flex + `grow`: baris terakhir yang belum penuh (mis. 2 kartu
                     dari 3 kolom) ikut melebar sampai tepi kiri & kanan
                     kontainer, jadi tidak ada celah sisa di ujung baris.
                     Tinggi dipatok supaya kartu yang jadi lebih lebar tidak
                     ikut jadi lebih tinggi — semua baris tetap sejajar. --}}
                <div class="mt-8 flex flex-wrap gap-5">
                    @foreach ($featured as $project)
                        <a
                            href="{{ route('portfolio.show', $project->id) }}"
                            class="group bg-base-200 relative h-56 grow basis-full overflow-hidden rounded-xl border sm:h-64 sm:basis-[calc((100%_-_1.25rem)/2)] lg:h-72 lg:basis-[calc((100%_-_2.5rem)/3)]"
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
                                    <x-lucide name="image" class="text-base-content/60/50 size-10" />
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
