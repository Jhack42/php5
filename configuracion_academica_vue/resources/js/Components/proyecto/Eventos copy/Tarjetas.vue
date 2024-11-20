<template>
    <div class="tarjetas">
      <div
        v-for="categoria in categorias"
        :key="categoria"
        class="tarjeta"
        @click="seleccionarTarjeta(categoria)"
      >
        {{ categoria }}
      </div>
    </div>
  </template>

  <script>
  export default {
    data() {
      return {
        categorias: ['Electrónica', 'Ropa', 'Juguetes'], // Categorías de ejemplo
      };
    },
    methods: {
      seleccionarTarjeta(categoria) {
        this.$emit('seleccionar-tarjeta', categoria); // Emitir el filtro seleccionado
      },
    },
  };
  </script>

  <style>
  .tarjeta {
    cursor: pointer;
    padding: 20px;
    border: 1px solid #ccc;
    margin: 10px;
    display: inline-block;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  .tarjeta:hover {
    background-color: #f0f0f0;
  }
  </style>
