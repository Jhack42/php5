import { createApp } from 'vue';
import App from './App.vue';
import axios from 'axios';

// Configura Axios para el acceso a la API Flask
axios.defaults.baseURL = 'http://localhost:5000';

const app = createApp(App);

// Agregar Axios a todas las instancias de Vue
app.config.globalProperties.$axios = axios;

app.mount('#app');
