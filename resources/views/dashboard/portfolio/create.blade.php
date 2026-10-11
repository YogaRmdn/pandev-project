@extends('layouts.dashboard')

@section('page-title', 'Buat Portfolio')
@section('page-description', 'Isi form dibawah untuk menambahkan portfolio')

@section('content')
    <div class="space-y-5">
        <div class="rounded-2xl border border-base-300/70 bg-base-100 p-5 shadow-sm md:p-6">
            <x-dashboard.portfolio-form :action="route('dashboard.portfolio.store')" method="POST" />
        </div>
    </div>
@endsection