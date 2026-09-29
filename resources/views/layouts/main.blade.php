@extends('layouts.app')

@section('body')
    <x-navbar />

    @yield('content')

    <x-footer />
@endsection
