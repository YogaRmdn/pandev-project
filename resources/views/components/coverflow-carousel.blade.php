@props(['slides' => []])

<div
    x-data="coverflow({{ count($slides) }})"
    x-on:keydown.left.prevent="nudge(-1)"
    x-on:keydown.right.prevent="nudge(1)"
    role="region"
    aria-roledescription="carousel"
    aria-label="Cover carousel"
    class="w-full"
    style="--cf-card: clamp(148px, 22vw, 260px);"
>
    <div
        tabindex="0"
        class="cursor-grab overflow-hidden py-10 outline-none ring-ring focus-visible:ring-2 active:cursor-grabbing"
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
                    class="absolute top-0 left-1/0 aspect-square w-[var(--cf-card)] overflow-hidden rounded-2xl bg-muted shadow-xl"
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

    <div class="mt-6 flex items-center justify-center gap-2">
        @foreach ($slides as $index => $slide)
            <button
                type="button"
                aria-label="Ke slide {{ $index + 1 }}"
                x-on:click="goTo({{ $index }})"
                x-bind:aria-current="index === {{ $index }}"
                class="bg-foreground size-2 rounded-full transition-opacity"
                x-bind:class="index === {{ $index }} ? 'opacity-100' : 'opacity-30'"
            ></button>
        @endforeach
    </div>
</div>
