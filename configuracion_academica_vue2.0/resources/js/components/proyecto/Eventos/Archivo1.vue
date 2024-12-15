// Archivo1.vue
<template>
  <div class="archivo1-container">
    <!-- Importar el componente Apps -->
    <Apps @change-view="handleViewChange" />

    <div class="content">
      <h2>Contenido del Archivo 1</h2>
      <!-- Tu contenido aquí -->
    </div>
    <div class="bg-blue-500 text-white p-4">
    Si este texto aparece con fondo azul y padding, Tailwind está funcionando
</div>
  </div>
</template>

<script>
import Apps from './Apps.vue';

export default {
  name: 'Archivo1',
  components: {
    Apps
  },
  methods: {
    handleViewChange(viewName) {
      this.$emit('change-view', viewName);
    }
  }
}
</script>
