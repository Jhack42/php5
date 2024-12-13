<template>
    <theme-detector>
        <div>
            <Calendario />
        </div>
    </theme-detector>
</template>

<script>
import { defineAsyncComponent } from 'vue';
import ThemeDetector from './ThemeDetector.vue';  // Añade esta línea

export default {
    components: {
        ThemeDetector,  // Añade esta línea
        Calendario: defineAsyncComponent(() =>
            import('../components/proyecto/Eventos/Calendario.vue')
        ),
    },
};
</script>
<style>
.mi-componente {
  background-color: var(--background-color);
  color: var(--text-color);
}
</style>
