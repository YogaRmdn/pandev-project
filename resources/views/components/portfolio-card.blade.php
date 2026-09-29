@props(['portfolio'])

@php
    $stacks = array_slice((array) ($portfolio->tech_stacks ?? []), 0, 3);
    $extraStacks = max(0, count((array) ($portfolio->tech_stacks ?? [])) - 3);
@endphp

{{-- p-0 di kartu + padding di konten: thumbnail jadi full-bleed tanpa garis
     card yang mengapit, dan tidak ada padding ganda. --}}
<x-ui.card class="h-full gap-0 overflow-hidden p-0 transition-shadow duration-300 hover:shadow-md">
    <a href="{{ route('portfolio.show', $portfolio->id) }}" class="group flex h-full flex-col">
        <div class="bg-muted aspect-video w-full overflow-hidden">
            @if (filled($portfolio->thumbnail))
                <img
                    src="{{ $portfolio->thumbnail }}"
                    alt="{{ $portfolio->name }}"
                    loading="lazy"
                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                />
            @else
                <div class="flex h-full w-full items-center justify-center">
                    <x-lucide name="image" class="text-muted-foreground/50 size-10" />
                </div>
            @endif
        </div>

        <div class="flex flex-1 flex-col gap-4 p-5">
            <div>
                <h3 class="font-heading truncate text-base font-bold tracking-tight" title="{{ $portfolio->name }}">
                    {{ $portfolio->name }}
                </h3>
                <div class="text-muted-foreground mt-1.5 flex items-center gap-1.5 text-xs">
                    <x-lucide name="clock" class="size-3.5 shrink-0" />
                    Updated {{ \App\Support\Format::date($portfolio->updated_at) }}
                </div>
            </div>

            @if (filled($portfolio->description))
                <p class="text-muted-foreground line-clamp-2 text-sm leading-relaxed text-pretty">
                    {{ $portfolio->description }}
                </p>
            @endif

            <div class="mt-auto flex flex-wrap items-center gap-1.5">
                @if (filled($portfolio->category))
                    <span class="inline-flex items-center gap-1 rounded-full bg-sky-500/15 px-2.5 py-0.5 text-xs font-medium text-sky-700">
                        <x-lucide name="tag" class="size-3" /> {{ $portfolio->category }}
                    </span>
                @endif

                @foreach ($stacks as $stack)
                    <span class="bg-muted text-muted-foreground rounded px-2 py-0.5 text-xs">{{ $stack }}</span>
                @endforeach

                @if ($extraStacks > 0)
                    <span class="bg-muted text-muted-foreground rounded px-2 py-0.5 text-xs">+{{ $extraStacks }}</span>
                @endif
            </div>
        </div>
    </a>
</x-ui.card>
