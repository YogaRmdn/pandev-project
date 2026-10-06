@extends('layouts.dashboard')

@section('content')
    <div class="space-y-4">
        <x-dashboard.navbar drawer-id="dashboard-drawer" />
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-dashboard.stat-card label="Total Proyek Diunggah" :value="number_format($totalPortfolios)" icon="folder" />
            <x-dashboard.stat-card label="Total Pendapatan" :value="\App\Support\Format::idr($totalIncome)" icon="trending-up" />
            <x-dashboard.stat-card label="Total Pengeluaran" :value="\App\Support\Format::idr($totalExpense)" icon="trending-down" />
        </div>
    </div>
@endsection
