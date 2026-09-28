<?php

namespace App\Http\Controllers;

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

        return view('dashboard.index', [
            'totalPortfolios' => $totalPortfolios,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'myPortfolioCount' => Portfolio::ownedBy($user)->count(),
        ]);
    }
}
