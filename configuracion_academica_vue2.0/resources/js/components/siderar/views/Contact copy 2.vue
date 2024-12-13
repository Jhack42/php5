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
            import('../../proyecto/Eventos/Calendario.vue')
        ),
    },
};
</script>
