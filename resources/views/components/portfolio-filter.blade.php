@props(['categories' => [], 'selected' => []])

<div x-data>
    <x-ui.button
        variant="{{ count($selected) > 0 ? 'default' : 'outline' }}"
        x-on:click="$dispatch('open-modal', 'portfolio-filter')"
    >
        <x-lucide name="filter" />
        Filter
        @if (count($selected) > 0)
            <span class="text-primary bg-primary-foreground ml-1 flex size-5 items-center justify-center rounded-full text-xs">{{ count($selected) }}</span>
        @endif
    </x-ui.button>

    <x-ui.dialog
        name="portfolio-filter"
        title="Filter Projects"
        description="Select categories to filter the projects."
    >
        <form method="GET" class="grid gap-4">
            @foreach (request()->except(['category', 'page']) as $key => $value)
                @if (is_array($value))
                    @foreach ($value as $item)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach

            <fieldset class="space-y-3">
                <legend class="mb-2 text-sm font-medium">Category</legend>
                <div class="flex flex-wrap gap-2">
                    @foreach ($categories as $category)
                        <label class="cursor-pointer">
                            <input
                                type="checkbox"
                                name="category[]"
                                value="{{ $category }}"
                                @checked(in_array($category, $selected, true))
                                class="peer sr-only"
                            />
                            <span
                                class="border hover:bg-accent hover:text-accent-foreground inline-flex h-8 items-center rounded-md border px-3 text-sm transition-colors peer-checked:bg-primary peer-checked:text-primary-foreground"
                            >{{ $category }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <x-ui.button type="button" variant="outline" x-on:click="$store.modals.close('portfolio-filter')">Cancel</x-ui.button>
                <x-ui.button type="submit">Apply filters</x-ui.button>
            </div>
        </form>
    </x-ui.dialog>
</div>
