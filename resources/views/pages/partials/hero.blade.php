@php
    use App\Support\SiteContent;
    $rows = SiteContent::marqueeRows();
@endphp

<section class="mt-16 text-center" x-data="{ visible: false }" x-intersect="visible = true">
    <div
        x-show="visible"
        x-transition:enter="transition ease-out duration-700"
        x-transition:enter-start="translate-y-5 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
    >
        <h1 class="text-primary text-2xl font-bold md:text-4xl lg:text-6xl">PANDEV</h1>
        <div class="mt-1 text-lg md:text-2xl lg:text-4xl">Unlock What's Possible</div>
        <div class="mt-4 text-base italic tracking-wider md:text-xl lg:text-2xl">
            Digital solutions, where ideas becomes reality
        </div>
    </div>

    <div class="relative mt-4">
        <div class="relative z-10">
            <img
                src="{{ asset('assets/common/hero-image.svg') }}"
                width="800"
                height="600"
                alt="Hero Image"
                class="mx-auto h-auto w-auto"
            />

            <div class="hidden md:block">
                <div class="absolute top-40 right-0 left-0 -z-10 flex flex-col gap-8">
                    @foreach ($rows as $row)
                        <div class="flex w-full items-center justify-center overflow-hidden">
                            <div class="flex shrink-0 animate-marquee items-center">
                                @for ($i = 0; $i < 2; $i++)
                                    @foreach ($row as $item)
                                        <span class="mx-8 shrink-0 text-3xl font-bold tracking-tight uppercase sm:text-4xl lg:text-5xl {{ $item['accent'] ? 'text-primary' : '' }}">{{ $item['text'] }}</span>
                                    @endforeach
                                @endfor
                            </div>
                            <div class="flex shrink-0 animate-marquee items-center" aria-hidden="true">
                                @for ($i = 0; $i < 2; $i++)
                                    @foreach ($row as $item)
                                        <span class="mx-8 shrink-0 text-3xl font-bold tracking-tight uppercase sm:text-4xl lg:text-5xl {{ $item['accent'] ? 'text-primary' : '' }}">{{ $item['text'] }}</span>
                                    @endforeach
                                @endfor
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
