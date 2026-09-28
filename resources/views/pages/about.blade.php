@extends('layouts.main')

@section('title', 'Tentang | PanDev')

@section('description', 'Kenali lebih dekat PanDev, software house di Indonesia yang fokus pada solusi teknologi.')

@section('content')
    <main class="flex w-full flex-1 flex-col pt-16">
        <div class="flex flex-col items-center justify-center gap-8 sm:flex-row">
            <img
                src="{{ asset('assets/common/logo.png') }}"
                width="300"
                height="300"
                alt="PanDev Logo"
            />
            <div class="max-w-xl">
                <h1 class="font-heading text-primary text-3xl font-bold tracking-tight uppercase sm:text-3xl">
                    Taking your ideas to next level
                </h1>
                <p class="mt-4 text-balance">
                    PanDev adalah digital agency di Indonesia yang menawarkan solusi
                    aplikasi untuk bisnis dan individu. Kami membantu mewujudkan ide
                    menjadi produk digital yang bermanfaat dan berkelanjutan. Serta
                    beragam produk digital lainnya untuk memenuhi kebutuhan Anda
                </p>
            </div>
        </div>

        <section class="mx-auto mt-16 max-w-4xl py-16">
            <div class="mb-10 text-center">
                <h2 class="text-primary text-3xl font-bold uppercase sm:text-4xl">Meet Our Team</h2>
                <p class="text-muted-foreground mt-2">Tim profesional yang berpengalaman di bidangnya</p>
            </div>

            <div class="grid grid-cols-2 gap-x-4 gap-y-8 px-4 sm:grid-cols-3 md:grid-cols-4">
                @foreach ($team as $member)
                    <div class="flex flex-col items-center text-center">
                        <div class="mb-3 w-28 overflow-hidden rounded-lg border border-border bg-muted sm:w-32">
                            <img
                                src="{{ asset($member['image']) }}"
                                alt="{{ $member['name'] }}"
                                loading="lazy"
                                class="aspect-[2/3] h-auto w-full object-cover"
                            />
                        </div>
                        <h3 class="font-semibold">{{ $member['name'] }}</h3>
                        <p class="text-muted-foreground text-sm">{{ $member['role'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        @include('pages.partials.cta')
    </main>
@endsection
