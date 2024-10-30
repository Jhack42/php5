<template>
    <div class="flex flex-col h-screen w-screen">
        <nav class="bg-gray-800 text-white text-center flex items-center justify-center" style="height: 30px;">
            <h1 class="text-sm">FACULTADES</h1>
        </nav>

        <!-- Contenedor principal que contiene la barra lateral y el contenido -->
        <div class="flex-1 flex bg-blue-200 no-bg">
            <!-- Barra lateral de filtro -->
            <div class="bg-gray-100 p-4" style="width: 250px;">
                <p>Barra lateral de filtro</p>
            </div>

            <!-- Contenedor de contenido principal que ocupa el espacio restante -->
            <div class="flex-1 bg-white p-4">
                <!-- Renderizado del componente dinámico -->
                <component :is="componenteCargado" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, shallowRef, onMounted, markRaw, DefineComponent } from 'vue';
import { obtenerVista } from './contenedor';

export default defineComponent({
    name: 'Blank',
    setup() {
        // Definimos el tipo del ref como `DefineComponent | null` para evitar conflictos de tipos
        const componenteCargado = shallowRef<DefineComponent | null>(null);

        // Configura la vista predeterminada en el montaje
        onMounted(() => {
            componenteCargado.value = markRaw(obtenerVista(true)) as DefineComponent;
        });

        return {
            componenteCargado
        };
    }
});
</script>

<style scoped>
.no-bg {
    background-color: transparent !important;
}
</style>
