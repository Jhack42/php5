<template>
    <div class="main-container">
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
                <li :class="{ 'is-active': activeTab === 'omesa' }">
                    <a @click="setActiveTab('omesa')">Crud-omesa</a>
                </li>
                <li :class="{ 'is-active': activeTab === 'omesa_test' }">
                    <a @click="setActiveTab('omesa_test')">omesa_test</a>
                </li>
            </ul>
        </div>

        <!-- Contenido dinámico -->
        <div class="content">
            <!-- Error Popup -->
            <div v-if="error" class="notification is-danger">
                <button class="delete" @click="clearError"></button>
                <strong>Error:</strong> {{ errorMessage }}
            </div>

            <div v-if="activeTab === 'events'" class="content-area">
                <EventosComponent v-if="!error" />
            </div>
            <div v-if="activeTab === 'email'" class="content-area">
                <CorreosComponent v-if="!error" />
            </div>
            <div v-if="activeTab === 'vista2'" class="content-area">
                <Vista2 v-if="!error" />
            </div>
            <div v-if="activeTab === 'omesa'" class="content-area">
                <omesa v-if="!error" />
            </div>
            <div v-if="activeTab === 'omesa_test'" class="content-area">
                <omesa_test v-if="!error" />
            </div>
        </div>
    </div>
</template>

<script>
import EventosComponent from './proyecto/Eventos/EventosComponent.vue';
import CorreosComponent from './proyecto/Correos/CorreosComponent.vue';
import Vista2 from './proyecto/Correos/Vista2.vue';
import omesa from './proyecto/omesa/TestCrud.vue';
import omesa_test from './proyecto/omesa/test/test.vue';

export default {
    components: {
        EventosComponent,
        CorreosComponent,
        Vista2,
        omesa,
        omesa_test,
    },
    data() {
        return {
            activeTab: 'events', // Pestaña activa por defecto
            error: false, // Propiedad para controlar errores
            errorMessage: '', // Mensaje de error
        };
    },
    methods: {
        setActiveTab(tab) {
            this.activeTab = tab;
            this.loadView(tab); // Intenta cargar la vista al cambiar de pestaña
        },
        loadView(tab) {
            try {
                // Aquí puedes añadir lógica para cargar datos o simular un retraso de carga
                if (Math.random() < 0.2) {
                    throw new Error(`Hubo un problema al cargar la vista de ${tab}.`);
                }
                this.error = false;
                this.errorMessage = '';
            } catch (err) {
                this.error = true;
                this.errorMessage = err.message || 'Error desconocido';
            }
        },
        clearError() {
            this.error = false;
            this.errorMessage = '';
        },
    },
    watch: {
        activeTab(newTab) {
            this.loadView(newTab); // Intentar cargar la vista al cambiar de pestaña
        },
    },
};
</script>

<style scoped>
@import url('https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css');

.main-container {
    display: flex;
    flex-direction: column;
    height: 100vh;
    /* Ocupa toda la pantalla */
}

.tabs {
    border-bottom: 1px solid #ddd;
    margin-bottom: 0;
}

.content {
    flex: 1;
    /* Hace que el contenido ocupe todo el espacio disponible */
    display: flex;
    flex-direction: column;
    padding: 0;
    overflow-y: auto;
}

.content-area {
    flex: 1;

    display: flex;

}

.notification.is-danger {
    margin-bottom: 20px;
}

body,
html {
    margin: 0;
    padding: 0;
    height: 100%;
}
</style>
