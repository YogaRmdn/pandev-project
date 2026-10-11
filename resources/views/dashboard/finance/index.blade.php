@extends('layouts.dashboard')

@section('page-title', 'Transaksi')
@section('page-description', 'Kelola data pemasukan dan pengeluaran')

@section('content')
    <div class="space-y-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-dashboard.stat-card label="Pemasukan" :value="\App\Support\Format::idr($totalIncome)" icon="trending-up"
                tone="success" />
            <x-dashboard.stat-card label="Pengeluaran" :value="\App\Support\Format::idr($totalExpense)"
                icon="trending-down" tone="error" />
            <x-dashboard.stat-card label="Saldo" :value="\App\Support\Format::idr($totalIncome - $totalExpense)"
                icon="wallet" tone="primary" />
        </div>

        <x-dashboard.card title="Data Transaksi" description="Berikut semua data transaksi yang ada" icon="wallet"
            :count="$transactions->isNotEmpty() ? $transactions->count() : null">
            <x-slot:actions>
                <div class="w-full sm:max-w-xs">
                    <x-search-field placeholder="Cari deskripsi..." />
                </div>

                <button type="button" class="btn btn-primary" onclick="create_transaction_modal.showModal()">
                    <x-lucide name="plus" class="size-4" /> Tambah
                </button>
            </x-slot:actions>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-12">#</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Description</th>
                            <th class="w-24 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($transactions as $transaction)
                            @php
                                $isIncome = $transaction->type === \App\Enums\TransactionType::INCOME;
                                $deleteName = 'delete-transaction-' . $transaction->id;
                            @endphp

                            <tr class="transition-colors hover:bg-base-200/60">
                                <td class="align-middle whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="align-middle whitespace-nowrap font-medium">
                                    {{ \App\Support\Format::date($transaction->date) }}</td>
                                <td class="align-middle whitespace-nowrap">
                                    <span @class([
                                        'flex items-center gap-1 font-semibold tabular-nums',
                                        'text-success' => $isIncome,
                                        'text-error' => !$isIncome,
                                    ])>
                                        <x-lucide :name="$isIncome ? 'plus' : 'minus'" class="size-3" />
                                        {{ \App\Support\Format::idr($transaction->amount) }}
                                    </span>
                                </td>
                                <td class="align-middle whitespace-nowrap">{{ $transaction->description }}</td>
                                <td class="align-middle whitespace-nowrap text-right">
                                    <button type="button" class="btn btn-ghost btn-square size-8"
                                        onclick="document.getElementById('edit_transaction_modal_{{ $transaction->id }}').showModal()"
                                        aria-label="Edit transaksi">
                                        <x-lucide name="pencil" />
                                    </button>

                                    <button type="button" class="btn btn-ghost btn-square size-8 hover:text-error"
                                        x-on:click="$dispatch('open-modal', '{{ $deleteName }}')" aria-label="Hapus transaksi">
                                        <x-lucide name="trash-2" />
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td class="align-middle p-0" colspan="5">
                                    <dialog id="edit_transaction_modal_{{ $transaction->id }}" class="modal whitespace-normal">
                                        <div class="modal-box sm:max-w-xl">
                                            <h3 class="text-lg font-bold">Edit Transaksi</h3>
                                            <p class="text-base-content/60 py-4 text-sm">Edit transaksi yang sudah dibuat</p>
                                            <x-dashboard.transaction-form :action="route('dashboard.finance.update', $transaction->id)" method="PUT"
                                                :transaction="$transaction" />
                                        </div>
                                        <form method="dialog" class="modal-backdrop">
                                            <button>close</button>
                                        </form>
                                    </dialog>

                                    <x-ui.confirm-dialog :name="$deleteName" title="Hapus transaksi?"
                                        description="Transaksi yang dihapus tidak dapat dikembalikan lagi">
                                        <form method="POST" action="{{ route('dashboard.finance.destroy', $transaction->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error">Hapus</button>
                                        </form>
                                    </x-ui.confirm-dialog>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="h-32 text-center text-base-content/60" colspan="5">
                                    Belum ada data transaksi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-dashboard.card>

        <dialog id="create_transaction_modal" class="modal">
            <div class="modal-box sm:max-w-md">
                <h3 class="text-lg font-bold">Buat Transaksi</h3>
                <p class="text-base-content/60 py-4 text-sm">Isi form di bawah ini untuk menambahkan transaksi</p>
                <x-dashboard.transaction-form :action="route('dashboard.finance.store')" />
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </div>
@endsection
