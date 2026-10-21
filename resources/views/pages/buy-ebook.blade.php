@extends('layouts.main')

@section('title', 'Buy eBook | PanDev')

@section('description', 'Practical eBooks written by the PanDev team — Laravel, frontend engineering, and cyber security, written for developers who ship to production.')

@section('keywords', 'ebook pemrograman, buku laravel, ebook web development, ebook cyber security, buku pemrograman indonesia, tutorial praktis developer, belajar laravel, belajar keamanan sibel, ebook praktis programmer')

@push('json-ld')
    <x-json-ld :data="\App\Support\Seo::graph([
        \App\Support\Seo::breadcrumbs([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Buy eBook', 'url' => route('buy-ebook')],
        ]),
        [
            '@type' => 'ItemList',
            'name' => 'PanDev eBooks',
            'itemListElement' => $ebooks->values()->map(fn ($ebook, $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => array_filter([
                    '@type' => 'Book',
                    'name' => $ebook->title,
                    'description' => $ebook->description,
                    'url' => $ebook->url,
                    'image' => filled($ebook->cover) ? \App\Support\Seo::image($ebook->cover) : null,
                    'author' => ['@type' => 'Organization', 'name' => config('seo.organization.name')],
                ]),
            ])->all(),
        ],
    ])" />
@endpush

@section('content')
    <main class="mx-auto flex w-full max-w-6xl flex-1 flex-col px-4 py-16 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <span class="bg-primary/10 text-primary inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold">
                <x-lucide name="book-open" class="size-4" /> eBooks
            </span>

            <h1 class="text-gradient font-heading mt-5 text-3xl font-bold tracking-tight sm:text-4xl">
                Buy eBook
            </h1>

            <p class="text-base-content/60 mt-4 text-base sm:text-lg">
                Every title below comes from work we do for clients: the same
                architecture, checklists, and lessons we apply in production,
                written down so you can reuse them.
            </p>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($ebooks as $ebook)
                {{-- Cover full-bleed: padding dititipkan ke px/py (menang atas p-6)
                     dan kartu dibungkus satu anak flex supaya gap-6 base tidak
                     berlaku. Cover yang kosong jatuh ke badge ikon. --}}
                <div
                    class="card cursor-glow gap-6 border p-6 shadow-sm h-full overflow-hidden px-0 py-0 transition-all duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-md">
                    <div class="relative z-[1] flex flex-1 flex-col">
                        @if (filled($ebook->cover))
                            <img src="{{ $ebook->cover }}" width="900" height="1200"
                                alt="Sampul {{ $ebook->title }}" loading="lazy"
                                class="aspect-[3/4] w-full border-b object-cover" />
                        @else
                            <span
                                class="bg-primary/10 text-primary inline-flex aspect-[3/4] w-full items-center justify-center border-b">
                                <x-lucide name="book-open" class="size-10" />
                            </span>
                        @endif

                        <div class="flex flex-1 flex-col gap-4 p-6">
                            <div class="card-title font-heading text-lg leading-tight font-bold tracking-tight">
                                {{ $ebook->title }}
                            </div>

                            <p class="text-sm leading-relaxed text-pretty">{{ $ebook->description }}</p>

                            <div class="flex flex-1 flex-col justify-end">
                                <div class="border-t pt-5">
                                    <a href="{{ $ebook->url }}" target="_blank" rel="noopener noreferrer"
                                        class="btn-pill w-full shrink-0">
                                        Beli Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="flex h-72 flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-base-300 bg-base-100 px-6 text-center sm:col-span-2 lg:col-span-3">
                    <span class="grid size-14 place-items-center rounded-full bg-base-200 text-base-content/40">
                        <x-lucide name="book-open" class="size-7" />
                    </span>
                    <p class="text-sm font-semibold text-base-content/70">
                        Belum ada ebook yang tersedia saat ini.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="bg-base-200 mt-12 rounded-2xl border p-6 sm:p-8">
            <h2 class="font-heading text-lg font-bold tracking-tight">What you get with every eBook</h2>

            <div class="mt-6 grid gap-6 sm:grid-cols-3">
                @foreach ([
                    ['icon' => 'file-text', 'label' => 'Instant delivery', 'description' => 'Sent to your inbox right after the order is confirmed.'],
                    ['icon' => 'refresh-cw', 'label' => 'Lifetime updates', 'description' => 'Every future edition of the title is included, at no extra cost.'],
                    ['icon' => 'messages-square', 'label' => 'Author support', 'description' => 'Questions about a chapter? Ask the team that wrote the book.'],
                ] as $perk)
                    <div class="flex items-start gap-3">
                        <span class="bg-base-100 text-primary inline-flex size-10 shrink-0 items-center justify-center rounded-lg border">
                            <x-lucide :name="$perk['icon']" class="size-4" />
                        </span>
                        <div>
                            <div class="font-heading text-sm font-bold">{{ $perk['label'] }}</div>
                            <p class="text-base-content/60 mt-1.5 text-sm leading-relaxed text-pretty">
                                {{ $perk['description'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
@endsection
