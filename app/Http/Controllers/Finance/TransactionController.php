<?php

namespace App\Http\Controllers\Finance;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $transactions = Transaction::query()
            ->when($request->filled('search'), fn ($query, $search) => $query->where('description', 'like', "%{$search}%"))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->query('type')))
            ->latest('date')
            ->latest()
            ->get();

        // The original finance page had no summary strip at all. Adding one
        // here because a bare table makes income vs expense unreadable.
        $totalIncome = (float) $transactions->where('type', TransactionType::INCOME)->sum('amount');
        $totalExpense = (float) $transactions->where('type', TransactionType::EXPENSE)->sum('amount');

        return view('dashboard.finance.index', [
            'transactions' => $transactions,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(), $this->messages());

        Transaction::create($data);

        return back()->with('success', 'Transaksi berhasil dibuat');
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        // The original edit dialog called createTransaction, so saving an edit
        // silently produced a duplicate row. This updates in place.
        $data = $request->validate($this->rules(), $this->messages());

        $transaction->update($data);

        return back()->with('success', 'Transaksi berhasil diperbarui');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->delete();

        return back()->with('success', 'Transaksi berhasil dihapus');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(TransactionType::class)],
            'description' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'type.required' => 'Tipe wajib diisi',
            'description.required' => 'Deskripsi wajib diisi',
            'date.required' => 'Tanggal wajib diisi',
            'amount.required' => 'Total wajib diisi',
            'amount.numeric' => 'Total harus berupa angka',
            'amount.min' => 'Total minimal 1',
        ];
    }
}
