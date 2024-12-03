<template>
  <div>
    <!-- Navegación de Pestañas -->
    <div class="tabs">
      <button @click="activeTab = 'categoria'" :class="{ active: activeTab === 'categoria' }">Categoría</button>
      <button @click="activeTab = 'subcategoria'" :class="{ active: activeTab === 'subcategoria' }">Sub-Categoría</button>
    </div>

    <!-- Contenidos de las Pestañas -->
    <div class="tab-content">
      <CategoriaCrud v-show="activeTab === 'categoria'" />
      <SubCategoriaCrud v-show="activeTab === 'subcategoria'" />
    </div>
  </div>
</template>

<script>

import CategoriaCrud from './CategoriaCrud.vue'; // Asegúrate de que las rutas sean correctas
import SubCategoriaCrud from './SubCategoriaCrud.vue';

export default {
  components: {
    CategoriaCrud,
    SubCategoriaCrud
  },
  data() {
    return {
      activeTab: 'categoria' // Pestaña activa por defecto
    };
  }
}
</script>

<style scoped>
.tabs button {
  background-color: #fff;
  border: none;
  padding: 10px 20px;
  cursor: pointer;
}
.tabs button.active {
  border-bottom: 2px solid red;
}
.tab-content {
  padding: 20px;
  border: 1px solid #ccc;
}
</style>
