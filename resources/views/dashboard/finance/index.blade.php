@extends('layouts.dashboard')

@section('content')
    <div class="space-y-4">
        <x-dashboard.header title="Keuangan" description="Kelola data pemasukan dan pengeluaran" />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-dashboard.stat-card label="Pemasukan" :value="\App\Support\Format::idr($totalIncome)" icon="trending-up" />
            <x-dashboard.stat-card label="Pengeluaran" :value="\App\Support\Format::idr($totalExpense)" icon="trending-down" />
            <x-dashboard.stat-card label="Saldo" :value="\App\Support\Format::idr($balance)" icon="wallet" />
        </div>

        <div class="card gap-6 border p-6 shadow-sm w-full gap-2">
            <div class="flex flex-col gap-1.5 flex flex-wrap items-center justify-between gap-2 border-b">
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

                    <x-ui.dialog
                        name="create-transaction"
                        title="Buat Transaksi"
                        description="Isi form di bawah ini untuk menambahkan transaksi"
                    >
                        <x-dashboard.transaction-form :action="route('dashboard.finance.store')" />
                    </x-ui.dialog>

                    <button type="button" class="btn btn-primary" x-on:click="$dispatch('open-modal', 'create-transaction')">
                        <x-lucide name="plus" /> Tambah
                    </button>
                </div>
            </div>

            <div class="px-0">
                <div class="relative w-full overflow-x-auto"><table class="table">
                    <thead class=""><tr class="hover:bg-base-200/60 transition-colors">
                            <th class="w-12">#</th>
                            <th class="">Date</th>
                            <th class="">Total Amount</th>
                            <th class="">Description</th>
                            <th class="w-24 text-right">Action</th></tr></thead>

                    <tbody class="">
                        @forelse ($transactions as $transaction)
                            @php
                                $isIncome = $transaction->type === \App\Enums\TransactionType::INCOME;
                                $editName = 'edit-transaction-'.$transaction->id;
                                $deleteName = 'delete-transaction-'.$transaction->id;
                            @endphp

                            <tr class="hover:bg-base-200/60 transition-colors">
                                <td class="align-middle whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="align-middle whitespace-nowrap font-medium">{{ \App\Support\Format::date($transaction->date) }}</td>
                                <td class="align-middle whitespace-nowrap">
                                    <span @class([
                                        'flex items-center gap-1',
                                        'text-green-700' => $isIncome,
                                        'text-red-700' => ! $isIncome,
                                    ])>
                                        <x-lucide :name="$isIncome ? 'plus' : 'minus'" class="size-3" />
                                        {{ \App\Support\Format::idr($transaction->amount) }}
                                    </span>
                                </td>
                                <td class="align-middle whitespace-nowrap">{{ $transaction->description }}</td>
                                <td class="align-middle whitespace-nowrap text-right">
                                    <button type="button" class="btn btn-ghost btn-square size-8" x-on:click="$dispatch('open-modal', '{{ $editName }}')" aria-label="Edit transaksi">
                                        <x-lucide name="pencil" />
                                    </button>

                                    <button type="button" class="btn btn-ghost btn-square size-8 hover:text-error" x-on:click="$dispatch('open-modal', '{{ $deleteName }}')" aria-label="Hapus transaksi">
                                        <x-lucide name="trash-2" />
                                    </button>
                                </td>
                            </tr>

                            <tr class="hover:bg-base-200/60 transition-colors">
                                <td class="align-middle whitespace-nowrap p-0" colspan="5">
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
                                            <button type="submit" class="btn btn-error">Hapus</button>
                                        </form>
                                    </x-ui.confirm-dialog>
                                </td>
                            </tr>
                        @empty
                            <tr class="hover:bg-base-200/60 transition-colors">
                                <td class="align-middle whitespace-nowrap text-base-content/60 h-32 text-center" colspan="5">
                                    Belum ada data transaksi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>
    </div>
@endsection
