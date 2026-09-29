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
        if (preg_match('/^\xEF\xBB\xBF/', $svg)) {
            $svg = substr($svg, 3);
        }
        $svg = preg_replace('/<!--.*?-->\s*/s', '', $svg);

        $classes = trim($attributes['class'] ?? '');

        if ($classes === '') {
            $classes = 'size-4 shrink-0';
        } elseif (! preg_match('/(^|\s)size-/', $classes)) {
            // The source file carries width/height, so an icon whose class
            // sets no size would silently render at the 24px file default.
            $classes .= ' size-4';
        }

        unset($attributes['class']);

        // Pull the intrinsic attributes (viewBox, fill, stroke, line caps and
        // joins) off the source <svg> tag and carry them over, otherwise the
        // icon loses its viewBox (gets clipped) and fill=none/stroke rules
        // (renders as a solid black blob).
        $sourceAttributes = [];
        if (preg_match('/^<svg([^>]*)>/', trim($svg), $match)) {
            preg_match_all('/([a-zA-Z0-9:_-]+)="([^"]*)"/', $match[1], $pairs, PREG_SET_ORDER);
            foreach ($pairs as $pair) {
                $sourceAttributes[$pair[1]] = $pair[2];
            }
            unset($sourceAttributes['class'], $sourceAttributes['stroke-width']);
        }

        $attributes['xmlns'] ??= 'http://www.w3.org/2000/svg';
        $attributes['stroke-width'] ??= '2';
        $attributes['aria-hidden'] ??= 'true';
        $attributes['class'] = $classes;

        $attributes = array_merge($sourceAttributes, $attributes);

        $rendered = collect($attributes)
            ->map(fn (string $value, string $key) => $key.'="'.e($value).'"')
            ->implode(' ');

        $inner = preg_replace('/^<svg[^>]*>/', '', trim($svg));
        $inner = preg_replace('/<\/svg>\s*$/', '', $inner);

        return '<svg '.$rendered.'>'.$inner.'</svg>';
    }
}
