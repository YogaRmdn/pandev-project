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
            <div class="text-primary text-sm uppercase tracking-wider sm:text-base">What we do</div>
            <div class="text-primary mb-1 text-2xl font-bold uppercase md:text-4xl">Technology solutions, built to perform</div>
            <div class="text-lg md:text-xl">From products and platforms to enterprise-grade infrastructure</div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach ($services as $index => $service)
                <div
                    x-show="visible"
                    x-transition:enter="transition ease-out duration-500 {{ ['delay-100', 'delay-200', 'delay-300', 'delay-400', 'delay-500'][$index] ?? '' }}"
                    x-transition:enter-start="translate-y-8 opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                >
                    <div class="card gap-6 border p-6 shadow-sm h-full transition-all duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-md">
                        <div class="flex h-full flex-col gap-4">
                            <span class="bg-primary text-primary-content flex size-12 shrink-0 items-center justify-center rounded-xl md:size-14">
                                <x-lucide :name="$service['icon']" class="size-6 md:size-7" />
                            </span>

                            <div class="space-y-2">
                                <h3 class="font-heading text-base font-bold tracking-tight sm:text-lg">
                                    {{ $service['label'] }}
                                </h3>
                                <p class="text-base-content/60 text-sm leading-relaxed text-pretty">
                                    {{ $service['description'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
