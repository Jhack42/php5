import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue'; // Importa el plugin de Vue
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    vue(), // Asegúrate de que Vue esté incluido
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
});
