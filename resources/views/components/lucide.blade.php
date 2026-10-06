@props(['name', 'class' => ''])
{!! \App\Support\Icon::render($name, array_merge($attributes->except('name')->all(), ['class' => $class])) !!}
