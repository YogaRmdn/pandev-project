@extends('layouts.dashboard')

@section('page-title', 'Edit Portfolio')
@section('page-description', 'Update data portfolio "'.$portfolio->name.'"')

@section('content')
    <div class="space-y-5">
        <div class="rounded-2xl border border-base-300/70 bg-base-100 p-5 shadow-sm md:p-6">
            <x-dashboard.portfolio-form
                :portfolio="$portfolio"
                :action="route('dashboard.portfolio.update', $portfolio->id)"
                method="PUT"
            />
        </div>
    </div>
@endsection