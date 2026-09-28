import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    css: {
        // Tailwind runs through its Vite plugin, so PostCSS must not go looking
        // for a config. Without this it walks up and picks up the old Next.js
        // root config, which pulls in @tailwindcss/postcss.
        postcss: { plugins: [] },
    },
});
