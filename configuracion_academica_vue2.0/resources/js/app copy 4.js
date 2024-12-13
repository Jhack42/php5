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

// Configuración del WebSocket
const websocketUrl = `${import.meta.env.VITE_WEBSOCKET_SCHEME}://${import.meta.env.VITE_WEBSOCKET_HOST}:${import.meta.env.VITE_WEBSOCKET_PORT}`;
const backendUrl = import.meta.env.VITE_APP_BACKEND_URL;

// Crear la instancia de Socket.IO con la configuración completa
const socket = io(`${websocketUrl}/carousel`, {
    withCredentials: true,
    autoConnect: false,
    transports: ['websocket'],
    path: '/socket.io',
    reconnection: true,
    reconnectionAttempts: 5,
    reconnectionDelay: 1000,
    timeout: 10000,
    forceNew: true,
    query: {
        appName: import.meta.env.VITE_APP_NAME
    }
});

// Manejadores de eventos del socket
socket.on('connect', () => {
    console.log('Socket conectado:', socket.id);
});

socket.on('connect_error', (error) => {
    console.error('Error de conexión del socket:', error);
});

socket.on('disconnect', (reason) => {
    console.log('Socket desconectado:', reason);
});

// Plugin de Socket.IO para Vue mejorado
const SocketPlugin = {
    install(app) {
        // Proporcionar el socket a través de globalProperties
        app.config.globalProperties.$socket = socket;

        // Proporcionar el socket a través de la API de composición
        app.provide('socket', socket);

        // Proporcionar configuración del WebSocket
        app.provide('websocketConfig', {
            url: websocketUrl,
            backendUrl: backendUrl,
            reconnectionAttempts: 5,
            timeout: 10000
        });
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
    }
];

// Crear el router
const router = createRouter({
    history: createWebHistory(),
    routes
});

// Crear la aplicación una sola vez
const app = createApp(App);

// Registrar componentes globales
app.component('theme-detector', ThemeDetector);

// Usar el router y el plugin de Socket.IO
app.use(router);
app.use(SocketPlugin);

// Middleware de navegación mejorado para manejar la conexión del socket
router.beforeEach((to, from, next) => {
    const carouselRoutes = ['/noticias', '/admin/noticias'];

    if (carouselRoutes.includes(to.path)) {
        if (!socket.connected) {
            console.log('Conectando socket para ruta del carrusel:', to.path);
            socket.connect();
        }
    } else {
        if (socket.connected) {
            console.log('Desconectando socket al salir del carrusel');
            socket.disconnect();
        }
    }
    next();
});

// Manejo de errores global
app.config.errorHandler = (err, vm, info) => {
    console.error('Error en la aplicación:', err, info);
    // Aquí podrías implementar un sistema de notificación de errores
};

// Montar la aplicación
app.mount('#app');

// Exportar el socket y la configuración para usarlos en otros componentes
export { socket, websocketUrl, backendUrl };
