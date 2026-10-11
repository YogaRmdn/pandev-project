@extends('layouts.dashboard')

@section('page-title', 'Faktur')
@section('page-description', 'Kelola faktur dan tagihan projek')

@section('content')
    <div class="space-y-5">
        <x-dashboard.card title="Data Faktur" description="Berikut semua data faktur yang ada" icon="notepad-text"
            :count="$invoices->isNotEmpty() ? $invoices->count() : null">
            <x-slot:actions>
                <div class="w-full sm:max-w-xs">
                    <x-search-field placeholder="Cari deskripsi..." />
                </div>

                <button type="button" class="btn btn-primary" onclick="create_invoice_modal.showModal()">
                    <x-lucide name="plus" class="size-4" /> Tambah
                </button>
            </x-slot:actions>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-12">#</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th class="w-32 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($invoices as $invoice)
                            @php
                                $deleteName = 'delete-invoice-' . $invoice->id;
                            @endphp

                            <tr class="transition-colors hover:bg-base-200/60">
                                <td class="align-middle whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="align-middle whitespace-nowrap font-medium">
                                    {{ \App\Support\Format::date($invoice->date) }}</td>
                                <td class="align-middle whitespace-nowrap">{{ $invoice->description }}</td>
                                <td class="align-middle whitespace-nowrap">
                                    <span @class([
                                        'badge',
                                        'badge-success' => $invoice->status === \App\Enums\InvoiceStatus::PAID,
                                        'badge-warning' => $invoice->status === \App\Enums\InvoiceStatus::PARTIALLY_PAID,
                                        'badge-error' => $invoice->status === \App\Enums\InvoiceStatus::UNPAID,
                                    ])>{{ $invoice->status->label() }}</span>
                                </td>
                                <td class="align-middle whitespace-nowrap font-medium tabular-nums">
                                    {{ \App\Support\Format::idr($invoice->total) }}</td>
                                <td class="align-middle whitespace-nowrap text-right">
                                    <button type="button" class="btn btn-ghost btn-square size-8"
                                        onclick="document.getElementById('detail_invoice_modal_{{ $invoice->id }}').showModal()"
                                        aria-label="Detail invoice">
                                        <x-lucide name="external-link" />
                                    </button>

                                    <button type="button" class="btn btn-ghost btn-square size-8"
                                        onclick="document.getElementById('edit_invoice_modal_{{ $invoice->id }}').showModal()"
                                        aria-label="Edit invoice">
                                        <x-lucide name="pencil" />
                                    </button>

                                    <button type="button" class="btn btn-ghost btn-square size-8 hover:text-error"
                                        x-on:click="$dispatch('open-modal', '{{ $deleteName }}')" aria-label="Hapus invoice">
                                        <x-lucide name="trash-2" />
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td class="align-middle whitespace-nowrap p-0" colspan="6">
                                    <x-dashboard.invoice-detail :invoice="$invoice" />
                                    <x-ui.confirm-dialog :name="$deleteName" title="Hapus Tagihan?"
                                        description="Tagihan yang dihapus tidak dapat dikembalikan lagi.">
                                        <form method="POST" action="{{ route('dashboard.invoice.destroy', $invoice->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error">Hapus</button>
                                        </form>
                                    </x-ui.confirm-dialog>
                                </td>
                            </tr>

                            <tr>
                                <td class="align-middle p-0" colspan="6">
                                    <dialog id="edit_invoice_modal_{{ $invoice->id }}" class="modal whitespace-normal">
                                        <div class="modal-box sm:max-w-2xl">
                                            <h3 class="text-lg font-bold">Edit Tagihan / Invoice</h3>
                                            <p class="text-base-content/60 py-4 text-sm">Ubah tagihan yang sudah dibuat</p>
                                            <x-dashboard.invoice-form :action="route('dashboard.invoice.update', $invoice->id)" method="PUT"
                                                :invoice="$invoice" />
                                        </div>
                                        <form method="dialog" class="modal-backdrop">
                                            <button>close</button>
                                        </form>
                                    </dialog>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="h-32 text-center text-base-content/60" colspan="6">
                                    Belum ada data tagihan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-dashboard.card>

        <dialog id="create_invoice_modal" class="modal">
            <div class="modal-box sm:max-w-2xl">
                <h3 class="text-lg font-bold">Buat Tagihan / Invoice</h3>
                <p class="text-base-content/60 py-4 text-sm">Isi form di bawah ini untuk menambahkan tagihan</p>
                <x-dashboard.invoice-form :action="route('dashboard.invoice.store')" />
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </div>
@endsection
