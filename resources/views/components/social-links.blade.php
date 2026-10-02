@props(['size' => 'size-5'])

<div {{ $attributes->merge(['class' => 'flex items-center']) }}>
    @foreach (App\Support\SiteContent::socials() as $social)
        <a
            href="{{ $social['href'] }}"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="{{ $social['name'] }}"
            title="{{ $social['name'] }}"
            class="text-base-content/60 hover:text-base-content inline-flex items-center justify-center transition-colors"
        >
            <x-brand-icon :name="$social['name']" :class="$size" />
        </a>
    @endforeach
</div>
