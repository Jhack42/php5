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
        Calendario: defineAsyncComponent(() =>
            import('./siderar/App.vue')
        ),
    },
};

</script>
