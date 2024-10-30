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
        </div>
    </div>
</template>

<script>
import EventosComponent from './proyecto/EventosComponent.vue';
import CorreosComponent from './proyecto/CorreosComponent.vue';

export default {
  components: {
    EventosComponent,
    CorreosComponent,
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
