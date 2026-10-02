@extends('layouts.dashboard')

@section('content')
    <div class="space-y-4">
        <x-dashboard.header title="Faktur" description="Kelola faktur dan tagihan projek" />

        <div class="card gap-6 border p-6 shadow-sm w-full gap-2">
            <div class="flex flex-col gap-1.5 flex flex-wrap items-center justify-between gap-2 border-b">
                <div>
                    <div class="card-title flex items-center gap-2">
                        Data Faktur
                        @if ($invoices->isNotEmpty())
                            <span class="badge badge-success px-1">Total {{ $invoices->count() }}</span>
                        @endif
                    </div>
                    <div class="text-base-content/60 text-sm">Berikut semua data faktur yang ada</div>
                </div>

                <div class="flex items-center gap-2">
                    <x-search-field placeholder="Cari deskripsi..." />

                    <x-ui.dialog
                        name="create-invoice"
                        title="Buat Tagihan / Invoice"
                        description="Isi form di bawah ini untuk menambahkan tagihan"
                        width="sm:max-w-2xl"
                    >
                        <x-dashboard.invoice-form :action="route('dashboard.invoice.store')" />
                    </x-ui.dialog>

                    <button type="button" class="btn btn-primary" x-on:click="$dispatch('open-modal', 'create-invoice')">
                        <x-lucide name="plus" /> Tambah
                    </button>
                </div>
            </div>

            <div class="px-0">
                <div class="relative w-full overflow-x-auto"><table class="table">
                    <thead class=""><tr class="hover:bg-base-200/60 transition-colors">
                            <th class="w-12">#</th>
                            <th class="">Date</th>
                            <th class="">Description</th>
                            <th class="">Status</th>
                            <th class="">Total</th>
                            <th class="w-32 text-right">Action</th></tr></thead>

                    <tbody class="">
                        @forelse ($invoices as $invoice)
                            @php
                                $detailName = 'detail-invoice-'.$invoice->id;
                                $editName = 'edit-invoice-'.$invoice->id;
                                $deleteName = 'delete-invoice-'.$invoice->id;
                            @endphp

                            <tr class="hover:bg-base-200/60 transition-colors">
                                <td class="align-middle whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="align-middle whitespace-nowrap font-medium">{{ \App\Support\Format::date($invoice->date) }}</td>
                                <td class="align-middle whitespace-nowrap">{{ $invoice->description }}</td>
                                <td class="align-middle whitespace-nowrap">
                                    <span @class(['badge',
                                        'badge-success' => $invoice->status === \App\Enums\InvoiceStatus::PAID,
                                        'badge-warning' => $invoice->status === \App\Enums\InvoiceStatus::PARTIALLY_PAID,
                                        'badge-error' => $invoice->status === \App\Enums\InvoiceStatus::UNPAID,
                                    ])>{{ $invoice->status->label() }}</span>
                                </td>
                                <td class="align-middle whitespace-nowrap font-medium">{{ \App\Support\Format::idr($invoice->total) }}</td>
                                <td class="align-middle whitespace-nowrap text-right">
                                    <button type="button" class="btn btn-ghost btn-square size-8" x-on:click="$dispatch('open-modal', '{{ $detailName }}')" aria-label="Detail invoice">
                                        <x-lucide name="external-link" />
                                    </button>

                                    <button type="button" class="btn btn-ghost btn-square size-8" x-on:click="$dispatch('open-modal', '{{ $editName }}')" aria-label="Edit invoice">
                                        <x-lucide name="pencil" />
                                    </button>

                                    <button type="button" class="btn btn-ghost btn-square size-8 hover:text-error" x-on:click="$dispatch('open-modal', '{{ $deleteName }}')" aria-label="Hapus invoice">
                                        <x-lucide name="trash-2" />
                                    </button>
                                </td>
                            </tr>

                            <tr class="hover:bg-base-200/60 transition-colors">
                                <td class="align-middle whitespace-nowrap p-0" colspan="6">
                                    <x-dashboard.invoice-detail :invoice="$invoice" :name="$detailName" />
                                    <x-ui.confirm-dialog
                                        :name="$deleteName"
                                        title="Hapus Tagihan?"
                                        description="Tagihan yang dihapus tidak dapat dikembalikan lagi."
                                    >
                                        <form method="POST" action="{{ route('dashboard.invoice.destroy', $invoice->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error">Hapus</button>
                                        </form>
                                    </x-ui.confirm-dialog>
                                </td>
                            </tr>

                            <tr class="hover:bg-base-200/60 transition-colors">
                                <td class="align-middle whitespace-nowrap p-0" colspan="6">
                                    <x-ui.dialog
                                        :name="$editName"
                                        title="Edit Tagihan / Invoice"
                                        description="Ubah tagihan yang sudah dibuat"
                                        width="sm:max-w-2xl"
                                    >
                                        <x-dashboard.invoice-form
                                            :action="route('dashboard.invoice.update', $invoice->id)"
                                            method="PUT"
                                            :invoice="$invoice"
                                        />
                                    </x-ui.dialog>
                                </td>
                            </tr>
                        @empty
                            <tr class="hover:bg-base-200/60 transition-colors">
                                <td class="align-middle whitespace-nowrap text-base-content/60 h-32 text-center" colspan="6">
                                    Belum ada data tagihan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>
    </div>
@endsection
