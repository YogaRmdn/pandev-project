@extends('layouts.dashboard')

@section('content')
    <div class="space-y-4">
        <x-dashboard.navbar drawer-id="dashboard-drawer" title="Faktur"
            description="Kelola faktur dan tagihan projek"></x-dashboard.navbar>
        <div class="card gap-6 border p-6 shadow-sm w-full">
            <div class="flex gap-1.5 flex-wrap items-center justify-between border-b pb-4">
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

                    <button type="button" class="btn btn-primary" onclick="create_invoice_modal.showModal()">
                        <x-lucide name="plus" /> Tambah
                    </button>

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
            </div>

            <div class="px-0">
                <div class="relative w-full overflow-x-auto">
                    <table class="table">
                        <thead class="">
                            <tr class="hover:bg-base-200/60 transition-colors">
                                <th class="w-12">#</th>
                                <th class="">Date</th>
                                <th class="">Description</th>
                                <th class="">Status</th>
                                <th class="">Total</th>
                                <th class="w-32 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="">
                            @forelse ($invoices as $invoice)
                                @php
                                    $deleteName = 'delete-invoice-' . $invoice->id;
                                @endphp

                                <tr class="hover:bg-base-200/60 transition-colors">
                                    <td class="align-middle whitespace-nowrap">{{ $loop->iteration }}</td>
                                    <td class="align-middle whitespace-nowrap font-medium">
                                        {{ \App\Support\Format::date($invoice->date) }}</td>
                                    <td class="align-middle whitespace-nowrap">{{ $invoice->description }}</td>
                                    <td class="align-middle whitespace-nowrap">
                                        <span @class([
                                            'badge',
                                            'badge-success' => $invoice->status === \App\Enums\InvoiceStatus::PAID,
                                            'badge-warning' =>
                                                $invoice->status === \App\Enums\InvoiceStatus::PARTIALLY_PAID,
                                            'badge-error' => $invoice->status === \App\Enums\InvoiceStatus::UNPAID,
                                        ])>{{ $invoice->status->label() }}</span>
                                    </td>
                                    <td class="align-middle whitespace-nowrap font-medium">
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
                                            x-on:click="$dispatch('open-modal', '{{ $deleteName }}')"
                                            aria-label="Hapus invoice">
                                            <x-lucide name="trash-2" />
                                        </button>
                                    </td>
                                </tr>

                                <tr class="hover:bg-base-200/60 transition-colors">
                                    <td class="align-middle whitespace-nowrap p-0" colspan="6">
                                        <x-dashboard.invoice-detail :invoice="$invoice" />
                                        <x-ui.confirm-dialog :name="$deleteName" title="Hapus Tagihan?"
                                            description="Tagihan yang dihapus tidak dapat dikembalikan lagi.">
                                            <form method="POST"
                                                action="{{ route('dashboard.invoice.destroy', $invoice->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-error">Hapus</button>
                                            </form>
                                        </x-ui.confirm-dialog>
                                    </td>
                                </tr>

                                <tr class="hover:bg-base-200/60 transition-colors">
                                    <td class="align-middle p-0" colspan="6">
                                        <dialog id="edit_invoice_modal_{{ $invoice->id }}" class="modal whitespace-normal">
                                            <div class="modal-box sm:max-w-2xl">
                                                <h3 class="text-lg font-bold">Edit Tagihan / Invoice</h3>
                                                <p class="text-base-content/60 py-4 text-sm">Ubah tagihan yang sudah dibuat</p>
                                                <x-dashboard.invoice-form :action="route('dashboard.invoice.update', $invoice->id)" method="PUT" :invoice="$invoice" />
                                            </div>
                                            <form method="dialog" class="modal-backdrop">
                                                <button>close</button>
                                            </form>
                                        </dialog>
                                    </td>
                                </tr>
                            @empty
                                <tr class="hover:bg-base-200/60 transition-colors">
                                    <td class="align-middle whitespace-nowrap text-base-content/60 h-32 text-center"
                                        colspan="6">
                                        Belum ada data tagihan
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
