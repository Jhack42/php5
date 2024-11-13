<template>
    <div class="flex flex-col h-screen w-screen">
        <nav class="bg-gray-800 text-white text-center flex items-center justify-center" style="height: 30px;">
            <h1 class="text-sm">FACULTADES</h1>
        </nav>

        <div class="flex-1 flex bg-blue-200 no-bg">
            <div class="bg-gray-100 p-4" style="width: 250px;">
                <FiltroSidebar @aplicarFiltro="aplicarFiltro" /> <!-- Escucha el evento -->
            </div>

            <div class="flex-1 bg-white p-4">
                <component :is="componenteCargado" :dataFiltrada="dataFiltrada" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, shallowRef, ref, onMounted, markRaw, DefineComponent } from 'vue';
import { obtenerVista } from './contenedor';
import FiltroSidebar from './FiltroSidebar.vue';
import axios from 'axios';

export default defineComponent({
    name: 'Blank',
    components: {
        FiltroSidebar
    },
    setup() {
        const componenteCargado = shallowRef<DefineComponent | null>(null);
        const datos = ref([]);
        const dataFiltrada = ref([]);

        onMounted(async () => {
            componenteCargado.value = markRaw(obtenerVista(true)) as DefineComponent;
            // Cargar datos de la API
            const respuesta = await axios.get('//localhost:3000/api/data/para-la-vita1');
            datos.value = respuesta.data;
            dataFiltrada.value = datos.value; // Inicialmente, todos los datos
        });

        const aplicarFiltro = (filtros: { precios: string[] }) => {
            // Filtra `datos.value` usando `filtros` y guarda en `dataFiltrada`
            dataFiltrada.value = datos.value.filter((actividad: any) => {
                // Aquí puedes personalizar los filtros; en este ejemplo filtramos solo por precio
                return filtros.precios.length === 0 || filtros.precios.includes(actividad.precio);
            });
        };

        return {
            componenteCargado,
            dataFiltrada,
            aplicarFiltro
        };
    }
});
</script>
