<template>
    <div>
        <!-- Renderizar el componente -->
        <Calendario />
    </div>
</template>

<script>
import { defineAsyncComponent } from 'vue';

export default {
    components: {
        Calendario: defineAsyncComponent(() =>
            import('./siderar/App.vue')
        ),
    },
};
</script>
