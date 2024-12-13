// resources/js/app.js

import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router'
import App from './components/App.vue'  // Cambiado aquí
import ThemeDetector from './components/ThemeDetector.vue';
import Calendario from './components/proyecto/Eventos/Calendario.vue'
import App_Mejora from './components/proyecto/Eventos/App.vue'
import CarouselFrontend from './components/proyecto/Noticias/CarouselFrontend.vue'
import CarouselAdmin from './components/proyecto/Noticias/CarouselAdmin.vue'


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
]

// Crear el router
const router = createRouter({
    history: createWebHistory(),
    routes
})

// Crear la aplicación una sola vez
const app = createApp(App)

// Registrar componentes globales
app.component('theme-detector', ThemeDetector);

// Usar el router
app.use(router)

// Montar la aplicación
app.mount('#app')
