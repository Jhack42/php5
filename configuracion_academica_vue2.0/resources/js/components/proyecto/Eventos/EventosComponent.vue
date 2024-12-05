<template>
    <div class="column">
      <!-- Barra de Tabs para cambiar entre vista de Tabla y Calendario -->
      <div class="tabs is-centered">
        <ul>
          <li :class="{ 'is-active': vistaActiva === 'tabla' }">
            <a @click="cambiarVista('tabla')">Tabla</a>
          </li>
          <li :class="{ 'is-active': vistaActiva === 'calendario' }">
            <a @click="cambiarVista('calendario')">Calendario</a>
          </li>
        </ul>
      </div>

      <!-- Componente dinámico -->
      <component :is="componenteActual"></component>
    </div>
  </template>

  <script>
  import App from './App.vue';
  import Tabla from './Calendario.vue';

  export default {
    name: 'Main',
    components: {
      App,
      Tabla,
    },
    data() {
      return {
        vistaActiva: 'tabla', // Estado inicial
      };
    },
    computed: {
      componenteActual() {
        // Devuelve el componente correspondiente basado en la vista activa
        return this.vistaActiva === 'tabla' ? 'App' : 'Tabla';
      },
    },
    methods: {
      cambiarVista(vista) {
        this.vistaActiva = vista;
      },
    },
  };
  </script>
