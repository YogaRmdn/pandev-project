<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

/**
 * Renders Lucide SVG icons with attributes merged cleanly.
 *
 * The upstream lucide-static files ship their own `class` and `stroke-width`
 * attributes. Passing those through Blade Icons' @svg() directive produces
 * duplicate attributes, so we merge them ourselves instead.
 */
class Icon
{
    public const DIRECTORY = 'node_modules/lucide-static/icons';

    /**
     * @param  array<string, string>  $attributes
     */
    public static function render(string $name, array $attributes = []): string
    {
        $path = base_path(static::DIRECTORY.'/'.$name.'.svg');

        if (! File::exists($path)) {
            $path = base_path(static::DIRECTORY.'/circle.svg');
        }

        $svg = File::get($path);

        // The lucide-static files start with a UTF-8 BOM followed by a
        // license comment, both of which have to go before the opening tag.
        $svg = preg_replace('/^\xEF\xBB\xBF/', '', $svg);
        $svg = preg_replace('/<!--.*?-->\s*/s', '', $svg);

        $classes = $attributes['class'] ?? '';
        $classes = trim($classes !== '' ? $classes : 'size-4 shrink-0');

        unset($attributes['class']);

        $attributes['xmlns'] ??= 'http://www.w3.org/2000/svg';
        $attributes['stroke-width'] ??= '2';
        $attributes['aria-hidden'] ??= 'true';
        $attributes['class'] = $classes;

        $rendered = collect($attributes)
            ->map(fn (string $value, string $key) => $key.'="'.e($value).'"')
            ->implode(' ');

        // Drop the attributes the source file already declares so we do not
        // emit them twice.
        $svg = preg_replace('/\s(class|stroke-width)="[^"]*"/', '', $svg, 1);

        $inner = preg_replace('/^<svg[^>]*>/', '', trim($svg));
        $inner = preg_replace('/<\/svg>\s*$/', '', $inner);

        return '<svg '.$rendered.'>'.$inner.'</svg>';
    }
}
