@extends('layouts.dashboard')

@section('page-title', 'Edit Ebook')
@section('page-description', 'Perbarui data ebook "'.$ebook->title.'"')

@section('content')
    <div class="space-y-5">
        <div class="rounded-2xl border border-base-300/70 bg-base-100 p-5 shadow-sm md:p-6">
            <x-dashboard.ebook-form :ebook="$ebook" :action="route('dashboard.ebook.update', $ebook->id)" method="PUT" />
        </div>
    </div>
@endsection
