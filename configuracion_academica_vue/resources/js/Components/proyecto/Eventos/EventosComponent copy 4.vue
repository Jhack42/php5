<template>
    <div>
      <Filtro @aplicar-filtro="actualizarFiltro" />
      <Tabla :datos="datosFiltrados" />
    </div>
  </template>

  <script>
  import Filtro from './Filtro.vue';
  import Tabla from './Tabla.vue';

  export default {
    components: { Filtro, Tabla },
    data() {
      return {
        datosOriginales: [
          { id: 1, nombre: 'Producto A', categoria: 'Electrónica' },
          { id: 2, nombre: 'Producto B', categoria: 'Ropa' },
          { id: 3, nombre: 'Producto C', categoria: 'Electrónica' },
        ],
        filtroActual: '', // Este será el filtro activo
      };
    },
    computed: {
      datosFiltrados() {
        if (!this.filtroActual) {
          return this.datosOriginales;
        }
        return this.datosOriginales.filter(dato =>
          dato.categoria.includes(this.filtroActual)
        );
      },
    },
    methods: {
      actualizarFiltro(nuevoFiltro) {
        this.filtroActual = nuevoFiltro; // Actualiza el filtro según lo recibido del componente Filtro
      },
    },
  };
  </script>
