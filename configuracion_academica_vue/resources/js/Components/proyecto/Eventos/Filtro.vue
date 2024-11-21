<template>
    <div>
      <select v-model="filtro" @change="emitirFiltro">
        <option value="">Todos</option>
        <option v-for="opcion in opcionesFiltro" :key="opcion" :value="opcion">
          {{ opcion }}
        </option>
      </select>
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
        filtro: '',
      };
    },
    computed: {
      opcionesFiltro() {
        // Extrae opciones únicas basadas en jerarquia.nivel o cualquier campo relevante
        const niveles = this.datos
          .map((dato) => dato.jerarquia?.nivel)
          .filter((nivel) => nivel); // Elimina valores nulos o indefinidos
        return [...new Set(niveles)]; // Devuelve opciones únicas
      },
    },
    methods: {
      emitirFiltro() {
        this.$emit('aplicar-filtro', this.filtro);
      },
    },
  };
  </script>
