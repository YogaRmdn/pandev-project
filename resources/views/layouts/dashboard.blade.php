<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard | '.config('app.name'))</title>

    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" href="{{ asset('assets/common/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&family=Montserrat:wght@100..900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body x-data class="flex min-h-full flex-col bg-base-100 font-sans text-base-content">
    <x-dashboard.sidebar />

    <div
        class="flex min-h-full flex-1 flex-col transition-[padding] duration-200 ease-in-out"
        x-bind:class="$store.sidebar.collapsed ? 'md:pl-12' : 'md:pl-64'"
    >
        <div class="flex-1 px-4 py-4 md:px-6">
            @yield('content')
        </div>
    </div>

    @if (session('status') || session('success'))
        <div
            class="toast toast-end z-[100]"
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 4000)"
            x-transition
            role="status"
        >
            <div class="alert alert-success">
                <span>{{ session('status') ?? session('success') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div
            class="toast toast-start z-[100]"
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            x-transition
            role="alert"
        >
            <div class="alert alert-error">
                <span class="font-medium">Terjadi kesalahan</span>
                <ul class="mt-1 list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @stack('scripts')
</body>
</html>
