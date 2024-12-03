<template>
    <nav class="breadcrumb">
      <ul>
        <li
          v-for="(nivel, index) in niveles"
          :key="index"
          :class="{ active: index === nivelActual }"
        >
          <button @click="navegar(index)" :disabled="index === nivelActual">
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
        required: true,
      },
      nivelActual: {
        type: Number,
        required: true,
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
    gap: 10px;
    margin-bottom: 20px;
  }
  .breadcrumb ul {
    list-style: none;
    display: flex;
    gap: 10px;
    padding: 0;
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
  .breadcrumb .active button {
    font-weight: bold;
    text-decoration: underline;
  }
  </style>
