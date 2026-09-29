@php
    use App\Support\SiteContent;
    $rows = SiteContent::marqueeRows();
@endphp

<section class="mt-16 px-4 text-center" x-data="{ visible: false }" x-intersect="visible = true">
    <div
        x-show="visible"
        x-transition:enter="transition ease-out duration-700"
        x-transition:enter-start="translate-y-5 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
    >
        <span class="text-primary bg-primary/10 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold tracking-widest uppercase">
            <x-lucide name="sparkles" class="size-4" />
            Software Development Agency
        </span>

        <h1 class="text-primary mt-6 text-2xl font-bold md:text-4xl lg:text-6xl">PANDEV</h1>
        <div class="mt-1 text-lg font-medium md:text-2xl lg:text-4xl">From bold ideas to reliable software</div>
        <p class="text-muted-foreground mx-auto mt-4 max-w-2xl text-balance text-base md:text-xl">
            We design, build, and scale web, mobile, desktop, IoT, and data products
            that help startups and growing businesses move forward.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            <x-ui.button size="lg" href="{{ route('contact') }}" class="h-12 px-8">
                Start a Project
                <x-lucide name="arrow-right" />
            </x-ui.button>
            <x-ui.button size="lg" variant="outline" href="{{ route('portfolio') }}" class="h-12 px-8">
                View Our Work
            </x-ui.button>
        </div>

        <div class="text-muted-foreground mt-8 flex flex-wrap items-center justify-center gap-x-8 gap-y-2 text-sm">
            <span class="inline-flex items-center gap-2">
                <x-lucide name="badge-check" class="text-primary size-4" /> 25+ projects shipped
            </span>
            <span class="inline-flex items-center gap-2">
                <x-lucide name="badge-check" class="text-primary size-4" /> End-to-end delivery
            </span>
            <span class="inline-flex items-center gap-2">
                <x-lucide name="badge-check" class="text-primary size-4" /> Ongoing support
            </span>
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
