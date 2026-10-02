@props(['slides' => [], 'interval' => 3800])

<div
    x-data="coverflow({{ count($slides) }}, { interval: {{ (int) $interval }} })"
    x-on:keydown.left.prevent="nudge(-1)"
    x-on:keydown.right.prevent="nudge(1)"
    x-on:mouseenter="hovering = true; sync()"
    x-on:mouseleave="hovering = false; sync()"
    x-on:focusin="focused = true; sync()"
    x-on:focusout="focused = false; sync()"
    role="region"
    aria-roledescription="carousel"
    aria-label="Cover carousel"
    class="w-full"
    style="--cf-card: clamp(124px, 22vw, 260px);"
>
    <div
        tabindex="0"
        class="motion-safe:animate-coverflow-drift cursor-grab overflow-hidden py-10 outline-none ring-ring focus-visible:ring-2 active:cursor-grabbing"
        style="perspective: calc(var(--cf-card) * 3); touch-action: pan-y;"
    >
        <div
            class="relative h-[var(--cf-card)] select-none"
            style="transform-style: preserve-3d;"
            x-on:dragstart.prevent
        >
            @foreach ($slides as $index => $slide)
                <div
                    data-cf-card
                    role="group"
                    aria-roledescription="slide"
                    aria-label="{{ $index + 1 }} of {{ count($slides) }}"
                    class="absolute top-0 left-1/0 aspect-square w-[var(--cf-card)] overflow-hidden rounded-2xl bg-base-200 shadow-xl"
                    style="left: 50%; transition: transform 420ms cubic-bezier(0.22, 1, 0.36, 1), opacity 420ms ease-out;"
                    x-bind:style="styleFor({{ $index }})"
                >
                    <img
                        src="{{ $slide['src'] }}"
                        alt="{{ $slide['alt'] }}"
                        draggable="false"
                        class="h-full w-full select-none object-cover"
                    />
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6 flex flex-wrap items-center justify-center gap-x-4 gap-y-3">
        <div class="flex items-center gap-2">
            @foreach ($slides as $index => $slide)
                <button
                    type="button"
                    aria-label="Go to slide {{ $index + 1 }}"
                    x-on:click="goTo({{ $index }})"
                    x-bind:aria-current="index === {{ $index }}"
                    class="size-2 rounded-full bg-foreground transition-opacity"
                    x-bind:class="index === {{ $index }} ? 'opacity-100' : 'opacity-30'"
                ></button>
            @endforeach
        </div>

        {{-- Auto-advance harus bisa dihentikan pembaca (WCAG: konten yang
             berubah sendiri). --}}
        <button
            type="button"
            class="text-base-content/60 hover:text-base-content inline-flex size-8 items-center justify-center rounded-full border transition-colors"
            x-on:click="toggle()"
            x-bind:aria-label="paused ? 'Play carousel' : 'Pause carousel'"
            x-bind:aria-pressed="paused ? 'true' : 'false'"
        >
            <span x-show="!paused">
                <x-lucide name="pause" class="size-4" />
            </span>
            <span x-show="paused" x-cloak>
                <x-lucide name="play" class="size-4" />
            </span>
        </button>
    </div>
</div>
