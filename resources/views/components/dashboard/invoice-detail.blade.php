@props(['invoice'])

@php $total = $invoice->total; @endphp

<dialog id="detail_invoice_modal_{{ $invoice->id }}" class="modal whitespace-normal">
    <div class="modal-box sm:max-w-2xl">

        <div class="space-y-4">
        <div class="flex items-center gap-3">
            <img src="{{ asset('assets/common/logo-mark.png') }}" alt="PanDev" width="48" height="48" class="size-12 object-contain" />
            <div>
                <h2 class="text-primary text-lg font-bold">Pandev</h2>
                <p class="text-base-content/60 text-sm">Digital Agency Indonesia</p>
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

        <div class="relative w-full overflow-x-auto"><table class="table">
            <thead class=""><tr class="hover:bg-base-200/60 transition-colors">
                    <th class="w-10">#</th>
                    <th class="">Item Transaksi</th>
                    <th class="text-right">Harga</th>
                    <th class="w-16 text-right">Jumlah</th>
                    <th class="text-right">Total</th></tr></thead>
            <tbody class="">
                @foreach ($invoice->invoiceItems as $item)
                    <tr class="hover:bg-base-200/60 transition-colors">
                        <td class="align-middle whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="align-middle whitespace-nowrap">{{ $item->name }}</td>
                        <td class="align-middle whitespace-nowrap text-right">{{ \App\Support\Format::idr($item->price) }}</td>
                        <td class="align-middle whitespace-nowrap text-right">{{ $item->quantity }}</td>
                        <td class="align-middle whitespace-nowrap text-right font-medium">
                            {{ \App\Support\Format::idr($item->quantity * $item->price) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table></div>

        <div class="bg-primary ml-auto w-fit rounded-md p-3 font-semibold text-white">
            Total: {{ \App\Support\Format::idr($total) }}
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2">
            @if ($invoice->status === \App\Enums\InvoiceStatus::UNPAID)
                <form method="POST" action="{{ route('dashboard.invoice.status', $invoice->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="PARTIALLY_PAID">
                    <button type="submit" class="btn btn-primary bg-amber-700! text-white! hover:bg-amber-700/90!">
                        <x-lucide name="circle-dashed-check" /> Sebagian dibayar / DP
                    </button>
                </form>

                <form method="POST" action="{{ route('dashboard.invoice.status', $invoice->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="PAID">
                    <button type="submit" class="btn btn-primary">
                        <x-lucide name="badge-check" /> Lunas
                    </button>
                </form>
            @elseif ($invoice->status === \App\Enums\InvoiceStatus::PARTIALLY_PAID)
                {{-- The original showed a disabled "Lunas" button here with no
                     handler, so the invoice could never actually be settled
                     from a PARTIALLY_PAID state. It is wired up now. --}}
                <form method="POST" action="{{ route('dashboard.invoice.status', $invoice->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="PAID">
                    <button type="submit" class="btn btn-primary">
                        <x-lucide name="badge-check" /> Lunas
                    </button>
                </form>
            @endif

            <a href="{{ route('dashboard.invoice.pdf', $invoice->id) }}" class="btn btn-outline">
                <x-lucide name="printer" /> Cetak Invoice
            </a>
        </div>
    </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
