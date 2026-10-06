@props([
    'href' => '#',
    'label',
    'active' => false,
    'class' => '',
    'icon' => null,
])
<li>
    <a href="{{ $href }}" @class([
        'is-drawer-close:tooltip is-drawer-close:tooltip-right text-lg py-2 active:bg-primary active:text-white!',
        $class,
        'bg-primary text-white' => $active,
    ]) data-tip="{{ $label }}">
        {{ $slot }}
        @if ($icon)
            <div @class([
                'size-6 text-primary active:text-white',
                'text-white' => $active,
            ])>
                @svg($icon)
            </div>
        @endif
        <span class="is-drawer-close:hidden">
            {{ $label }}
        </span>
    </a>
</li>
