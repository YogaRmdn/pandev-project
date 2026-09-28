@extends('layouts.app')

@section('title', 'PanDev | Jasa IT Profesional')

@section('body')
    <x-navbar />

    @yield('content')

    <x-footer />
@endsection
