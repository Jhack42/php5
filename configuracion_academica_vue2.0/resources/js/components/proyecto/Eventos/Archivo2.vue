// Archivo2.vue
<template>
  <div class="archivo2-container">
    <!-- Importar el componente Apps -->
    <Apps @change-view="handleViewChange" />

    <div class="content">
      <h2>Contenido del Archivo 2</h2>
      <!-- Tu contenido aquí -->
    </div>
  </div>
</template>

<script>
import Apps from './Apps.vue';

export default {
  name: 'Archivo2',
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
