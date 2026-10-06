@props(['paginator' => null, 'limitOptions' => []])

<div class="mt-4 flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-2">
        <span class="text-base-content/60 text-sm">Limit</span>
        <form method="GET" class="flex w-24 items-center gap-2">
            @foreach (request()->except(['limit', 'page']) as $key => $value)
                @if (is_array($value))
                    @foreach ($value as $item)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach

            <select class="select h-9 w-full" name="limit" x-on:change="$el.form.submit()">
                @foreach ($limitOptions as $option)
                    <option value="{{ $option }}" @selected((int) request('limit', 9) === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @php
        $last = $paginator ? max(1, $paginator->lastPage()) : 1;
        $current = $paginator ? $paginator->currentPage() : 1;

        // Show every page when there are 10 or fewer; otherwise collapse to 1 2 … last.
        $pages = $last <= 10 ? range(1, $last) : [1, 2, '…', $last];
    @endphp

    <nav role="navigation" aria-label="Pagination" class="flex items-center gap-1">
        @if ($paginator && ! $paginator->onFirstPage())
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                class="border hover:bg-base-200 hover:text-base-content inline-flex h-9 items-center rounded-md px-3 text-sm">Previous</a>
        @else
            <span
                class="text-base-content/60 pointer-events-none inline-flex h-9 items-center rounded-md border px-3 text-sm opacity-50">Previous</span>
        @endif

        @foreach ($pages as $page)
            @if ($page === '…')
                <span class="text-base-content/60 inline-flex h-9 items-center px-1">…</span>
            @elseif ($page === $current)
                <span
                    class="bg-primary text-primary-content inline-flex size-9 items-center justify-center rounded-md border text-sm">{{ $page }}</span>
            @elseif ($paginator)
                <a href="{{ $paginator->url($page) }}"
                    class="hover:bg-base-200 hover:text-base-content inline-flex size-9 items-center justify-center rounded-md border text-sm">{{ $page }}</a>
            @else
                <span
                    class="text-base-content/60 pointer-events-none inline-flex size-9 items-center justify-center rounded-md border text-sm opacity-50">{{ $page }}</span>
            @endif
        @endforeach

        @if ($paginator && $paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                class="border hover:bg-base-200 hover:text-base-content inline-flex h-9 items-center rounded-md px-3 text-sm">Next</a>
        @else
            <span
                class="text-base-content/60 pointer-events-none inline-flex h-9 items-center rounded-md border px-3 text-sm opacity-50">Next</span>
        @endif
    </nav>
</div>
