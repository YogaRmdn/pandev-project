@props(['class' => ''])

{{-- No layout is imposed here: callers stack a column, or pass their own flex
     row (see x-dashboard.stat-card). A default `flex items-center` forced
     every column-style card into a row. --}}
<div {{ $attributes->merge(['class' => $class]) }}>
    {{ $slot }}
</div>
