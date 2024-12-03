// resources/js/app.js

import './bootstrap';
import { createApp } from 'vue'; // Vue 3
import App from './components/App.vue';

const app = createApp(App);
app.mount('#app');
