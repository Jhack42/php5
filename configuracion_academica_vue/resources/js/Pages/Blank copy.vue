<template>
    <div>
        <!-- Barra de pestañas -->
        <div class="tabs is-centered">
            <ul>
                <li :class="{ 'is-active': activeTab === 'events' }">
                    <a @click="setActiveTab('events')">Eventos</a>
                </li>
                <li :class="{ 'is-active': activeTab === 'email' }">
                    <a @click="setActiveTab('email')">Correo</a>
                </li>
                <li :class="{ 'is-active': activeTab === 'vista2' }">
                    <a @click="setActiveTab('vista2')">Vista2</a>
                </li>
            </ul>
        </div>

        <!-- Contenido dinámico -->
        <div class="content">
            <div v-if="activeTab === 'events'">
                <EventosComponent />
            </div>
            <div v-if="activeTab === 'email'">
                <CorreosComponent />
            </div>
            <div v-if="activeTab === 'vista2'">
                <Vista2 /> <!-- Cambiado de CorreosComponent a Vista2 -->
            </div>
        </div>
    </div>
</template>

<script>
import EventosComponent from '../Components/proyecto/Eventos/EventosComponent.vue';
import CorreosComponent from '../Components/proyecto/Correos/CorreosComponent.vue';
import Vista2 from '../Components/proyecto/Correos/Vista2.vue'; // Cambiado 'vista2' a 'Vista2'

export default {
    components: {
        EventosComponent,
        CorreosComponent,
        Vista2, // Asegúrate de usar el nombre correcto aquí
    },
    data() {
        return {
            activeTab: 'events', // Pestaña activa por defecto
        };
    },
    methods: {
        setActiveTab(tab) {
            this.activeTab = tab;
        },
    },
};
</script>

<style scoped>
@import url('https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css');

.content {
    padding: 1.5rem;
    border-top: 1px solid #ddd;
}
</style>
