@extends('layouts.dashboard')

@php
    $user = auth()->user();
@endphp

@section('content')
    <div class="space-y-5">
        {{-- Hero / sambutan --}}
        <section
            class="relative overflow-hidden rounded-2xl border border-base-300/70 bg-gradient-to-br from-primary to-primary/75 p-6 text-primary-content shadow-sm md:p-8">
            <div class="pointer-events-none absolute -right-16 -top-20 size-56 rounded-full bg-white/10 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-24 right-24 size-56 rounded-full bg-white/5 blur-2xl"></div>

            <div class="relative flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                <div class="min-w-0">
                    <p class="flex items-center gap-2 text-xs font-medium uppercase tracking-wider text-primary-content/70">
                        <x-lucide name="sparkles" class="size-4" />
                        Ringkasan · {{ \App\Support\Format::date(now(), 'd F Y') }}
                    </p>
                    <h2 class="mt-2 font-heading text-2xl font-bold md:text-3xl">
                        Halo, {{ $user?->fullname ?? 'Sobat PanDev' }}
                    </h2>
                    <p class="mt-1.5 max-w-xl text-sm text-primary-content/80">
                        Kamu mengelola {{ $myPortfolioCount }} portfolio dengan
                        {{ $pendingInvoices }} faktur yang menunggu tindak lanjut.
                        Semangat berkarya hari ini!
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('dashboard.portfolio.create') }}"
                        class="btn btn-sm border-0 bg-base-100 text-primary hover:bg-base-200">
                        <x-lucide name="plus" class="size-4" /> Portfolio Baru
                    </a>
                    @if ($user?->isAdmin())
                        <a href="{{ route('dashboard.finance.index') }}"
                            class="btn btn-sm border-primary-content/40 bg-transparent text-primary-content hover:border-primary-content/40 hover:bg-primary-content/10">
                            <x-lucide name="wallet" class="size-4" /> Keuangan
                        </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Statistik --}}
        <div class="grid grid-cols-1 gap-4 {{ $user?->isAdmin() ? 'md:grid-cols-3' : 'md:grid-cols-2' }}">
            <x-dashboard.stat-card label="Total Proyek Diunggah" :value="number_format($totalPortfolios)" icon="folder"
                tone="primary" :hint="$myPortfolioCount.' milikmu'" />
            @if ($user?->isAdmin())
                <x-dashboard.stat-card label="Total Pendapatan" :value="\App\Support\Format::idr($totalIncome)"
                    icon="trending-up" tone="success" />
                <x-dashboard.stat-card label="Total Pengeluaran" :value="\App\Support\Format::idr($totalExpense)"
                    icon="trending-down" tone="error" />
            @else
                <x-dashboard.stat-card label="Faktur Menunggu" :value="number_format($pendingInvoices)"
                    icon="receipt" tone="warning" />
            @endif
        </div>

        {{-- Aktivitas --}}
        <div class="grid gap-6 lg:grid-cols-5">
            {{-- Portfolio terbaru --}}
            <div class="{{ $user?->isAdmin() ? 'lg:col-span-3' : 'lg:col-span-5' }}">
                <x-dashboard.card title="Portfolio Terbaru" description="Proyek yang terakhir kamu perbarui" icon="briefcase"
                    class="h-full">
                    <x-slot:actions>
                        <a href="{{ route('dashboard.portfolio.index') }}"
                            class="btn btn-ghost btn-xs gap-1 text-primary">
                            Kelola <x-lucide name="arrow-up-right" class="size-3.5" />
                        </a>
                    </x-slot:actions>

                    @forelse ($recentPortfolios as $portfolio)
                        <a href="{{ route('dashboard.portfolio.edit', $portfolio->id) }}"
                            class="flex items-center gap-3 border-b border-base-300/60 px-5 py-3 transition-colors last:border-b-0 hover:bg-base-200/50">
                            @if (filled($portfolio->thumbnail))
                                <img src="{{ $portfolio->thumbnail }}" alt="{{ $portfolio->name }}" loading="lazy"
                                    class="size-11 shrink-0 rounded-lg border border-base-300/60 object-cover" />
                            @else
                                <span
                                    class="grid size-11 shrink-0 place-items-center rounded-lg border border-base-300/60 bg-base-200 text-base-content/40">
                                    <x-lucide name="image" class="size-5" />
                                </span>
                            @endif

                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-semibold text-base-content">{{ $portfolio->name }}</span>
                                <span class="block truncate text-xs text-base-content/50">
                                    {{ $portfolio->category }} · {{ \App\Support\Format::relativeTime($portfolio->updated_at) }}
                                </span>
                            </span>

                            @if ($portfolio->status === \App\Enums\PortfolioStatus::PUBLISHED)
                                <span class="badge badge-sm border-0 bg-success/10 font-medium text-success">Terbit</span>
                            @else
                                <span class="badge badge-sm border-0 bg-warning/10 font-medium text-warning">Draft</span>
                            @endif
                        </a>
                    @empty
                        <div class="flex flex-col items-center gap-3 px-5 py-12 text-center">
                            <span class="grid size-12 place-items-center rounded-full bg-base-200 text-base-content/40">
                                <x-lucide name="folder-open" class="size-6" />
                            </span>
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-base-content">Belum ada portfolio</p>
                                <p class="text-xs text-base-content/50">Mulai unggah proyek pertamamu sekarang.</p>
                            </div>
                            <a href="{{ route('dashboard.portfolio.create') }}" class="btn btn-primary btn-sm">
                                <x-lucide name="plus" class="size-4" /> Portfolio Baru
                            </a>
                        </div>
                    @endforelse
                </x-dashboard.card>
            </div>

            {{-- Transaksi terbaru (admin saja) --}}
            @if ($user?->isAdmin())
            <div class="lg:col-span-2">
                <x-dashboard.card title="Aktivitas Keuangan" description="Transaksi terakhir yang tercatat" icon="activity"
                    class="h-full">
                    <x-slot:actions>
                        <a href="{{ route('dashboard.finance.index') }}" class="btn btn-ghost btn-xs gap-1 text-primary">
                            Detail <x-lucide name="arrow-up-right" class="size-3.5" />
                        </a>
                    </x-slot:actions>

                    @forelse ($recentTransactions as $transaction)
                        @php $isIncome = $transaction->type === \App\Enums\TransactionType::INCOME; @endphp
                        <div class="flex items-center gap-3 border-b border-base-300/60 px-5 py-3 last:border-b-0">
                            <span @class([
                                'grid size-9 shrink-0 place-items-center rounded-lg',
                                'bg-success/10 text-success' => $isIncome,
                                'bg-error/10 text-error' => !$isIncome,
                            ])>
                                <x-lucide :name="$isIncome ? 'trending-up' : 'trending-down'" class="size-4" />
                            </span>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-base-content">
                                    {{ $transaction->description ?: $transaction->type->label() }}
                                </p>
                                <p class="truncate text-xs text-base-content/50">
                                    {{ \App\Support\Format::date($transaction->date) }}
                                </p>
                            </div>

                            <span @class([
                                'shrink-0 text-sm font-semibold tabular-nums',
                                'text-success' => $isIncome,
                                'text-error' => !$isIncome,
                            ])>
                                {{ $isIncome ? '+' : '-' }}{{ \App\Support\Format::idr($transaction->amount) }}
                            </span>
                        </div>
                    @empty
                        <div class="flex flex-col items-center gap-3 px-5 py-12 text-center">
                            <span class="grid size-12 place-items-center rounded-full bg-base-200 text-base-content/40">
                                <x-lucide name="receipt" class="size-6" />
                            </span>
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-base-content">Belum ada transaksi</p>
                                <p class="text-xs text-base-content/50">Pemasukan & pengeluaran akan tampil di sini.</p>
                            </div>
                        </div>
                    @endforelse
                </x-dashboard.card>
            </div>
            @endif
        </div>
    </div>
@endsection
