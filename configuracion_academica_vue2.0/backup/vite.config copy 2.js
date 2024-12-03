import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue'; // Asegúrate de que estás importando el plugin de Vue
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        vue(),  // Usamos el plugin de Vue
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            external: ['vue'], // Si no quieres externalizar Vue, asegúrate de quitar esto
        },
    },
});
