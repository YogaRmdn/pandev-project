@props(['placeholder' => 'Search...', 'value' => ''])

<form method="GET" x-data class="relative flex-1" x-on:submit="$el.submit()">
    @foreach (request()->except(['search', 'page']) as $key => $queryValue)
        @if (is_array($queryValue))
            @foreach ($queryValue as $item)
                <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $queryValue }}">
        @endif
    @endforeach

    <x-lucide-search name="search" class="text-base-content/60 pointer-events-none absolute top-3 left-3 size-4" />

    <input class="input w-full bg-base-100 pl-9" name="search" value="{{ $value }}" placeholder="{{ $placeholder }}" x-on:input.debounce.400ms="$el.form.submit()">
</form>
