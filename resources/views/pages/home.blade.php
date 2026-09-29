@extends('layouts.main')

@section('title', 'PanDev | Software Development Agency in Indonesia')

@section('description', 'PanDev is a professional software development agency in Indonesia. We design, build, and scale web, mobile, desktop, IoT, and data products for startups and growing businesses.')

@section('content')
    @include('pages.partials.hero')
    @include('pages.partials.stats')
    @include('pages.partials.services')
    @include('pages.partials.tech-stacks')
    @include('pages.partials.process')
    @include('pages.partials.cta')
@endsection
