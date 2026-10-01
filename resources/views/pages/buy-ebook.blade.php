@extends('layouts.main')

@section('title', 'Buy eBook | PanDev')

@section('description', 'Practical eBooks written by the PanDev team — Laravel, frontend engineering, and cyber security, written for developers who ship to production.')

@section('content')
    <main class="mx-auto flex w-full max-w-6xl flex-1 flex-col px-4 py-16 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <span class="bg-primary/10 text-primary inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold">
                <x-lucide name="book-open" class="size-4" /> eBooks
            </span>

            <h1 class="font-heading mt-5 text-3xl font-bold tracking-tight sm:text-4xl">
                Buy eBook
            </h1>

            <p class="text-muted-foreground mt-4 text-base sm:text-lg">
                Every title below comes from work we do for clients: the same
                architecture, checklists, and lessons we apply in production,
                written down so you can reuse them.
            </p>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($ebooks as $ebook)
                {{-- Sampul ebook. Berkunya dicek dulu supaya title yang
                     artwork-nya belum ada di public/assets/ebooks tetap jatuh
                     ke badge ikon (bukan jadi gambar rusak). --}}
                @php
                    $cover = $ebook['cover'] ?? null;
                    $hasCover = filled($cover) && is_file(public_path(ltrim($cover, '/')));
                @endphp

                <x-ui.card class="h-full overflow-hidden px-0 py-0 transition-all duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-md">
                    {{-- Pembungkus tunggal: `x-ui.card` membawa `flex flex-col
                         gap-6`, dan utility gap-6 menang atas gap-0 di CSS.
                         Dengan hanya satu anak, gap itu tidak berlaku sama
                         sekali; padding dititipkan ke px/py karena px-*/py-*
                         memang menang atas p-6 (lihat components/ui/card). --}}
                    <div class="flex flex-1 flex-col">
                        @if ($hasCover)
                            <img
                                src="{{ asset($cover) }}"
                                width="900"
                                height="1200"
                                alt="Sampul {{ $ebook['title'] }}"
                                loading="lazy"
                                class="aspect-[3/4] w-full border-b object-cover"
                            />
                        @else
                            <span class="bg-primary/10 text-primary inline-flex aspect-[3/4] w-full items-center justify-center border-b">
                                <x-lucide :name="$ebook['icon']" class="size-10" />
                            </span>
                        @endif

                        <div class="flex flex-1 flex-col gap-6 p-6">
                            <x-ui.card-header>
                                <x-ui.card-title class="font-heading text-lg font-bold tracking-tight">
                                    {{ $ebook['title'] }}
                                </x-ui.card-title>
                                <x-ui.card-description>{{ $ebook['tagline'] }}</x-ui.card-description>
                            </x-ui.card-header>

                            <x-ui.card-content class="flex flex-1 flex-col">
                                <p class="text-sm leading-relaxed text-pretty">{{ $ebook['description'] }}</p>

                                <ul class="mt-4 space-y-2 text-sm">
                                    @foreach ($ebook['topics'] as $topic)
                                        <li class="text-muted-foreground flex items-start gap-2">
                                            <x-lucide name="check" class="text-primary mt-0.5 size-4 shrink-0" />
                                            {{ $topic }}
                                        </li>
                                    @endforeach
                                </ul>

                                {{-- Footer: wrapper luarnya yang melar (flex-1) dan mengunci isi
                                 ke dasar card; yang diberi border-T adalah div
                                 DALAM yang menempel pada isinya. Kalau
                                 border-nya ada di wrapper yang melar, garisnya
                                 ikut terdorong ke atas mengikuti tinggi topik
                                 tiap judul, sehingga tidak rata antar card.
                                 Tinggi isi footer sendiri konstan (harga
                                 leading-tight + format nowrap + tombol w-full),
                                 jadi posisinya selalu sama. --}}
                            <div class="flex flex-1 flex-col justify-end">
                                <div class="flex flex-col gap-4 border-t pt-5">
                                    <div class="flex flex-col gap-1.5">
                                        <div class="font-heading text-xl leading-tight font-bold tabular-nums">{{ \App\Support\Format::idr($ebook['price']) }}</div>
                                        <div class="text-muted-foreground flex items-center gap-1.5 text-xs">
                                            <x-lucide name="file-text" class="size-3.5 shrink-0" />
                                            <span class="truncate">{{ $ebook['format'] }} · Lifetime updates</span>
                                        </div>
                                    </div>

                                    <a
                                        href="mailto:info@pandev.dev?subject={{ rawurlencode('Order: '.$ebook['title']) }}"
                                        class="btn-pill w-full shrink-0"
                                    >Order now</a>
                                </div>
                            </div>
                            </x-ui.card-content>
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        <div class="bg-muted mt-12 rounded-2xl border p-6 sm:p-8">
            <h2 class="font-heading text-lg font-bold tracking-tight">What you get with every eBook</h2>

            <div class="mt-6 grid gap-6 sm:grid-cols-3">
                @foreach ([
                    ['icon' => 'file-text', 'label' => 'Instant delivery', 'description' => 'Sent to your inbox right after the order is confirmed.'],
                    ['icon' => 'refresh-cw', 'label' => 'Lifetime updates', 'description' => 'Every future edition of the title is included, at no extra cost.'],
                    ['icon' => 'messages-square', 'label' => 'Author support', 'description' => 'Questions about a chapter? Ask the team that wrote the book.'],
                ] as $perk)
                    <div class="flex items-start gap-3">
                        <span class="bg-background text-primary inline-flex size-10 shrink-0 items-center justify-center rounded-lg border">
                            <x-lucide :name="$perk['icon']" class="size-4" />
                        </span>
                        <div>
                            <div class="font-heading text-sm font-bold">{{ $perk['label'] }}</div>
                            <p class="text-muted-foreground mt-1.5 text-sm leading-relaxed text-pretty">
                                {{ $perk['description'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
@endsection
