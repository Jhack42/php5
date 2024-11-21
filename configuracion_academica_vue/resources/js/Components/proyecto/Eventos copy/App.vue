<template>
    <div>
      <!-- Breadcrumb -->
      <Breadcrumb
        :niveles="niveles"
        @navegar="cambiarNivel"
      />

      <!-- Contenido según el nivel -->
      <div v-if="nivelActual === 0">
        <Tarjetas @seleccionar-tarjeta="mostrarTabla" />
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

  export default {
    components: { Breadcrumb, Tarjetas, Filtro, Tabla },
    data() {
      return {
        niveles: ['Tarjetas', 'Tabla'], // Definición de los niveles
        nivelActual: 0, // Nivel inicial (0 = Tarjetas)
        datosOriginales: [
          { id: 1, nombre: 'Producto A', categoria: 'Electrónica' },
          { id: 2, nombre: 'Producto B', categoria: 'Ropa' },
          { id: 3, nombre: 'Producto C', categoria: 'Electrónica' },
          { id: 4, nombre: 'Producto D', categoria: 'Juguetes' },
        ],
        filtroActual: {
          categoria: '',
          nombre: '',
        }, // Filtros activos
      };
    },
    computed: {
      datosFiltrados() {
        // Filtrar por categoría y nombre
        return this.datosOriginales.filter((dato) => {
          const coincideCategoria = this.filtroActual.categoria
            ? dato.categoria.includes(this.filtroActual.categoria)
            : true;
          const coincideNombre = this.filtroActual.nombre
            ? dato.nombre.toLowerCase().includes(this.filtroActual.nombre.toLowerCase())
            : true;
          return coincideCategoria && coincideNombre;
        });
      },
    },
    methods: {
      actualizarFiltro(nuevoFiltro) {
        this.filtroActual = nuevoFiltro;
      },
      mostrarTabla(filtroSeleccionado) {
        this.filtroActual.categoria = filtroSeleccionado;
        this.nivelActual = 1; // Cambiar al nivel Tabla
      },
      cambiarNivel(index) {
        this.nivelActual = index; // Cambiar al nivel seleccionado
      },
    },
  };
  </script>
