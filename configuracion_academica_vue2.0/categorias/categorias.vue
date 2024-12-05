<script>
import CategoriaCrud from './CategoriaCrud.vue' // Asegúrate de que las rutas sean correctas
import SubCategoriaCrud from './SubCategoriaCrud.vue'

export default {
  components: {
    CategoriaCrud,
    SubCategoriaCrud,
  },
  data() {
    return {
      activeTab: 'categoria', // Pestaña activa por defecto
    }
  },
}
</script>

<template>
  <div>
    <!-- Navegación de Pestañas -->
    <div class="tabs">
      <button
        :class="{ active: activeTab === 'categoria' }"
        @click="activeTab = 'categoria'"
      >
        Categoría
      </button>
      <button
        :class="{ active: activeTab === 'subcategoria' }"
        @click="activeTab = 'subcategoria'"
      >
        Sub-Categoría
      </button>
    </div>

    <!-- Contenidos de las Pestañas -->
    <div class="tab-content">
      <CategoriaCrud v-show="activeTab === 'categoria'" />
      <SubCategoriaCrud v-show="activeTab === 'subcategoria'" />
      <SubCategoriaCrudNueva v-show="activeTab === 'subcategoria_nueva'" />
    </div>
  </div>
</template>

<style scoped>
/* Pestañas */
.tabs button {
  border: none;
  border-radius: 4px;
  background-color: rgb(var(--v-theme-surface)); /* Fondo dinámico */
  color: rgb(var(--v-theme-on-surface)); /* Texto dinámico */
  cursor: pointer;
  padding-block: 10px;
  padding-inline: 20px;
  transition: background-color 0.3s, color 0.3s;
}

.tabs button.active {
  border-block-end: 2px solid rgb(var(--v-theme-primary)); /* Color del tema activo */
  font-weight: bold;
}

/* Contenedor de contenido */
.tab-content {
  padding: 20px;
  border: 1px solid rgb(var(--v-theme-outline)); /* Borde dinámico */
  border-radius: 4px;
  background-color: rgb(var(--v-theme-surface)); /* Fondo dinámico */
  color: rgb(var(--v-theme-on-surface)); /* Texto dinámico */
  transition: background-color 0.3s, color 0.3s;
}
</style>
