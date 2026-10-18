@extends('layouts.main')

@section('title', '404 | Page Not Found - PanDev')
@section('description', 'The page you are looking for could not be found. Explore PanDev services, portfolio, or get in touch with our team.')
@section('robots', 'noindex, nofollow')

@section('content')
    {{-- Halaman error sengaja TIDAK bergantung pada Alpine (tidak ada
         x-show/x-cloak) supaya konten tetap tampil walau JS gagal dimuat,
         misalnya saat error berasal dari masalah aset. --}}
    <section class="relative flex min-h-[calc(100vh-8rem)] items-center overflow-hidden px-4 py-24">
        <div class="mx-auto w-full max-w-3xl text-center">
            {{-- Angka 404 besar, selaras dengan heading hero. --}}
            <h1
                class="text-gradient text-7xl font-bold leading-none tracking-tight select-none sm:text-8xl lg:text-9xl"
                aria-hidden="true"
            >
                404
            </h1>

            <span class="text-primary bg-primary/10 mt-6 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold tracking-widest uppercase">
                <x-lucide name="compass" class="size-4" />
                Page Not Found
            </span>

            <h2 class="mt-6 text-2xl font-bold md:text-4xl">
                Oops — this page went off-script.
            </h2>

            <p class="text-base-content/60 mx-auto mt-4 max-w-xl text-balance text-base md:text-lg">
                The link may be outdated, or the page has moved. Don't worry — let's get you
                back on track.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg h-12 px-8">
                    <x-lucide name="house" />
                    Back to Home
                </a>
                <a href="{{ route('portfolio') }}" class="btn btn-outline btn-lg h-12 px-8">
                    <x-lucide name="folder-open" />
                    View Our Work
                </a>
            </div>

            {{-- Link cepat ke halaman utama. --}}
            <div class="text-base-content/60 mt-10 border-t border-base-300 pt-6 text-sm">
                <span class="me-2 font-semibold tracking-wider uppercase">Or jump to</span>
                <span class="inline-flex flex-wrap items-center justify-center gap-x-6 gap-y-2">
                    <a href="{{ route('services') }}" class="text-base-content hover:text-primary inline-flex items-center gap-1.5 font-medium transition-colors">
                        <x-lucide name="layers" class="text-primary size-4" /> Services
                    </a>
                    <a href="{{ route('about') }}" class="text-base-content hover:text-primary inline-flex items-center gap-1.5 font-medium transition-colors">
                        <x-lucide name="users" class="text-primary size-4" /> About
                    </a>
                    <a href="{{ route('buy-ebook') }}" class="text-base-content hover:text-primary inline-flex items-center gap-1.5 font-medium transition-colors">
                        <x-lucide name="book-open" class="text-primary size-4" /> E-Book
                    </a>
                    <a href="{{ route('contact') }}" class="text-base-content hover:text-primary inline-flex items-center gap-1.5 font-medium transition-colors">
                        <x-lucide name="mail" class="text-primary size-4" /> Contact
                    </a>
                </span>
            </div>
        </div>
    </section>
@endsection
