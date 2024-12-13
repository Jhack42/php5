// resources/js/app.js

import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';
import ThemeDetector from './components/ThemeDetector.vue';

const app = createApp(App);
app.component('theme-detector', ThemeDetector); // Cambio aquí
app.mount('#app');
