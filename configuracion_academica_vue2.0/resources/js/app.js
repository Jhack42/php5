// resources/js/app.js

import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { io } from 'socket.io-client';
import App from './components/App.vue';
import ThemeDetector from './components/ThemeDetector.vue';
import Calendario from './components/proyecto/Eventos/Calendario.vue';
import App_Mejora from './components/proyecto/Eventos/App.vue';
import CarouselFrontend from './components/proyecto/Noticias/CarouselFrontend.vue';
import CarouselAdmin from './components/proyecto/Noticias/CarouselAdmin.vue';
import PreviewFinal from './components/proyecto/Noticias/PreviewFinal.vue';

// Configuración del WebSocket
const socketUrl = 'http://localhost:3000';
const socket = io(socketUrl, {
    autoConnect: false,
    reconnection: true,
    reconnectionAttempts: 5,
    reconnectionDelay: 1000,
    transports: ['websocket', 'polling']
});

// Plugin de Socket.IO para Vue
const SocketPlugin = {
    install(app) {
        app.config.globalProperties.$socket = socket;
        app.provide('socket', socket);
    }
};

// Definir las rutas
const routes = [
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
    },
    {
        path: '/admin/noticias/previewfinal',
        component: PreviewFinal
    }
];

// Crear el router
const router = createRouter({
    history: createWebHistory(),
    routes
});

// Middleware para manejar la conexión del WebSocket
router.beforeEach((to, from, next) => {
    const carouselRoutes = ['/noticias', '/admin/noticias'];
    if (carouselRoutes.includes(to.path)) {
        if (!socket.connected) {
            socket.connect();
        }
    } else if (socket.connected) {
        socket.disconnect();
    }
    next();
});

// Crear la aplicación Vue
const app = createApp(App);

// Registrar componentes y plugins
app.component('theme-detector', ThemeDetector);
app.use(router);
app.use(SocketPlugin);

// Manejo de errores global
app.config.errorHandler = (err, vm, info) => {
    console.error('Error en la aplicación:', {
        error: err,
        component: vm?.$options?.name || 'Unknown',
        info,
        socketConnected: socket.connected
    });
};

// Montar la aplicación
app.mount('#app');

// Exportar el socket para uso en componentes
export { socket };
