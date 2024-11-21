<template>
    <div class="eventos-component columns">
      <div class="column is-one-quarter">
        <Filtros :filtrosVisibles="filtrosVisibles" :valoresFiltros="valoresFiltros" @aplicarFiltros="aplicarFiltros" @toggleConfiguracion="mostrarConfiguracion = true" />
      </div>
      <ConfiguracionModal v-if="mostrarConfiguracion" :mostrarConfiguracion="mostrarConfiguracion" :configuracionActiva="configuracionActiva" :opcionesFiltros="opcionesFiltros" :opcionesColumnas="opcionesColumnas" @toggleModal="mostrarConfiguracion = false" @guardarConfiguracionFiltros="guardarConfiguracionFiltros" @guardarConfiguracionColumnas="guardarConfiguracionColumnas" />
      <div class="column">
        <EventosTable :columnasVisibles="columnasVisibles" :eventosFiltrados="eventosFiltrados" />
      </div>
    </div>
  </template>

  <script>
  import Filtros from './Eventos/Filtros.vue';
  import ConfiguracionModal from './ConfiguracionModal.vue';
  import EventosTable from './Eventos/EventosTable.vue';
  import axios from 'axios';
  import Cookies from 'js-cookie';

  export default {
    components: { Filtros, ConfiguracionModal, EventosTable },
    data() {
      return {
        // Data properties as before
      };
    },
    computed: {
      filtrosVisibles() {
        return this.opcionesFiltros.filter(filtro => filtro.visible);
      },
      columnasVisibles() {
        return this.opcionesColumnas.filter(col => col.visible);
      },
    },
    methods: {
      // Methods as before
    },
    mounted() {
      this.cargarDatos();
      this.cargarConfiguracion();
    }
  };
  </script>

  <style scoped>
  .eventos-component {
    display: flex;
  }
  </style>
