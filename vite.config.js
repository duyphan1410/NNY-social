import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.css',
                'resources/js/home.js',
                'resources/css/home.css',
                'resources/js/create.js',
                'resources/css/create.css',
                'resources/sass/app.scss', // Đảm bảo file SCSS này tồn tại
            ],
            refresh: true,
        }),
    ],
});
