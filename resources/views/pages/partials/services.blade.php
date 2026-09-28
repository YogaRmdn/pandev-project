@php
    use App\Support\SiteContent;
    $services = SiteContent::services();
@endphp

<section id="services-section" class="w-full space-y-6 p-4 md:p-8" x-data="{ visible: false }" x-intersect="visible = true">
    <div class="mx-auto w-full max-w-6xl space-y-6">
        <div
            x-show="visible"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="translate-y-5 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
        >
            <div class="text-primary text-sm uppercase tracking-wider sm:text-base">our services</div>
            <div class="text-primary mb-1 text-2xl font-bold uppercase md:text-4xl">Satu tempat beragam solusi</div>
            <div class="text-lg md:text-xl">Dari aplikasi hingga kebutuhan lainnya</div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach ($services as $index => $service)
                <div
                    x-show="visible"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="translate-y-8 opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    x-transition:enter-delay="{{ $index * 100 }}ms"
                >
                    <x-ui.card class="h-full p-4">
                        <x-ui.card-content>
                            <div class="text-primary mb-4 flex size-12 items-center justify-center rounded-full bg-primary text-white md:size-18">
                                <x-lucide :name="$service['icon']" class="size-7 md:size-10" />
                            </div>
                            <div class="text-base font-bold sm:text-lg">{{ $service['label'] }}</div>
                            <div>{{ $service['description'] }}</div>
                        </x-ui.card-content>
                    </x-ui.card>
                </div>
            @endforeach
        </div>
    </div>
</section>
