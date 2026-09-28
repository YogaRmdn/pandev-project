@extends('layouts.main')

@section('title', $portfolio->name.' | PanDev')

@section('description', $portfolio->description)

@section('content')
    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
        <a href="{{ route('portfolio.index') }}" class="text-muted-foreground hover:text-foreground mb-4 inline-flex items-center gap-1 text-sm">
            <x-lucide name="chevron-left" /> Kembali ke daftar portfolio
        </a>

        @php
            $images = $portfolio->galery->pluck('image_url')->filter()->values();

            if ($images->isEmpty() && filled($portfolio->thumbnail)) {
                $images = collect([$portfolio->thumbnail]);
            }
        @endphp

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
                            <x-ui.button
                                type="button"
                                variant="secondary"
                                size="icon"
                                x-on:click="index = index === 0 ? {{ $images->count() - 1 }} : index - 1"
                                x-bind:class="hovered ? 'opacity-100' : 'opacity-0'"
                                class="absolute top-1/2 left-2 rounded-full bg-black/50 text-white transition-opacity hover:bg-black/70"
                                aria-label="Gambar sebelumnya"
                            >
                                <x-lucide name="chevron-left" class="size-5" />
                            </x-ui.button>

                            <x-ui.button
                                type="button"
                                variant="secondary"
                                size="icon"
                                x-on:click="index = index === {{ $images->count() - 1 }} ? 0 : index + 1"
                                x-bind:class="hovered ? 'opacity-100' : 'opacity-0'"
                                class="absolute top-1/2 right-2 rounded-full bg-black/50 text-white transition-opacity hover:bg-black/70"
                                aria-label="Gambar berikutnya"
                            >
                                <x-lucide name="chevron-right" class="size-5" />
                            </x-ui.button>
                        @endif
                    @else
                        <div class="bg-muted flex h-full w-full items-center justify-center">
                            <x-lucide name="image" class="text-muted-foreground/50 size-16" />
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

            <x-ui.card class="h-fit w-full lg:w-1/3">
                <x-ui.card-header>
                    <div>
                        <h1 class="text-2xl font-bold">{{ $portfolio->name }}</h1>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="bg-primary/10 text-primary rounded-full px-2 py-1 text-sm font-medium">{{ $portfolio->category }}</span>
                            @if ($portfolio->status === \App\Enums\PortfolioStatus::DRAFT)
                                <x-ui.badge class="bg-muted text-muted-foreground">Draft</x-ui.badge>
                            @endif
                        </div>
                    </div>
                </x-ui.card-header>

                <x-ui.card-content class="space-y-4">
                    <div>
                        <div class="text-muted-foreground text-sm font-semibold uppercase">Description</div>
                        <p class="mt-2 whitespace-pre-wrap">{{ $portfolio->description }}</p>
                    </div>

                    @if (! empty($portfolio->tech_stacks))
                        <div>
                            <div class="text-muted-foreground text-sm font-semibold uppercase">Tech Stacks</div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($portfolio->tech_stacks as $tech)
                                    <span class="bg-secondary text-secondary-foreground rounded-lg border px-3 py-1 text-sm">{{ $tech }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <div class="text-muted-foreground text-sm font-semibold uppercase">Links</div>
                        <div class="mt-2 flex flex-wrap gap-3">
                            @php($demoLink = \App\Support\Format::url($portfolio->demo_link))
                            @php($repoLink = \App\Support\Format::url($portfolio->repository_link))

                            @if ($demoLink)
                                <x-ui.button href="{{ $demoLink }}" target="_blank" rel="noopener noreferrer">
                                    <x-lucide name="external-link" class="mr-2" /> Lihat Demo
                                </x-ui.button>
                            @endif

                            @if ($repoLink)
                                <x-ui.button href="{{ $repoLink }}" variant="outline" target="_blank" rel="noopener noreferrer">
                                    <x-lucide name="code" class="mr-2" /> Repository
                                </x-ui.button>
                            @endif
                        </div>
                    </div>
                </x-ui.card-content>
            </x-ui.card>
        </div>
    </main>
@endsection
