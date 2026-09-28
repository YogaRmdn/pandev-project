@props(['paginator' => null, 'limitOptions' => []])

@if ($paginator && $paginator->hasPages())
    <div class="flex w-full flex-col gap-3 border sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <span class="text-muted-foreground text-sm">Limit</span>
            <form method="GET" class="flex items-center gap-2">
                @foreach (request()->except(['limit', 'page']) as $key => $value)
                    @if (is_array($value))
                        @foreach ($value as $item)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach

                <x-ui.select name="limit" class="h-9 w-24" x-on:change="$el.form.submit()">
                    @foreach ($limitOptions as $option)
                        <option value="{{ $option }}" @selected((int) request('limit', 9) === $option)>{{ $option }}</option>
                    @endforeach
                </x-ui.select>
            </form>
        </div>

        <nav role="navigation" aria-label="Pagination" class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="text-muted-foreground pointer-events-none inline-flex h-9 items-center rounded-md border px-3 text-sm opacity-50">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="border hover:bg-accent hover:text-accent-foreground inline-flex h-9 items-center rounded-md px-3 text-sm">Sebelumnya</a>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                <a
                    href="{{ $url }}"
                    @class([
                        'inline-flex size-9 items-center justify-center rounded-md border text-sm',
                        'bg-primary text-primary-foreground' => $page === $paginator->currentPage(),
                        'hover:bg-accent hover:text-accent-foreground' => $page !== $paginator->currentPage(),
                    ])
                >{{ $page }}</a>
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="border hover:bg-accent hover:text-accent-foreground inline-flex h-9 items-center rounded-md px-3 text-sm">Selanjutnya</a>
            @else
                <span class="text-muted-foreground pointer-events-none inline-flex h-9 items-center rounded-md border px-3 text-sm opacity-50">Selanjutnya</span>
            @endif
        </nav>
    </div>
@endif
