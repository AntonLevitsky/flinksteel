import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/islands/product-configurator.tsx',
                'resources/js/islands/cart-summary.tsx',
                'resources/js/islands/cart-table.tsx',
                'resources/js/islands/category-filter.tsx',
                'resources/js/islands/search-autocomplete.tsx',
                'resources/js/islands/toast-notifier.tsx',
                'resources/js/category-ajax.ts',
                'resources/js/islands/quick-order-pad.tsx',
            ],
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
