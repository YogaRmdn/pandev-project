@extends('layouts.main')

@section('title', 'PanDev | Software Development Agency in Indonesia')

@section('description', 'PanDev is a professional software development agency in Indonesia. We design, build, and scale web, mobile, desktop, IoT, and data products for startups and growing businesses.')

@push('json-ld')
    <x-json-ld :data="\App\Support\Seo::graph([
        \App\Support\Seo::breadcrumbs([
            ['name' => 'Home', 'url' => route('home')],
        ]),
        [
            '@type' => 'WebPage',
            'name' => 'PanDev | Software Development Agency in Indonesia',
            'url' => route('home'),
            'about' => ['@id' => url('/').'#organization'],
        ],
    ])" />
@endpush

@section('content')
    @include('pages.partials.hero')
    @include('pages.partials.stats')
    @include('pages.partials.services')
    @include('pages.partials.tech-stacks')
    @include('pages.partials.process')
    @include('pages.partials.cta')
@endsection
