@extends('layouts.dashboard')

@section('page-title', 'Portfolio')
@section('page-description', 'Kelola, tambah, edit, atau hapus portfolio Anda')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-wrap items-center gap-2">
            <div class="w-full min-w-0 sm:w-72 sm:max-w-md sm:flex-1">
                <x-search-field placeholder="Cari portfolio..." :value="$filters['search'] ?? ''" />
            </div>

            <x-ui.dialog name="portfolio-filter" title="Filter Portfolio"
                description="Pilih item-item di bawah ini untuk mengfilter portfolio Anda." width="sm:max-w-md">
                <form method="GET" action="{{ route('dashboard.portfolio.index') }}" class="space-y-4">
                    @if ($filters['search'])
                        <input type="hidden" name="search" value="{{ $filters['search'] }}">
                    @endif

                    <div>
                        <div class="mb-2 text-sm font-medium">Status</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($statuses as $status)
                                @php $checked = in_array($status['value'], $filters['statuses'] ?? [], true) @endphp
                                <label
                                    class="border-base-300 inline-flex cursor-pointer items-center gap-1.5 rounded-md border px-3 py-1.5 text-sm has-[:checked]:border-primary has-[:checked]:bg-primary/10">
                                    <input type="checkbox" name="status[]" value="{{ $status['value'] }}"
                                        @checked($checked) class="accent-primary">
                                    {{ $status['label'] }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 text-sm font-medium">Kategori</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($categories as $category)
                                @php $checked = in_array($category, $filters['categories'] ?? [], true) @endphp
                                <label
                                    class="border-base-300 inline-flex cursor-pointer items-center gap-1.5 rounded-md border px-3 py-1.5 text-sm has-[:checked]:border-primary has-[:checked]:bg-primary/10">
                                    <input type="checkbox" name="category[]" value="{{ $category }}"
                                        @checked($checked) class="accent-primary">
                                    {{ $category }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" class="btn btn-outline" x-on:click="$store.modals.close('portfolio-filter')">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">Terapkan filter</button>
                    </div>
                </form>
            </x-ui.dialog>

            <button type="button" class="btn" x-on:click="$dispatch('open-modal', 'portfolio-filter')">
                <x-lucide name="filter" />
                Filter
                @if (count($filters['statuses'] ?? []) + count($filters['categories'] ?? []) > 0)
                    <span
                        class="bg-primary-foreground text-primary flex size-5 items-center justify-center rounded-full text-xs">
                        {{ count($filters['statuses'] ?? []) + count($filters['categories'] ?? []) }}
                    </span>
                @endif
            </button>

            <a href="{{ route('dashboard.portfolio.create') }}" class="btn btn-primary ml-auto">
                <x-lucide name="plus" /> Tambah
            </a>
        </div>

        @if (($filters['search'] ?? null) || count($filters['categories'] ?? []) > 0 || count($filters['statuses'] ?? []) > 0)
            <div class="flex flex-wrap gap-2">
                @if ($filters['search'])
                    <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => null]) }}"
                        class="bg-base-200 text-base-content hover:text-error inline-flex items-center gap-1 rounded-full px-2 py-1 text-sm">
                        Pencarian: {{ $filters['search'] }}
                        <x-lucide name="x" class="size-3" />
                    </a>
                @endif

                @foreach ($filters['statuses'] ?? [] as $status)
                    <a href="{{ request()->fullUrlWithQuery(['status' => array_values(array_diff($filters['statuses'], [$status])), 'page' => null]) }}"
                        class="bg-base-200 text-base-content hover:text-error inline-flex items-center gap-1 rounded-full px-2 py-1 text-sm">
                        Status: {{ $statuses[$loop->index]['label'] ?? $status }}
                        <x-lucide name="x" class="size-3" />
                    </a>
                @endforeach

                @foreach ($filters['categories'] ?? [] as $category)
                    <a href="{{ request()->fullUrlWithQuery(['category' => array_values(array_diff($filters['categories'], [$category])), 'page' => null]) }}"
                        class="bg-base-200 text-base-content hover:text-error inline-flex items-center gap-1 rounded-full px-2 py-1 text-sm">
                        Kategori: {{ $category }}
                        <x-lucide name="x" class="size-3" />
                    </a>
                @endforeach

                <a href="{{ route('dashboard.portfolio.index') }}"
                    class="text-error hover:bg-error/10 inline-flex items-center gap-1 rounded-full px-2 py-1 text-sm">Hapus
                    Semua</a>
            </div>
        @endif

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($portfolios as $portfolio)
                <x-dashboard.portfolio-card :portfolio="$portfolio" />
            @empty
                <div
                    class="flex h-72 flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-base-300 bg-base-100 px-6 text-center sm:col-span-2 xl:col-span-3">
                    <span class="grid size-14 place-items-center rounded-full bg-base-200 text-base-content/40">
                        <x-lucide name="folder-open" class="size-7" />
                    </span>
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-base-content/70">
                            @if (($filters['search'] ?? null) || count($filters['categories'] ?? []) > 0 || count($filters['statuses'] ?? []) > 0)
                                Tidak ada portfolio yang sesuai dengan filter.
                            @else
                                Anda belum menambahkan portfolio.
                            @endif
                        </p>
                        @if (! (($filters['search'] ?? null) || count($filters['categories'] ?? []) > 0 || count($filters['statuses'] ?? []) > 0))
                            <p class="text-xs text-base-content/50">Mulai unggah proyek pertamamu lewat tombol "Tambah".</p>
                        @endif
                    </div>
                </div>
            @endforelse
        </section>

        <x-pagination-bar :paginator="$portfolios" :limit-options="\App\Support\PortfolioOptions::LIMIT_OPTIONS" />
    </div>
@endsection
