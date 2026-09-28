@extends('layouts.dashboard')

@section('content')
    <div class="space-y-4">
        <x-dashboard.header title="Keuangan" description="Kelola data pemasukan dan pengeluaran" />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-dashboard.stat-card label="Pemasukan" :value="\App\Support\Format::idr($totalIncome)" icon="trending-up" />
            <x-dashboard.stat-card label="Pengeluaran" :value="\App\Support\Format::idr($totalExpense)" icon="trending-down" />
            <x-dashboard.stat-card label="Saldo" :value="\App\Support\Format::idr($balance)" icon="wallet" />
        </div>

        <x-ui.card class="w-full gap-2">
            <x-ui.card-header class="flex flex-wrap items-center justify-between gap-2 border-b">
                <div>
                    <x-ui.card-title class="flex items-center gap-2">
                        Data Transaksi
                        @if ($transactions->isNotEmpty())
                            <x-ui.badge class="bg-green-100! text-primary! px-1 hover:bg-green-100!">Total {{ $transactions->count() }}</x-ui.badge>
                        @endif
                    </x-ui.card-title>
                    <x-ui.card-description>Berikut semua data transaksi yang ada</x-ui.card-description>
                </div>

                <div class="flex items-center gap-2">
                    <x-search-field placeholder="Cari deskripsi..." />

                    <x-ui.dialog
                        name="create-transaction"
                        title="Buat Transaksi"
                        description="Isi form di bawah ini untuk menambahkan transaksi"
                    >
                        <x-dashboard.transaction-form :action="route('dashboard.finance.store')" />
                    </x-ui.dialog>

                    <x-ui.button type="button" x-on:click="$dispatch('open-modal', 'create-transaction')">
                        <x-lucide name="plus" /> Tambah
                    </x-ui.button>
                </div>
            </x-ui.card-header>

            <x-ui.card-content class="px-0">
                <x-ui.table>
                    <x-ui.table-header>
                        <x-ui.table-row>
                            <x-ui.table-head class="w-12">#</x-ui.table-head>
                            <x-ui.table-head>Date</x-ui.table-head>
                            <x-ui.table-head>Total Amount</x-ui.table-head>
                            <x-ui.table-head>Description</x-ui.table-head>
                            <x-ui.table-head class="w-24 text-right">Action</x-ui.table-head>
                        </x-ui.table-row>
                    </x-ui.table-header>

                    <x-ui.table-body>
                        @forelse ($transactions as $transaction)
                            @php
                                $isIncome = $transaction->type === \App\Enums\TransactionType::INCOME;
                                $editName = 'edit-transaction-'.$transaction->id;
                                $deleteName = 'delete-transaction-'.$transaction->id;
                            @endphp

                            <x-ui.table-row>
                                <x-ui.table-cell>{{ $loop->iteration }}</x-ui.table-cell>
                                <x-ui.table-cell class="font-medium">{{ \App\Support\Format::date($transaction->date) }}</x-ui.table-cell>
                                <x-ui.table-cell>
                                    <span @class([
                                        'flex items-center gap-1',
                                        'text-green-700' => $isIncome,
                                        'text-red-700' => ! $isIncome,
                                    ])>
                                        <x-lucide :name="$isIncome ? 'plus' : 'minus'" class="size-3" />
                                        {{ \App\Support\Format::idr($transaction->amount) }}
                                    </span>
                                </x-ui.table-cell>
                                <x-ui.table-cell>{{ $transaction->description }}</x-ui.table-cell>
                                <x-ui.table-cell class="text-right">
                                    <x-ui.button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="size-8"
                                        x-on:click="$dispatch('open-modal', '{{ $editName }}')"
                                        aria-label="Edit transaksi"
                                    >
                                        <x-lucide name="pencil" />
                                    </x-ui.button>

                                    <x-ui.button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="size-8 hover:text-destructive"
                                        x-on:click="$dispatch('open-modal', '{{ $deleteName }}')"
                                        aria-label="Hapus transaksi"
                                    >
                                        <x-lucide name="trash-2" />
                                    </x-ui.button>
                                </x-ui.table-cell>
                            </x-ui.table-row>

                            <x-ui.table-row>
                                <x-ui.table-cell colspan="5" class="p-0">
                                    <x-ui.dialog :name="$editName" title="Edit Transaksi" description="Edit transaksi yang sudah dibuat" width="sm:max-w-xl">
                                        <x-dashboard.transaction-form
                                            :action="route('dashboard.finance.update', $transaction->id)"
                                            method="PUT"
                                            :transaction="$transaction"
                                        />
                                    </x-ui.dialog>

                                    <x-ui.confirm-dialog
                                        :name="$deleteName"
                                        title="Hapus transaksi?"
                                        description="Transaksi yang dihapus tidak dapat dikembalikan lagi"
                                    >
                                        <form method="POST" action="{{ route('dashboard.finance.destroy', $transaction->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" variant="destructive">Hapus</x-ui.button>
                                        </form>
                                    </x-ui.confirm-dialog>
                                </x-ui.table-cell>
                            </x-ui.table-row>
                        @empty
                            <x-ui.table-row>
                                <x-ui.table-cell colspan="5" class="text-muted-foreground h-32 text-center">
                                    Belum ada data transaksi
                                </x-ui.table-cell>
                            </x-ui.table-row>
                        @endforelse
                    </x-ui.table-body>
                </x-ui.table>
            </x-ui.card-content>
        </x-ui.card>
    </div>
@endsection
