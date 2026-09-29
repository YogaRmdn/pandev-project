@php
    use App\Support\SiteContent;

    $positions = SiteContent::techStackPositions();
@endphp

<section
    id="tech-stacks-section"
    class="relative isolate flex min-h-[520px] w-full items-center justify-center overflow-hidden bg-cover bg-center bg-no-repeat py-14 md:min-h-[640px] md:py-20"
    style="background-image: url('{{ asset('assets/common/tech-stacks-background.jpg') }}')"
>
    <div class="orbit relative aspect-square w-[min(88vw,680px)]">
        <div class="absolute inset-[4%] rounded-full border border-border/40"></div>
        <div class="orbit-ring absolute inset-[14%] rounded-full border border-dashed border-border/25"></div>
        <div class="absolute inset-[20%] rounded-full bg-primary/10 blur-3xl"></div>

        <div class="orbit-spin absolute inset-0 z-10">
            @foreach ($positions as $position)
                <div
                    class="orbit-item absolute"
                    style="left: {{ $position['left'] }}; top: {{ $position['top'] }};"
                >
                    <div
                        class="orbit-icon flex items-center justify-center rounded-full bg-white shadow-[0_8px_30px_rgb(0,0,0,0.35)] ring-1 ring-white/60 transition-transform duration-300 hover:scale-110"
                    >
                        <img
                            src="{{ asset($position['icon']) }}"
                            alt="{{ $position['name'] }}"
                            width="36"
                            height="36"
                            loading="lazy"
                            class="h-[55%] w-[55%] object-contain"
                        />
                    </div>

                    <span
                        class="orbit-tip rounded-full bg-black/70 px-2.5 py-1 text-xs font-medium text-white backdrop-blur-sm"
                    >
                        {{ $position['name'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="pointer-events-none absolute inset-0 z-20 flex items-center justify-center px-6">
        <div class="max-w-[200px] text-center sm:max-w-sm md:max-w-md">
            <h2 class="text-primary text-2xl font-bold sm:text-4xl md:text-6xl">TECH STACKS</h2>
            <p class="mt-3 text-sm text-pretty sm:mt-4 sm:text-base md:text-lg">
                Every project is built on modern, battle-tested technologies
                that scale as you grow
            </p>
        </div>
    </div>
</section>
