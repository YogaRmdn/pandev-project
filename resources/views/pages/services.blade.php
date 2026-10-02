@extends('layouts.main')

@section('title', 'Services | PanDev')

@section('description', 'Explore PanDev services for businesses and individuals — from web applications and mobile apps to IoT projects and data analytics.')

@section('content')
    <main class="mx-auto flex w-full max-w-6xl flex-1 flex-col px-4 py-16 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <div class="text-primary text-sm font-semibold tracking-wider uppercase">What we do</div>
            <h1 class="font-heading mt-2 text-3xl font-bold tracking-tight sm:text-4xl">Services</h1>
            <p class="text-base-content/60 mt-4 text-base text-pretty sm:text-lg">
                PanDev provides application development and technology services that help
                businesses and individuals turn ideas into working products.
            </p>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($services as $service)
                <div class="card gap-6 border p-6 shadow-sm h-full gap-0 transition-all duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-md">
                    <div class="flex h-full flex-col gap-4">
                        <span class="bg-primary text-primary-content inline-flex size-11 shrink-0 items-center justify-center rounded-xl">
                            <x-lucide :name="$service['icon']" class="size-5" />
                        </span>

                        <div class="space-y-2">
                            <h2 class="font-heading text-base font-bold tracking-tight">
                                {{ $service['title'] }}
                            </h2>
                            <p class="text-base-content/60 text-sm leading-relaxed text-pretty">
                                {{ $service['description'] }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @include('pages.partials.cta')
    </main>
@endsection
