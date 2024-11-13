<template>
    <div class="container mt-5">
        <div class="box">
            <!-- Filtro de Precio -->
            <div class="filter-category" @click="toggleSection('price-section', 'price-chevron')">
                <h3 class="is-size-6">Filtrar por Precio
                    <span id="price-chevron" :class="{ 'rotate': isSectionOpen }" class="icon">
                        <i class="fas fa-chevron-down"></i>
                    </span>
                </h3>
            </div>
            <div v-show="isSectionOpen" id="price-section">
                <div class="checkbox-container" v-for="(price, index) in priceRanges" :key="index">
                    <input type="checkbox" :id="'checkbox' + index" v-model="selectedPrices" :value="price.range">
                    <label :for="'checkbox' + index" class="checkbox-custom"></label>
                    <span class="checkbox-label">{{ price.label }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch } from 'vue';

export default defineComponent({
    name: 'FiltroSidebar',
    emits: ['aplicarFiltro'], // Emite un evento para el filtro
    setup(_, { emit }) {
        const isSectionOpen = ref(false);
        const selectedPrices = ref<string[]>([]);

        const toggleSection = () => {
            isSectionOpen.value = !isSectionOpen.value;
        };

        const priceRanges = [
            { label: 'Hasta S/ 50', range: '0-50' },
            { label: 'S/ 50 - S/ 100', range: '50-100' },
            { label: 'S/ 100 - S/ 250', range: '100-250' },
            { label: 'S/ 250 - S/ 500', range: '250-500' }
        ];

        // Observa los cambios en `selectedPrices` y emite el evento
        watch(selectedPrices, (nuevasSelecciones) => {
            emit('aplicarFiltro', { precios: nuevasSelecciones });
        });

        return {
            isSectionOpen,
            selectedPrices,
            toggleSection,
            priceRanges
        };
    }
});
</script>


<style scoped>
.rotate {
    transform: rotate(180deg);
}
.checkbox-container {
    display: flex;
    align-items: center;
    gap: 10px;
}
input[type="checkbox"] {
    display: none;
}
.checkbox-custom {
    display: inline-block;
    width: 20px;
    height: 20px;
    background-color: #f0f0f0;
    border-radius: 5px;
    border: 2px solid #ccc;
    position: relative;
    cursor: pointer;
}
input[type="checkbox"]:checked + .checkbox-custom {
    background-color: #4CAF50;
    border-color: #4CAF50;
}
.checkbox-custom::after {
    content: '';
    position: absolute;
    width: 6px;
    height: 12px;
    border: solid white;
    border-width: 0 2px 2px 0;
    left: 50%;
    top: 50%;
    opacity: 0;
    transform: translate(-50%, -50%) rotate(45deg);
}
input[type="checkbox"]:checked + .checkbox-custom::after {
    opacity: 1;
}
.checkbox-label {
    font-size: 16px;
}
</style>
