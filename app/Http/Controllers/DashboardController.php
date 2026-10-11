<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Portfolio;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // The original summed both branches into totalIncome and never touched
        // totalExpense, so the expense card always read Rp 0. Split properly.
        $totalPortfolios = Portfolio::query()->count();
        $totalIncome = Transaction::income()->sum('amount');
        $totalExpense = Transaction::expense()->sum('amount');

        $recentPortfolios = Portfolio::ownedBy($user)
            ->latest('updated_at')
            ->take(4)
            ->get();

        $recentTransactions = Transaction::query()
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $invoices = Invoice::query()->with('invoiceItems')->get();

        return view('dashboard.index', [
            'totalPortfolios' => $totalPortfolios,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'myPortfolioCount' => Portfolio::ownedBy($user)->count(),
            'recentPortfolios' => $recentPortfolios,
            'recentTransactions' => $recentTransactions,
            'pendingInvoices' => $invoices
                ->whereIn('status', [InvoiceStatus::UNPAID, InvoiceStatus::PARTIALLY_PAID])
                ->count(),
            'outstandingInvoiceTotal' => $invoices
                ->reject(fn (Invoice $invoice) => $invoice->status === InvoiceStatus::PAID)
                ->sum(fn (Invoice $invoice) => $invoice->total * (1 - $invoice->status->paidRatio())),
        ]);
    }
}
