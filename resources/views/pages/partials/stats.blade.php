@php
    use App\Support\SiteContent;
    $stats = SiteContent::stats();
@endphp

<section id="stats-section" class="bg-secondary/50 w-full border-y border-border py-14 md:py-20" x-data="{ visible: false }" x-intersect="visible = true">
    <div class="mx-auto w-full max-w-6xl px-4">
        <div class="grid grid-cols-1 gap-px sm:grid-cols-2 lg:grid-cols-4" x-show="visible" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="translate-y-5 opacity-0" x-transition:enter-end="translate-y-0 opacity-100">
            @foreach ($stats as $index => $stat)
                <div
                    class="bg-background flex flex-col items-center gap-2 p-8 text-center"
                    x-show="visible"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="translate-y-8 opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    x-transition:enter-delay="{{ $index * 100 }}ms"
                >
                    <div class="text-primary text-4xl font-bold tracking-tight md:text-5xl">
                        {{ $stat['value'] }}<span class="text-primary">{{ $stat['suffix'] }}</span>
                    </div>
                    <div class="font-semibold">{{ $stat['label'] }}</div>
                    <p class="text-muted-foreground text-sm text-balance">{{ $stat['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>