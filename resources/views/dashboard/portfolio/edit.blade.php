@extends('layouts.dashboard')

@section('content')
    <div class="space-y-4">
        <x-dashboard.header
            title="Edit Portfolio"
            :description="'Update data portfolio &quot;'.$portfolio->name.'&quot;'"
        />

        <x-ui.card class="mt-4">
            <x-ui.card-content>
                <x-dashboard.portfolio-form
                    :portfolio="$portfolio"
                    :action="route('dashboard.portfolio.update', $portfolio->id)"
                    method="PUT"
                />
            </x-ui.card-content>
        </x-ui.card>
    </div>
@endsection
