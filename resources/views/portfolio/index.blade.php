@extends('layouts.main')

@section('title', 'Daftar Portfolio | PanDev')

@section('description', 'Lihat semua proyek dan karya terbaik yang telah dikerjakan oleh PanDev.')

@section('content')
    <main class="flex-1 space-y-4">
        <section class="py-16">
            <h1 class="font-heading text-primary text-center text-3xl font-bold tracking-tight uppercase sm:text-4xl">
                Semua Portfolio
            </h1>
            <p class="text-muted-foreground mx-auto mt-4 max-w-4xl text-center text-base lg:text-xl">
                Kumpulan proyek aplikasi dalam bentuk website, mobile, dan desktop
                serta design yang telah kami kerjakan dan bangun
            </p>
        </section>

        <section class="space-y-4 px-4 pb-16">
            <div class="flex flex-col gap-1 sm:flex-row">
                <x-search-field placeholder="Cari portfolio..." :value="$filters['search'] ?? ''" />
                <x-portfolio-filter :categories="$categories" :selected="$filters['categories'] ?? []" />
            </div>

            @if (($filters['search'] ?? null) || count($filters['categories'] ?? []) > 0)
                <div class="flex flex-wrap gap-2">
                    @if ($filters['search'])
                        <a
                            href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => null]) }}"
                            class="bg-secondary text-secondary-foreground hover:text-destructive inline-flex items-center gap-1 rounded-full px-2 py-1 text-sm"
                        >
                            Pencarian: {{ $filters['search'] }}
                            <x-lucide name="x" class="size-3" />
                        </a>
                    @endif

                    @foreach ($filters['categories'] as $category)
                        <a
                            href="{{ request()->fullUrlWithQuery(['category' => array_values(array_diff($filters['categories'], [$category])), 'page' => null]) }}"
                            class="bg-secondary text-secondary-foreground hover:text-destructive inline-flex items-center gap-1 rounded-full px-2 py-1 text-sm"
                        >
                            Kategori: {{ $category }}
                            <x-lucide name="x" class="size-3" />
                        </a>
                    @endforeach

                    <a
                        href="{{ route('portfolio.index') }}"
                        class="text-destructive hover:bg-destructive/10 inline-flex items-center gap-1 rounded-full px-2 py-1 text-sm"
                    >Hapus Semua</a>
                </div>
            @endif

            <section class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($portfolios as $portfolio)
                    <x-portfolio-card :portfolio="$portfolio" />
                @empty
                    <div class="text-muted-foreground col-span-3 flex h-64 items-center justify-center">
                        @if (($filters['search'] ?? null) || count($filters['categories'] ?? []) > 0)
                            Tidak ada portfolio yang sesuai dengan filter.
                        @else
                            Belum ada portfolio yang dipublikasikan.
                        @endif
                    </div>
                @endforelse
            </section>

            <x-pagination-bar :paginator="$portfolios" :limit-options="\App\Support\PortfolioOptions::LIMIT_OPTIONS" />
        </section>
    </main>
@endsection
