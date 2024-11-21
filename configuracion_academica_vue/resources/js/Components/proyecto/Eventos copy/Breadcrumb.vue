<template>
    <nav class="breadcrumb">
      <ul>
        <li v-for="(nivel, index) in niveles" :key="index">
          <button
            @click="navegar(index)"
            :disabled="index === niveles.length - 1">
            {{ nivel }}
          </button>
        </li>
      </ul>
    </nav>
  </template>

  <script>
  export default {
    props: {
      niveles: {
        type: Array,
        required: true, // Array con las migajas (p. ej., ['Inicio', 'Tarjetas', 'Tabla'])
      },
    },
    methods: {
      navegar(index) {
        this.$emit('navegar', index); // Emitir el nivel al que se desea regresar
      },
    },
  };
  </script>

  <style>
  .breadcrumb {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
  }
  .breadcrumb ul {
    list-style: none;
    padding: 0;
    display: flex;
    gap: 8px;
  }
  .breadcrumb button {
    background: none;
    border: none;
    color: #007bff;
    cursor: pointer;
  }
  .breadcrumb button:disabled {
    color: #6c757d;
    cursor: default;
  }
  </style>
