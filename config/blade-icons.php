<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Icons Sets
    |--------------------------------------------------------------------------
    |
    | The Lucide icon set is loaded from node_modules. Icons are resolved at
    | runtime via the @svg() directive rather than compiled Blade classes,
    | because the set contains ~2100 icons and component discovery would
    | compile every single one of them on boot.
    |
    */

    'sets' => [

        'lucide' => [
            'path' => 'node_modules/lucide-static/icons',
            'prefix' => 'lucide',
            'fallback' => 'lucide-circle',
            'class' => 'size-4 shrink-0',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global Default Classes
    |--------------------------------------------------------------------------
    */

    'class' => 'size-4 shrink-0',

    /*
    |--------------------------------------------------------------------------
    | Global Default Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'stroke-width' => 2,
        'aria-hidden' => 'true',
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Fallback Icon
    |--------------------------------------------------------------------------
    */

    'fallback' => 'lucide-circle',

    /*
    |--------------------------------------------------------------------------
    | Components
    |--------------------------------------------------------------------------
    |
    | Disabled on purpose — see the note above. Use @svg('lucide-menu').
    |
    */

    'components' => [
        'disabled' => true,
        'default' => 'icon',
    ],

];
