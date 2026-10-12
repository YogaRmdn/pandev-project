@props([
    'title' => null,
    'description' => null,
    'icon' => null,
    'count' => null,
])

<section {{ $attributes->class(['overflow-hidden rounded-2xl border border-base-300/70 bg-base-100 shadow-sm']) }}>
    @if ($title || isset($actions))
        <div class="flex flex-col gap-3 border-b border-base-300/70 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="flex min-w-0 items-center gap-3">
                @if ($icon)
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-primary/10 text-primary">
                        <x-lucide :name="$icon" class="size-5" />
                    </span>
                @endif

                <div class="min-w-0">
                    <h2 class="flex items-center gap-2 font-heading text-base font-semibold text-base-content">
                        {{ $title }}
                        @if ($count !== null)
                            <span class="badge badge-sm border-0 bg-primary/10 font-semibold text-primary">
                                {{ $count }}
                            </span>
                        @endif
                    </h2>
                    @if ($description)
                        <p class="truncate text-xs text-base-content/50">{{ $description }}</p>
                    @endif
                </div>
            </div>

            @isset($actions)
                <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    {{ $slot }}
</section>
