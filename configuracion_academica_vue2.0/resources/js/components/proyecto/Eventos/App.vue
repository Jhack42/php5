// App.vue (Componente principal)
<template>
  <div>
    <component
      :is="currentComponent"
      @change-view="handleViewChange"
    />
  </div>
</template>

<script>
import { defineAsyncComponent, ref } from 'vue';

export default {
  components: {
    Archivo1: defineAsyncComponent(() => import('./Archivo1.vue')),
    Archivo2: defineAsyncComponent(() => import('./Archivo2.vue')),
    Calendario: defineAsyncComponent(() => import('./Calendario.vue')),
    Noticias: defineAsyncComponent(() => import('../Noticias/CarouselAdmin.vue')),

  },
  setup() {
    const currentComponent = ref('Archivo1');

    const handleViewChange = (viewName) => {
      currentComponent.value = viewName;
    };

    return {
      currentComponent,
      handleViewChange
    };
  }
};
</script>
