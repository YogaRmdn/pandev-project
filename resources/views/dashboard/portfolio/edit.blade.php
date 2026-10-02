@extends('layouts.dashboard')

@section('content')
    <div class="space-y-4">
        <x-dashboard.header
            title="Edit Portfolio"
            :description="'Update data portfolio &quot;'.$portfolio->name.'&quot;'"
        />

        <div class="card gap-6 border p-6 shadow-sm mt-4">
            <div class="">
                <x-dashboard.portfolio-form
                    :portfolio="$portfolio"
                    :action="route('dashboard.portfolio.update', $portfolio->id)"
                    method="PUT"
                />
            </div>
        </div>
    </div>
@endsection
