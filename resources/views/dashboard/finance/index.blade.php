@extends('layouts.dashboard')

@section('content')
    <div class="space-y-4">
        <x-dashboard.navbar drawer-id="dashboard-drawer" title="Transaksi"
            description="Kelola data pemasukan dan pengeluaran" />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-dashboard.stat-card label="Pemasukan" :value="\App\Support\Format::idr($totalIncome)" icon="trending-up" />
            <x-dashboard.stat-card label="Pengeluaran" :value="\App\Support\Format::idr($totalExpense)" icon="trending-down" />
        </div>

        <div class="card gap-6 border p-6 shadow-sm w-full">
            <div class="flex flex-wrap items-center justify-between border-b pb-4">
                <div>
                    <div class="card-title flex items-center gap-2">
                        Data Transaksi
                        @if ($transactions->isNotEmpty())
                            <span class="badge badge-success px-1">Total {{ $transactions->count() }}</span>
                        @endif
                    </div>
                    <div class="text-base-content/60 text-sm">Berikut semua data transaksi yang ada</div>
                </div>

                <div class="flex items-center gap-2">
                    <x-search-field placeholder="Cari deskripsi..." />

                    <button type="button" class="btn btn-primary" onclick="create_transaction_modal.showModal()">
                        <x-lucide name="plus" /> Tambah
                    </button>

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
            </div>

            <div class="px-0">
                <div class="relative w-full overflow-x-auto">
                    <table class="table">
                        <thead class="">
                            <tr class="hover:bg-base-200/60 transition-colors">
                                <th class="w-12">#</th>
                                <th class="">Date</th>
                                <th class="">Total Amount</th>
                                <th class="">Description</th>
                                <th class="w-24 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="">
                            @forelse ($transactions as $transaction)
                                @php
                                    $isIncome = $transaction->type === \App\Enums\TransactionType::INCOME;
                                    $deleteName = 'delete-transaction-' . $transaction->id;
                                @endphp

                                <tr class="hover:bg-base-200/60 transition-colors">
                                    <td class="align-middle whitespace-nowrap">{{ $loop->iteration }}</td>
                                    <td class="align-middle whitespace-nowrap font-medium">
                                        {{ \App\Support\Format::date($transaction->date) }}</td>
                                    <td class="align-middle whitespace-nowrap">
                                        <span @class([
                                            'flex items-center gap-1',
                                            'text-green-700' => $isIncome,
                                            'text-red-700' => !$isIncome,
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
                                            x-on:click="$dispatch('open-modal', '{{ $deleteName }}')"
                                            aria-label="Hapus transaksi">
                                            <x-lucide name="trash-2" />
                                        </button>
                                    </td>
                                </tr>

                                <tr class="hover:bg-base-200/60 transition-colors">
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
                                            <form method="POST"
                                                action="{{ route('dashboard.finance.destroy', $transaction->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-error">Hapus</button>
                                            </form>
                                        </x-ui.confirm-dialog>
                                    </td>
                                </tr>
                            @empty
                                <tr class="hover:bg-base-200/60 transition-colors">
                                    <td class="align-middle whitespace-nowrap text-base-content/60 h-32 text-center"
                                        colspan="5">
                                        Belum ada data transaksi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
