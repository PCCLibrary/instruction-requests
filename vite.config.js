import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/public.js',
                'resources/js/table-lock-refresh.js',
                'resources/js/edit-lock-refresh.js',
                'resources/js/campus-edit.js'
            ],
            refresh: true,
        }),
    ],
});
