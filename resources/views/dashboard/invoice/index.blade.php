@extends('layouts.dashboard')

@section('content')
    <div class="space-y-4">
        <x-dashboard.header title="Faktur" description="Kelola faktur dan tagihan projek" />

        <x-ui.card class="w-full gap-2">
            <x-ui.card-header class="flex flex-wrap items-center justify-between gap-2 border-b">
                <div>
                    <x-ui.card-title class="flex items-center gap-2">
                        Data Faktur
                        @if ($invoices->isNotEmpty())
                            <x-ui.badge class="bg-green-100! text-primary! px-1 hover:bg-green-100!">Total {{ $invoices->count() }}</x-ui.badge>
                        @endif
                    </x-ui.card-title>
                    <x-ui.card-description>Berikut semua data faktur yang ada</x-ui.card-description>
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

                    <x-ui.button type="button" x-on:click="$dispatch('open-modal', 'create-invoice')">
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
                            <x-ui.table-head>Description</x-ui.table-head>
                            <x-ui.table-head>Status</x-ui.table-head>
                            <x-ui.table-head>Total</x-ui.table-head>
                            <x-ui.table-head class="w-32 text-right">Action</x-ui.table-head>
                        </x-ui.table-row>
                    </x-ui.table-header>

                    <x-ui.table-body>
                        @forelse ($invoices as $invoice)
                            @php
                                $detailName = 'detail-invoice-'.$invoice->id;
                                $editName = 'edit-invoice-'.$invoice->id;
                                $deleteName = 'delete-invoice-'.$invoice->id;
                            @endphp

                            <x-ui.table-row>
                                <x-ui.table-cell>{{ $loop->iteration }}</x-ui.table-cell>
                                <x-ui.table-cell class="font-medium">{{ \App\Support\Format::date($invoice->date) }}</x-ui.table-cell>
                                <x-ui.table-cell>{{ $invoice->description }}</x-ui.table-cell>
                                <x-ui.table-cell>
                                    <x-ui.badge @class([
                                        'bg-green-100! text-black hover:bg-green-100!' => $invoice->status === \App\Enums\InvoiceStatus::PAID,
                                        'bg-yellow-100! text-black hover:bg-yellow-100!' => $invoice->status === \App\Enums\InvoiceStatus::PARTIALLY_PAID,
                                        'bg-red-100! text-black hover:bg-red-100!' => $invoice->status === \App\Enums\InvoiceStatus::UNPAID,
                                    ])>{{ $invoice->status->label() }}</x-ui.badge>
                                </x-ui.table-cell>
                                <x-ui.table-cell class="font-medium">{{ \App\Support\Format::idr($invoice->total) }}</x-ui.table-cell>
                                <x-ui.table-cell class="text-right">
                                    <x-ui.button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="size-8"
                                        x-on:click="$dispatch('open-modal', '{{ $detailName }}')"
                                        aria-label="Detail invoice"
                                    >
                                        <x-lucide name="external-link" />
                                    </x-ui.button>

                                    <x-ui.button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="size-8"
                                        x-on:click="$dispatch('open-modal', '{{ $editName }}')"
                                        aria-label="Edit invoice"
                                    >
                                        <x-lucide name="pencil" />
                                    </x-ui.button>

                                    <x-ui.button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="size-8 hover:text-destructive"
                                        x-on:click="$dispatch('open-modal', '{{ $deleteName }}')"
                                        aria-label="Hapus invoice"
                                    >
                                        <x-lucide name="trash-2" />
                                    </x-ui.button>
                                </x-ui.table-cell>
                            </x-ui.table-row>

                            <x-ui.table-row>
                                <x-ui.table-cell colspan="6" class="p-0">
                                    <x-dashboard.invoice-detail :invoice="$invoice" :name="$detailName" />
                                    <x-ui.confirm-dialog
                                        :name="$deleteName"
                                        title="Hapus Tagihan?"
                                        description="Tagihan yang dihapus tidak dapat dikembalikan lagi."
                                    >
                                        <form method="POST" action="{{ route('dashboard.invoice.destroy', $invoice->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" variant="destructive">Hapus</x-ui.button>
                                        </form>
                                    </x-ui.confirm-dialog>
                                </x-ui.table-cell>
                            </x-ui.table-row>

                            <x-ui.table-row>
                                <x-ui.table-cell colspan="6" class="p-0">
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
                                </x-ui.table-cell>
                            </x-ui.table-row>
                        @empty
                            <x-ui.table-row>
                                <x-ui.table-cell colspan="6" class="text-muted-foreground h-32 text-center">
                                    Belum ada data tagihan
                                </x-ui.table-cell>
                            </x-ui.table-row>
                        @endforelse
                    </x-ui.table-body>
                </x-ui.table>
            </x-ui.card-content>
        </x-ui.card>
    </div>
@endsection
