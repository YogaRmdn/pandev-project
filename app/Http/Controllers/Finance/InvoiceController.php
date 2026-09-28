<?php

namespace App\Http\Controllers\Finance;

use App\Enums\InvoiceStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $invoices = Invoice::with('invoiceItems')
            ->when($request->filled('search'), fn ($query, $search) => $query->where('description', 'like', "%{$search}%"))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->latest('date')
            ->latest()
            ->get();

        return view('dashboard.invoice.index', [
            'invoices' => $invoices,
            'statuses' => InvoiceStatus::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(), $this->messages());
        $items = $data['invoice_items'];
        unset($data['invoice_items']);

        DB::transaction(function () use ($data, $items) {
            $invoice = Invoice::create($data);
            $this->syncItems($invoice, $items);
        });

        return back()->with('success', 'Tagihan berhasil dibuat');
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validate($this->rules(), $this->messages());
        $items = $data['invoice_items'];
        unset($data['invoice_items']);

        DB::transaction(function () use ($invoice, $data, $items) {
            $invoice->update($data);
            $this->syncItems($invoice, $items);
        });

        return back()->with('success', 'Tagihan berhasil diperbarui');
    }

    /**
     * Moving an invoice further towards a paid state books the matching slice
     * of the total as income. Only the delta that has not been booked yet is
     * recorded, so UNPAID → PAID books the full total, PARTIALLY_PAID → PAID
     * books the remaining half, and repeating a status books nothing.
     */
    public function updateStatus(Request $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([
                InvoiceStatus::PARTIALLY_PAID->value,
                InvoiceStatus::PAID->value,
            ])],
        ]);

        $target = InvoiceStatus::from($data['status']);
        $invoice->load('invoiceItems');
        $total = $invoice->total;

        DB::transaction(function () use ($invoice, $target, $total) {
            $booked = (float) Transaction::income()
                ->where('invoice_id', $invoice->id)
                ->sum('amount');

            $delta = round($total * $target->paidRatio() - $booked, 2);

            $invoice->update(['status' => $target->value]);

            if ($delta > 0) {
                Transaction::create([
                    'type' => TransactionType::INCOME,
                    'amount' => $delta,
                    'description' => $invoice->description.' - '.$target->label(),
                    'date' => now(),
                    'invoice_id' => $invoice->id,
                ]);
            }
        });

        return back()->with('success', 'Status invoice berhasil diperbarui');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return back()->with('success', 'Tagihan berhasil dihapus');
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load('invoiceItems');

        return Pdf::loadView('dashboard.invoice.pdf', ['invoice' => $invoice])
            ->setPaper('a4')
            ->download('invoice-'.strtoupper($invoice->id).'.pdf');
    }

    /**
     * @param  array<int, array{name: string, quantity: int|float, price: int|float}>  $items
     */
    private function syncItems(Invoice $invoice, array $items): void
    {
        $invoice->invoiceItems()->delete();
        $invoice->invoiceItems()->createMany($items);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'status' => ['nullable', Rule::enum(InvoiceStatus::class)],
            'invoice_items' => ['required', 'array', 'min:1'],
            'invoice_items.*.name' => ['required', 'string', 'max:255'],
            'invoice_items.*.quantity' => ['required', 'numeric', 'min:1'],
            'invoice_items.*.price' => ['required', 'numeric', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'description.required' => 'Deskripsi wajib diisi',
            'date.required' => 'Tanggal wajib diisi',
            'invoice_items.required' => 'Minimal 1 item transaksi',
            'invoice_items.min' => 'Minimal 1 item transaksi',
            'invoice_items.*.name.required' => 'Nama item wajib diisi',
            'invoice_items.*.quantity.required' => 'Jumlah wajib diisi',
            'invoice_items.*.quantity.min' => 'Jumlah minimal 1',
            'invoice_items.*.price.required' => 'Harga wajib diisi',
            'invoice_items.*.price.min' => 'Harga minimal 1',
        ];
    }
}
