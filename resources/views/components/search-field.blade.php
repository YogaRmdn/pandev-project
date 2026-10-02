@props(['placeholder' => 'Search...', 'value' => ''])

<form method="GET" x-data class="relative flex-1" x-on:submit="$el.submit()">
    @foreach (request()->except(['search', 'page']) as $key => $value)
        @if (is_array($value))
            @foreach ($value as $item)
                <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach

    <x-lucide name="search" class="text-base-content/60 pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2" />

    <input class="input w-full bg-base-100 pl-9" name="search" value="{{ $value }}" placeholder="{{ $placeholder }}" x-on:input.debounce.400ms="$el.form.submit()">
</form>
