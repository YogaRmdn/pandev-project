@props(['portfolio'])

@php
    $isPublished = $portfolio->status === \App\Enums\PortfolioStatus::PUBLISHED;
    $stacks = array_slice((array) $portfolio->tech_stacks, 0, 2);
    $extraStacks = max(0, count((array) $portfolio->tech_stacks) - 2);
    $confirmName = 'portfolio-delete-'.$portfolio->id;
@endphp

<x-ui.card class="h-full gap-0 overflow-hidden p-0 transition-shadow duration-300 hover:shadow-md">
    {{-- Thumbnail full-bleed: kartu punya padding, jadi gambar ditarik ke tepi
         dengan margin negatif agar tidak ada garis card yang mengapit. --}}
    <x-ui.card-header class="gap-0">
        @if (filled($portfolio->thumbnail))
            <img
                src="{{ $portfolio->thumbnail }}"
                alt="{{ $portfolio->name }}"
                loading="lazy"
                class="aspect-video w-full object-cover"
            />
        @else
            <div class="bg-muted flex aspect-video w-full items-center justify-center">
                <x-lucide name="image" class="text-muted-foreground/50 size-10" />
            </div>
        @endif
    </x-ui.card-header>

    <x-ui.card-content class="space-y-3 p-5">
        <div class="flex items-center justify-between border-b pb-2 font-semibold">
            <span class="truncate" title="{{ $portfolio->name }}">{{ $portfolio->name }}</span>

            <div x-data="{ open: false }" class="relative shrink-0">
                <x-ui.button
                    type="button"
                    variant="ghost"
                    size="icon"
                    class="size-8"
                    x-on:click="open = !open"
                    aria-label="Aksi portfolio"
                >
                    <x-lucide name="ellipsis-vertical" />
                </x-ui.button>

                <div
                    x-show="open"
                    x-cloak
                    x-on:click.outside="open = false"
                    class="bg-popover absolute right-0 z-30 mt-1 w-fit rounded-md border p-1 shadow-lg"
                >
                    <a
                        href="{{ route('dashboard.portfolio.edit', $portfolio->id) }}"
                        class="hover:bg-accent hover:text-accent-foreground flex items-center gap-1 rounded-sm px-2 py-1.5 text-sm"
                    >
                        <x-lucide name="pen" class="size-4" /> Edit
                    </a>
                    <a
                        href="{{ route('portfolio.show', $portfolio->id) }}"
                        target="_blank"
                        class="hover:bg-accent hover:text-accent-foreground flex items-center gap-1 rounded-sm px-2 py-1.5 text-sm"
                    >
                        <x-lucide name="external-link" class="size-4" /> Preview
                    </a>
                    <button
                        type="button"
                        class="text-destructive hover:bg-accent flex items-center gap-1 rounded-sm px-2 py-1.5 text-sm"
                        x-on:click="open = false; $dispatch('open-modal', '{{ $confirmName }}')"
                    >
                        <x-lucide name="trash" class="size-4" /> Delete
                    </button>
                </div>
            </div>
        </div>

        <div class="text-muted-foreground flex items-center gap-1.5 text-sm">
            <x-lucide name="clock" class="size-4" />
            {{ \App\Support\Format::relativeTime($portfolio->updated_at) }}
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($isPublished)
                <span class="inline-flex items-center gap-1 rounded-full bg-green-500/15 px-2.5 py-0.5 text-xs font-medium text-green-700">
                    <x-lucide name="circle-dot" class="size-3" /> Published
                </span>
            @else
                <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/15 px-2.5 py-0.5 text-xs font-medium text-amber-700">
                    <x-lucide name="circle" class="size-3" /> Draft
                </span>
            @endif

            <span class="inline-flex items-center gap-1 rounded-full bg-sky-500/15 px-2.5 py-0.5 text-xs font-medium text-sky-700">
                <x-lucide name="tag" class="size-3" /> {{ $portfolio->category }}
            </span>
        </div>

        <div class="flex flex-wrap gap-1.5">
            @forelse ($stacks as $stack)
                <span class="bg-muted text-muted-foreground rounded px-2 py-0.5 text-xs">{{ $stack }}</span>
            @empty
                <span class="text-muted-foreground text-xs">Tidak ada tech stack</span>
            @endforelse

            @if ($extraStacks > 0)
                <span class="bg-muted text-muted-foreground rounded px-2 py-0.5 text-xs">+{{ $extraStacks }}</span>
            @endif
        </div>
    </x-ui.card-content>
</x-ui.card>

<x-ui.confirm-dialog
    :name="$confirmName"
    title="Hapus Portofolio"
    :description="'Apakah Anda yakin ingin menghapus portofolio &quot;'.$portfolio->name.'&quot;? Tindakan ini tidak dapat dibatalkan.'"
>
    <form method="POST" action="{{ route('dashboard.portfolio.destroy', $portfolio->id) }}">
        @csrf
        @method('DELETE')
        <x-ui.button type="submit" variant="destructive">Hapus</x-ui.button>
    </form>
</x-ui.confirm-dialog>
