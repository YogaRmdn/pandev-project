@php
    use App\Support\SiteContent;

    $radius = 280.0;
    $iconSize = 72.0;
    $center = $radius + $iconSize / 2;
    $positions = SiteContent::techStackPositions($radius);
@endphp

<section
    id="tech-stacks-section"
    class="relative flex min-h-[600px] w-full items-center justify-center bg-cover bg-center bg-no-repeat py-24"
    style="background-image: url('{{ asset('assets/common/tech-stacks-background.jpg') }}')"
>
    <div class="relative flex items-center justify-center">
        <svg
            class="absolute hidden md:block"
            width="{{ $radius * 2 + $iconSize }}"
            height="{{ $radius * 2 + $iconSize }}"
            style="left: calc(50% - {{ $center }}px); top: calc(50% - {{ $center }}px);"
            aria-hidden="true"
        >
            @foreach ($positions as $position)
                <line
                    x1="{{ $center }}" y1="{{ $center }}"
                    x2="{{ $center + $position['x'] }}" y2="{{ $center + $position['y'] }}"
                    stroke="currentColor" stroke-width="1"
                    class="text-border opacity-50"
                />
            @endforeach

            @foreach ($positions as $index => $position)
                @php $next = $positions[($index + 1) % count($positions)]; @endphp
                <line
                    x1="{{ $center + $position['x'] }}" y1="{{ $center + $position['y'] }}"
                    x2="{{ $center + $next['x'] }}" y2="{{ $center + $next['y'] }}"
                    stroke="currentColor" stroke-width="1"
                    class="text-border opacity-50"
                />
            @endforeach
        </svg>

        @foreach ($positions as $position)
            <div
                class="absolute z-10 hidden md:flex"
                style="left: calc(50% + {{ $position['x'] }}px - {{ $iconSize / 2 }}px); top: calc(50% + {{ $position['y'] }}px - {{ $iconSize / 2 }}px);"
            >
                <div
                    class="flex items-center justify-center rounded-full bg-white shadow-[0_8px_30px_rgb(0,0,0,0.3)]"
                    style="width: {{ $iconSize }}px; height: {{ $iconSize }}px;"
                >
                    <img src="{{ asset($position['icon']) }}" alt="{{ $position['name'] }}" width="36" height="36" class="object-contain" />
                </div>
            </div>
        @endforeach

        <div class="relative z-20 px-4 text-center">
            <h2 class="text-primary text-4xl font-bold md:text-6xl">TECH STACKS</h2>
            <p class="mx-auto mt-4 w-64 text-base md:w-128 md:text-lg">
                Projek-proyek dibangun menggunakan stacks dan teknologi yang stable
                dan terbaru
            </p>
        </div>
    </div>
</section>
