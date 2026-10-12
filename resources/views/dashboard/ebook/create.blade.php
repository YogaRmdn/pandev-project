@extends('layouts.dashboard')

@section('page-title', 'Tambah Ebook')
@section('page-description', 'Isi form di bawah untuk menambahkan ebook')

@section('content')
    <div class="space-y-5">
        <div class="rounded-2xl border border-base-300/70 bg-base-100 p-5 shadow-sm md:p-6">
            <x-dashboard.ebook-form :action="route('dashboard.ebook.store')" method="POST" />
        </div>
    </div>
@endsection
