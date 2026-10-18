@extends('layouts.main')

@section('title', $portfolio->name.' | PanDev')

@section('description', $portfolio->description)

@section('canonical', route('portfolio.show', $portfolio->id))

@section('og_type', 'article')

@if (filled($portfolio->thumbnail))
    @section('og_image', $portfolio->thumbnail)
@endif

{{-- Draft cuma bisa dibuka penulisnya, jadi jangan sampai terindeks. --}}
@if ($portfolio->status === \App\Enums\PortfolioStatus::DRAFT)
    @section('robots', 'noindex, nofollow')
@endif

@php
    // Galeri dipakai dua kali — Lightbox di konten dan daftar `image` pada
    // schema CreativeWork — jadi dihitung sekali di sini saja.
    $images = $portfolio->galery->pluck('image_url')->filter()->values();

    if ($images->isEmpty() && filled($portfolio->thumbnail)) {
        $images = collect([$portfolio->thumbnail]);
    }
@endphp

@push('json-ld')
    <x-json-ld :data="\App\Support\Seo::graph([
        \App\Support\Seo::breadcrumbs([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Portfolio', 'url' => route('portfolio')],
            ['name' => $portfolio->name, 'url' => route('portfolio.show', $portfolio->id)],
        ]),
        array_filter([
            '@type' => 'CreativeWork',
            'name' => $portfolio->name,
            'description' => \App\Support\Seo::excerpt($portfolio->description, 300),
            'url' => route('portfolio.show', $portfolio->id),
            'image' => $images->map(fn ($image) => \App\Support\Seo::image($image))->all(),
            'author' => ['@id' => url('/').'#organization'],
            'genre' => $portfolio->category,
            'keywords' => filled($portfolio->tech_stacks) ? implode(', ', $portfolio->tech_stacks) : null,
            'dateModified' => $portfolio->updated_at?->toAtomString(),
            'datePublished' => $portfolio->created_at?->toAtomString(),
        ]),
    ])" />
@endpush

@section('content')
    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
        <a href="{{ route('portfolio.index') }}" class="text-base-content/60 hover:text-base-content mb-4 inline-flex items-center gap-1 text-sm">
            <x-lucide name="chevron-left" /> Back to all projects
        </a>

        <div class="flex flex-col gap-6 lg:flex-row" x-data="{ index: 0, hovered: false }">
            <div class="w-full lg:w-2/3">
                <div
                    class="relative aspect-video w-full overflow-hidden rounded-lg border"
                    x-on:mouseenter="hovered = true"
                    x-on:mouseleave="hovered = false"
                >
                    @if ($images->isNotEmpty())
                        @foreach ($images as $image)
                            <img
                                src="{{ $image }}"
                                alt="{{ $portfolio->name }}"
                                x-show="index === {{ $loop->index }}"
                                x-transition.opacity
                                class="absolute inset-0 h-full w-full object-cover"
                            />
                        @endforeach

                        @if ($images->count() > 1)
                            <button type="button" class="btn btn-secondary btn-square absolute top-1/2 left-2 rounded-full bg-black/50 text-white transition-opacity hover:bg-black/70" x-on:click="index = index === 0 ? {{ $images->count() - 1 }} : index - 1" x-bind:class="hovered ? 'opacity-100' : 'opacity-0'" aria-label="Previous image">
                                <x-lucide name="chevron-left" class="size-5" />
                            </button>

                            <button type="button" class="btn btn-secondary btn-square absolute top-1/2 right-2 rounded-full bg-black/50 text-white transition-opacity hover:bg-black/70" x-on:click="index = index === {{ $images->count() - 1 }} ? 0 : index + 1" x-bind:class="hovered ? 'opacity-100' : 'opacity-0'" aria-label="Next image">
                                <x-lucide name="chevron-right" class="size-5" />
                            </button>
                        @endif
                    @else
                        <div class="bg-base-200 flex h-full w-full items-center justify-center">
                            <x-lucide name="image" class="text-base-content/60/50 size-16" />
                        </div>
                    @endif
                </div>

                @if ($images->count() > 1)
                    <div class="mt-4 flex snap-x snap-mandatory gap-2 overflow-x-auto pb-2">
                        @foreach ($images as $image)
                            <button
                                type="button"
                                x-on:click="index = {{ $loop->index }}"
                                x-bind:class="index === {{ $loop->index }} ? 'border-primary opacity-100 ring-2 ring-primary/20' : 'border-transparent opacity-60 hover:opacity-100'"
                                class="relative h-16 w-24 shrink-0 snap-center overflow-hidden rounded-lg border-2 transition-all"
                            >
                                <img src="{{ $image }}" alt="Thumbnail {{ $loop->iteration }}" class="absolute inset-0 h-full w-full object-cover" />
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="card cursor-glow gap-6 border p-6 shadow-sm h-fit w-full gap-0 lg:w-1/3">
                <div class="relative z-[1] flex flex-col gap-1.5">
                    <div>
                        <h1 class="font-heading text-2xl font-bold tracking-tight">{{ $portfolio->name }}</h1>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-full bg-sky-500/15 px-2.5 py-1 text-xs font-medium text-sky-700">
                                <x-lucide name="tag" class="size-3" /> {{ $portfolio->category }}
                            </span>
                            @if ($portfolio->status === \App\Enums\PortfolioStatus::DRAFT)
                                <span class="badge bg-amber-500/15 text-amber-700">Draft</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="relative z-[1] space-y-4">
                    <div>
                        <div class="text-base-content/60 text-sm font-semibold uppercase">Description</div>
                        <p class="mt-2 whitespace-pre-wrap">{{ $portfolio->description }}</p>
                    </div>

                    @if (! empty($portfolio->tech_stacks))
                        <div>
                            <div class="text-base-content/60 text-sm font-semibold uppercase">Tech Stacks</div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($portfolio->tech_stacks as $tech)
                                    <span class="bg-base-200 text-base-content rounded-lg border px-3 py-1 text-sm">{{ $tech }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <div class="text-base-content/60 text-sm font-semibold uppercase">Links</div>
                        <div class="mt-2 flex flex-wrap gap-3">
                            @php($demoLink = \App\Support\Format::url($portfolio->demo_link))
                            @php($repoLink = \App\Support\Format::url($portfolio->repository_link))

                            @if ($demoLink)
                                <a href="{{ $demoLink }}" class="btn btn-primary" target="_blank" rel="noopener noreferrer">
                                    <x-lucide name="external-link" class="mr-2" /> Live Demo
                                </a>
                            @endif

                            @if ($repoLink)
                                <a href="{{ $repoLink }}" class="btn btn-outline" target="_blank" rel="noopener noreferrer">
                                    <x-lucide name="code" class="mr-2" /> Repository
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
