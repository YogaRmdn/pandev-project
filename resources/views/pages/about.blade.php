@extends('layouts.main')

@section('title', 'About Us | PanDev')

@section('description', 'Get to know PanDev, a software development agency in Indonesia focused on reliable technology solutions.')

@section('content')
    <main class="flex w-full flex-1 flex-col">
        <section class="mx-auto flex w-full max-w-6xl flex-col items-center gap-10 px-4 pt-16 pb-4 sm:flex-row sm:items-center sm:justify-between">
            <span class="bg-base-100 flex size-40 shrink-0 items-center justify-center overflow-hidden rounded-2xl border shadow-sm sm:size-44">
                <img
                    src="{{ asset('assets/common/logo.png') }}"
                    width="300"
                    height="300"
                    alt="PanDev Logo"
                    class="size-full object-contain p-6"
                />
            </span>

            <div class="max-w-2xl">
                <div class="text-primary text-sm font-semibold tracking-wider uppercase">About us</div>
                <h1 class="font-heading text-primary mt-2 text-3xl font-bold tracking-tight sm:text-4xl">
                    Taking your ideas to the next level
                </h1>
                <p class="mt-4 text-base text-pretty sm:text-lg">
                    PanDev is a software development agency in Indonesia offering application
                    solutions for businesses and individuals. We help you turn ideas into
                    useful, sustainable digital products — and a range of other digital
                    services to cover your needs.
                </p>
            </div>
        </section>

        <section class="mx-auto w-full max-w-6xl px-4 py-16">
            <div class="max-w-2xl">
                <h2 class="text-primary font-heading text-2xl font-bold tracking-tight sm:text-3xl">
                    Meet our team
                </h2>
                <p class="text-base-content/60 mt-3 text-pretty">
                    A team of professionals who are passionate about their craft.
                </p>
            </div>

            <div class="mt-10 grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($team as $member)
                    <div class="card gap-6 border p-6 shadow-sm h-full gap-0 transition-all duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-md">
                        <div class="flex h-full flex-col items-center gap-3 text-center">
                            <div class="bg-base-200 w-full overflow-hidden rounded-lg border">
                                <img
                                    src="{{ asset($member['image']) }}"
                                    alt="{{ $member['name'] }}"
                                    loading="lazy"
                                    class="aspect-[2/3] w-full object-cover"
                                />
                            </div>

                            <div>
                                <h3 class="font-heading text-sm font-bold">{{ $member['name'] }}</h3>
                                <p class="text-primary text-base-content/60 mt-0.5 text-xs font-medium tracking-wide">
                                    {{ $member['role'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        @include('pages.partials.cta')
    </main>
@endsection
