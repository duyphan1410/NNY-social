import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                //css
                'resources/css/app.css',
                'resources/css/create.css',
                'resources/css/detail.css',
                'resources/css/edit.css',
                'resources/css/friends.css',
                'resources/css/home.css',
                //js
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/create.js',
                'resources/js/detail.js',
                'resources/js/edit.js',
                'resources/js/home.js',
                'resources/sass/app.scss', // Đảm bảo file SCSS này tồn tại
            ],
            refresh: true,
        }),
    ],
});
