@props(['portfolio'])

<x-ui.card class="h-full transition-transform hover:scale-[1.02]">
    <a href="{{ route('portfolio.show', $portfolio->id) }}" class="flex h-full flex-col">
        <div class="aspect-video w-full overflow-hidden rounded-t-lg bg-muted">
            @if (filled($portfolio->thumbnail))
                <img
                    src="{{ $portfolio->thumbnail }}"
                    alt="{{ $portfolio->name }}"
                    loading="lazy"
                    class="h-full w-full object-cover"
                />
            @else
                <div class="flex h-full w-full items-center justify-center">
                    <x-lucide name="image" class="text-muted-foreground/50 size-10" />
                </div>
            @endif
        </div>

        <div class="flex flex-1 flex-col gap-3 p-6">
            <div class="border-b pb-2 font-semibold">
                <div class="truncate">{{ $portfolio->name }}</div>
            </div>

            <div class="flex items-center gap-1">
                <x-lucide name="clock" class="size-4" />
                <span class="text-sm">{{ \App\Support\Format::relativeTime($portfolio->updated_at, '') }}</span>
            </div>

            <div class="space-y-1">
                <div class="text-muted-foreground uppercase">Kategori</div>
                <div class="w-fit rounded-lg border bg-cyan-200 px-2 py-1 text-sm text-cyan-800">
                    {{ $portfolio->category }}
                </div>
            </div>

            <div class="text-muted-foreground line-clamp-2 text-sm">
                {{ $portfolio->description }}
            </div>

            <div class="mt-auto space-y-1">
                <div class="text-muted-foreground uppercase">Tech Stacks</div>
                <div class="flex flex-wrap gap-1">
                    @forelse (array_slice($portfolio->tech_stacks ?? [], 0, 3) as $tech)
                        <div class="w-fit rounded-lg border bg-slate-200 px-2 py-1 text-sm text-slate-800">{{ $tech }}</div>
                    @empty
                        <div class="text-muted-foreground text-sm">Tidak ada tech stack</div>
                    @endforelse

                    @if (count($portfolio->tech_stacks ?? []) > 3)
                        <div class="w-fit px-2 py-1 text-sm text-slate-800">+{{ count($portfolio->tech_stacks) - 3 }}</div>
                    @endif
                </div>
            </div>
        </div>
    </a>
</x-ui.card>
