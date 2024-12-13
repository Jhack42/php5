import { createApp } from 'vue';
import App from './App.vue';
import { createRouter, createWebHistory } from 'vue-router';
import '@fullcalendar/core/vdom'; // necesario para FullCalendar en Vue 3

// Importación de vistas
import Home from './views/Home.vue';
import About from './views/About.vue';
import Contact from './views/Contact.vue';

// Configuración de rutas
const routes = [
  {
    path: '/',
    name: 'home',
    component: Home
  },
  {
    path: '/about',
    name: 'about',
    component: About
  },
  {
    path: '/contact',
    name: 'contact',
    component: Contact
  },
  // Ruta 404 para manejar rutas no encontradas
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    redirect: '/'
  }
];

// Crear instancia del router
const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Crear y montar la aplicación
const app = createApp(App);

// Usar el router
app.use(router);

// Montar la aplicación
app.mount('#app');
