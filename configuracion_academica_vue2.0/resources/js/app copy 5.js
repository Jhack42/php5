// resources/js/app.js

import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import App from './components/App.vue';
import ThemeDetector from './components/ThemeDetector.vue';
import Calendario from './components/proyecto/Eventos/Calendario.vue';
import App_Mejora from './components/proyecto/Eventos/App.vue';
import CarouselFrontend from './components/proyecto/Noticias/CarouselFrontend.vue';
import CarouselAdmin from './components/proyecto/Noticias/CarouselAdmin.vue';

// Configuración de Pusher
window.Pusher = Pusher;

const PUSHER_CONFIG = {
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY || 'local',
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST || '127.0.0.1',
    wsPort: import.meta.env.VITE_PUSHER_PORT || 6001,
    wssPort: import.meta.env.VITE_PUSHER_PORT || 6001,
    forceTLS: false,
    encrypted: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
};

// Crear instancia de Echo
window.Echo = new Echo(PUSHER_CONFIG);

// Plugin para Vue que maneja la conexión de Pusher
const PusherPlugin = {
    install(app) {
        app.config.globalProperties.$echo = window.Echo;

        app.provide('echo', window.Echo);
        app.provide('pusherConfig', {
            ...PUSHER_CONFIG,
            connect: () => {
                if (!window.Echo.connector.pusher.connection.state === 'connected') {
                    window.Echo.connector.pusher.connect();
                }
            },
            disconnect: () => {
                if (window.Echo.connector.pusher.connection.state === 'connected') {
                    window.Echo.connector.pusher.disconnect();
                }
            },
            subscribe: (channel) => {
                return window.Echo.channel(channel);
            }
        });
    }
};

// Router
const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: () => import('../js/components/siderar/App.vue')
        },
        {
            path: '/calendar',
            component: App_Mejora
        },
        {
            path: '/noticias',
            component: CarouselFrontend
        },
        {
            path: '/admin/noticias',
            component: CarouselAdmin
        }
    ]
});

// Middleware de navegación
router.beforeEach(async (to, from, next) => {
    const carouselRoutes = ['/noticias', '/admin/noticias'];
    const needsPusher = carouselRoutes.includes(to.path);

    try {
        if (needsPusher) {
            console.log('Configurando canales Pusher para:', to.path);

            // Suscribirse al canal del carrusel
            const channel = window.Echo.channel('carousel');

            // Configurar listeners
            channel
                .listen('CarouselUpdated', (e) => {
                    console.log('Carrusel actualizado:', e);
                })
                .listen('CarouselDeleted', (e) => {
                    console.log('Item eliminado:', e);
                })
                .listen('CarouselReordered', (e) => {
                    console.log('Items reordenados:', e);
                });
        } else {
            // Limpiar suscripciones si salimos de las rutas del carrusel
            window.Echo.leave('carousel');
        }
        next();
    } catch (error) {
        console.error('Error en la configuración de Pusher:', error);
        next();
    }
});

// Crear aplicación Vue
const app = createApp(App);

// Registrar componentes globales
app.component('theme-detector', ThemeDetector);

// Usar plugins
app.use(router);
app.use(PusherPlugin);

// Manejo global de errores
app.config.errorHandler = (err, vm, info) => {
    console.error('Error en la aplicación:', {
        error: err,
        component: vm?.$options?.name || 'Unknown',
        info,
        pusherState: window.Echo?.connector?.pusher?.connection?.state || 'no-connection'
    });
};

// Montar la aplicación
app.mount('#app');

// Exportar configuraciones para uso en otros componentes
/*export {
    window.Echo as echo,
    PUSHER_CONFIG as pusherConfig
};
*/
