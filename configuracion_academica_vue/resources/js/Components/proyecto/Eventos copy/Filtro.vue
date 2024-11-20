<template>
    <div class="filtro-container">
      <!-- Filtro por categoría -->
      <label for="categoria-filtro">Filtrar por categoría:</label>
      <select id="categoria-filtro" v-model="filtro.categoria" @change="emitirFiltro">
        <option value="">Todos</option>
        <option v-for="categoria in categoriasUnicas" :key="categoria" :value="categoria">
          {{ categoria }}
        </option>
      </select>

      <!-- Filtro por nombre -->
      <label for="nombre-filtro">Filtrar por nombre:</label>
      <input
        id="nombre-filtro"
        type="text"
        v-model="filtro.nombre"
        @input="emitirFiltro"
        placeholder="Buscar por nombre"
      />

      <!-- Botón para limpiar filtros -->
      <button @click="limpiarFiltros">Limpiar Filtros</button>
    </div>
  </template>

  <script>
  export default {
    props: {
      datos: {
        type: Array,
        required: true,
      },
    },
    data() {
      return {
        filtro: {
          categoria: '',
          nombre: '',
        },
      };
    },
    computed: {
      categoriasUnicas() {
        // Extraer categorías únicas desde los datos
        const categorias = this.datos.map((dato) => dato.categoria);
        return [...new Set(categorias)]; // Devuelve solo categorías únicas
      },
    },
    methods: {
      emitirFiltro() {
        this.$emit('aplicar-filtro', this.filtro);
      },
      limpiarFiltros() {
        this.filtro = {
          categoria: '',
          nombre: '',
        };
        this.emitirFiltro();
      },
    },
  };
  </script>

  <style>
  .filtro-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
  }

  label {
    font-weight: bold;
    margin-bottom: 5px;
  }

  input, select {
    padding: 5px;
    font-size: 14px;
  }

  button {
    padding: 5px 10px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  button:hover {
    background-color: #0056b3;
  }
  </style>
