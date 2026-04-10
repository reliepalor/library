import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin/students-page.css',
                'resources/js/app.js',
                'resources/js/admin/students-page.js',
            ],
            refresh: true,
        }),
    ],
});
