@props(['portfolio'])

@php
    $isPublished = $portfolio->status === \App\Enums\PortfolioStatus::PUBLISHED;
    $stacks = array_slice((array) $portfolio->tech_stacks, 0, 2);
    $extraStacks = max(0, count((array) $portfolio->tech_stacks) - 2);
    $confirmName = 'portfolio-delete-'.$portfolio->id;
@endphp

<div class="group flex h-full flex-col overflow-hidden rounded-2xl border border-base-300/70 bg-base-100 shadow-sm transition-shadow duration-300 hover:shadow-md">
    {{-- Thumbnail --}}
    <div class="relative aspect-video w-full overflow-hidden">
        @if (filled($portfolio->thumbnail))
            <img
                src="{{ $portfolio->thumbnail }}"
                alt="{{ $portfolio->name }}"
                loading="lazy"
                class="size-full object-cover transition-transform duration-300 group-hover:scale-105"
            />
        @else
            <div class="bg-base-200 grid size-full place-items-center">
                <x-lucide name="image" class="size-10 text-base-content/40" />
            </div>
        @endif

        @if ($isPublished)
            <span
                class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full border border-white/20 bg-white/90 px-2.5 py-0.5 text-xs font-medium text-success shadow-sm backdrop-blur">
                <x-lucide name="circle-dot" class="size-3" /> Published
            </span>
        @else
            <span
                class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full border border-white/20 bg-white/90 px-2.5 py-0.5 text-xs font-medium text-warning shadow-sm backdrop-blur">
                <x-lucide name="circle" class="size-3" /> Draft
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-3 p-5">
        <div class="flex items-start justify-between gap-2">
            <a href="{{ route('dashboard.portfolio.edit', $portfolio->id) }}"
                class="min-w-0 truncate font-heading text-base font-semibold text-base-content transition-colors hover:text-primary"
                title="{{ $portfolio->name }}">
                {{ $portfolio->name }}
            </a>

            <div x-data="{ open: false }" class="relative shrink-0">
                <button type="button" class="btn btn-ghost btn-square size-8" x-on:click="open = !open" aria-label="Aksi portfolio">
                    <x-lucide name="ellipsis-vertical" />
                </button>

                <div
                    x-show="open"
                    x-cloak
                    x-on:click.outside="open = false"
                    class="bg-base-100 absolute right-0 z-30 mt-1 w-40 rounded-lg border border-base-300/70 p-1 shadow-lg"
                >
                    <a
                        href="{{ route('dashboard.portfolio.edit', $portfolio->id) }}"
                        class="hover:bg-base-200 hover:text-base-content flex items-center gap-2 rounded-md px-2 py-1.5 text-sm"
                    >
                        <x-lucide name="pen" class="size-4" /> Edit
                    </a>
                    <a
                        href="{{ route('portfolio.show', $portfolio->id) }}"
                        target="_blank"
                        class="hover:bg-base-200 hover:text-base-content flex items-center gap-2 rounded-md px-2 py-1.5 text-sm"
                    >
                        <x-lucide name="external-link" class="size-4" /> Preview
                    </a>
                    <button
                        type="button"
                        class="text-error hover:bg-base-200 flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-sm"
                        x-on:click="open = false; $dispatch('open-modal', '{{ $confirmName }}')"
                    >
                        <x-lucide name="trash" class="size-4" /> Delete
                    </button>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-base-content/50">
            <span class="inline-flex items-center gap-1.5">
                <x-lucide name="tag" class="size-3.5" /> {{ $portfolio->category }}
            </span>
            <span class="inline-flex items-center gap-1.5">
                <x-lucide name="clock" class="size-3.5" /> {{ \App\Support\Format::relativeTime($portfolio->updated_at) }}
            </span>
        </div>

        <div class="mt-auto flex flex-wrap gap-1.5 pt-1">
            @forelse ($stacks as $stack)
                <span class="bg-base-200 text-base-content/60 rounded-md px-2 py-0.5 text-xs">{{ $stack }}</span>
            @empty
                <span class="text-base-content/50 text-xs">Tidak ada tech stack</span>
            @endforelse

            @if ($extraStacks > 0)
                <span class="bg-base-200 text-base-content/60 rounded-md px-2 py-0.5 text-xs">+{{ $extraStacks }}</span>
            @endif
        </div>
    </div>
</div>

<x-ui.confirm-dialog
    :name="$confirmName"
    title="Hapus Portofolio"
    :description="'Apakah Anda yakin ingin menghapus portofolio &quot;'.$portfolio->name.'&quot;? Tindakan ini tidak dapat dibatalkan.'"
>
    <form method="POST" action="{{ route('dashboard.portfolio.destroy', $portfolio->id) }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-error">Hapus</button>
    </form>
</x-ui.confirm-dialog>