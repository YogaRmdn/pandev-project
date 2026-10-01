@props(['invoice', 'name'])

@php $total = $invoice->total; @endphp

<x-ui.dialog :name="$name" width="sm:max-w-2xl">
    <div class="space-y-4">
        <div class="flex items-center gap-3">
            <img src="{{ asset('assets/common/logo-mark.png') }}" alt="PanDev" width="48" height="48" class="size-12 object-contain" />
            <div>
                <h2 class="text-primary text-lg font-bold">Pandev</h2>
                <p class="text-muted-foreground text-sm">Digital Agency Indonesia</p>
            </div>
        </div>

        <div class="text-primary flex items-center gap-4 font-bold uppercase">
            <div class="bg-primary h-1 w-full"></div>
            Invoice
            <div class="bg-primary h-1 w-1/8"></div>
        </div>

        <div class="text-sm">
            Tanggal: <span class="font-medium">{{ \App\Support\Format::date($invoice->date, 'd-m-Y') }}</span>
        </div>

        <x-ui.table>
            <x-ui.table-header>
                <x-ui.table-row>
                    <x-ui.table-head class="w-10">#</x-ui.table-head>
                    <x-ui.table-head>Item Transaksi</x-ui.table-head>
                    <x-ui.table-head class="text-right">Harga</x-ui.table-head>
                    <x-ui.table-head class="w-16 text-right">Jumlah</x-ui.table-head>
                    <x-ui.table-head class="text-right">Total</x-ui.table-head>
                </x-ui.table-row>
            </x-ui.table-header>
            <x-ui.table-body>
                @foreach ($invoice->invoiceItems as $item)
                    <x-ui.table-row>
                        <x-ui.table-cell>{{ $loop->iteration }}</x-ui.table-cell>
                        <x-ui.table-cell>{{ $item->name }}</x-ui.table-cell>
                        <x-ui.table-cell class="text-right">{{ \App\Support\Format::idr($item->price) }}</x-ui.table-cell>
                        <x-ui.table-cell class="text-right">{{ $item->quantity }}</x-ui.table-cell>
                        <x-ui.table-cell class="text-right font-medium">
                            {{ \App\Support\Format::idr($item->quantity * $item->price) }}
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @endforeach
            </x-ui.table-body>
        </x-ui.table>

        <div class="bg-primary ml-auto w-fit rounded-md p-3 font-semibold text-white">
            Total: {{ \App\Support\Format::idr($total) }}
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2">
            @if ($invoice->status === \App\Enums\InvoiceStatus::UNPAID)
                <form method="POST" action="{{ route('dashboard.invoice.status', $invoice->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="PARTIALLY_PAID">
                    <x-ui.button type="submit" class="bg-amber-700! text-white! hover:bg-amber-700/90!">
                        <x-lucide name="circle-dashed-check" /> Sebagian dibayar / DP
                    </x-ui.button>
                </form>

                <form method="POST" action="{{ route('dashboard.invoice.status', $invoice->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="PAID">
                    <x-ui.button type="submit">
                        <x-lucide name="badge-check" /> Lunas
                    </x-ui.button>
                </form>
            @elseif ($invoice->status === \App\Enums\InvoiceStatus::PARTIALLY_PAID)
                {{-- The original showed a disabled "Lunas" button here with no
                     handler, so the invoice could never actually be settled
                     from a PARTIALLY_PAID state. It is wired up now. --}}
                <form method="POST" action="{{ route('dashboard.invoice.status', $invoice->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="PAID">
                    <x-ui.button type="submit">
                        <x-lucide name="badge-check" /> Lunas
                    </x-ui.button>
                </form>
            @endif

            <x-ui.button
                href="{{ route('dashboard.invoice.pdf', $invoice->id) }}"
                variant="outline"
            >
                <x-lucide name="printer" /> Cetak Invoice
            </x-ui.button>
        </div>
    </div>
</x-ui.dialog>
