<template>
    <div>
      <!-- Breadcrumb -->
      <Breadcrumb
        :niveles="niveles"
        :nivel-actual="nivelActual"
        @navegar="cambiarNivel"
      />

      <!-- Contenido según el nivel -->
      <div v-if="nivelActual === 0">
        <Tarjetas
          :categorias="categorias"
          @seleccionar-tarjeta="mostrarTabla"
          @seleccionar-especialidad="mostrarTablaPorEspecialidad"
        />
      </div>
      <div v-else>
        <Filtro :datos="datosOriginales" @aplicar-filtro="actualizarFiltro" />
        <Tabla :datos="datosFiltrados" />
      </div>
    </div>
  </template>

  <script>
  import Breadcrumb from './Breadcrumb.vue';
  import Tarjetas from './Tarjetas.vue';
  import Filtro from './Filtro.vue';
  import Tabla from './Tabla.vue';
  import axios from 'axios';

  export default {
    components: { Breadcrumb, Tarjetas, Filtro, Tabla },
    data() {
      return {
        niveles: ['Facultades', 'Tabla'], // Niveles dinámicos
        nivelActual: 0, // Nivel inicial
        datosOriginales: [], // Datos obtenidos de la API
        filtroActual: '', // Filtro activo
        categorias: {}, // Estructura para facultades y especialidades
      };
    },
    computed: {
      datosFiltrados() {
        // Filtrar los datos según el filtro actual
        if (!this.filtroActual) return this.datosOriginales;
        return this.datosOriginales.filter(
          (dato) =>
            dato.facultad?.includes(this.filtroActual) ||
            dato.especialidad?.includes(this.filtroActual)
        );
      },
    },
    methods: {
      async cargarDatos() {
        try {
          const respuesta = await axios.get(
            'http://localhost:3000/api/data/para-la-vita1'
          );
          this.datosOriginales = respuesta.data;

          // Crear estructura de categorías (facultades y sus especialidades)
          this.categorias = this.datosOriginales.reduce((result, dato) => {
            const facultad = dato.facultad;
            const especialidad = dato.especialidad;

            if (!result[facultad]) {
              result[facultad] = [];
            }

            if (especialidad && !result[facultad].includes(especialidad)) {
              result[facultad].push(especialidad);
            }

            return result;
          }, {});
        } catch (error) {
          console.error('Error al cargar los datos:', error);
        }
      },
      actualizarFiltro(nuevoFiltro) {
        this.filtroActual = nuevoFiltro;
      },
      mostrarTabla(facultadSeleccionada) {
        this.filtroActual = facultadSeleccionada;
        this.nivelActual = 1; // Cambiar al nivel Tabla
      },
      mostrarTablaPorEspecialidad(especialidadSeleccionada) {
        this.filtroActual = especialidadSeleccionada;
        this.nivelActual = 1; // Cambiar al nivel Tabla
      },
      cambiarNivel(index) {
        this.nivelActual = index; // Cambiar al nivel seleccionado
      },
    },
    created() {
      this.cargarDatos();
    },
  };
  </script>
