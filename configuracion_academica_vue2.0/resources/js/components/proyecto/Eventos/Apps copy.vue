// Apps.vue
<template>
    <div class="menu-container">
      <!-- Botón para cambiar vista -->
      <div class="cal-header-left2" @click="togglePopup" ref="menuButton">
        <div class="app-launcher-icon">
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
        </div>
      </div>

      <!-- Popup para cambiar vistas -->
      <div v-if="localShowPopup"
           class="popup-overlay"
           @click.self="closePopup">
        <div class="popup-content" v-click-outside="closePopup">
          <div class="apps-grid">
            <div class="app-item" @click="changeView('Archivo1')">
              <img src="../../../../../public/img/calendario.png" alt="Vista 1" class="app-icon">
              <span>Vista 1</span>
            </div>
            <div class="app-item" @click="changeView('Archivo2')">
              <img src="../../../../../public/img/calendario.png" alt="Vista 2" class="app-icon">
              <span>Vista 2</span>
            </div>
            <div class="app-item" @click="changeView('Calendario')">
              <img src="../../../../../public/img/calendario.png" alt="Vista 3" class="app-icon">
              <span>Calendario</span>
            </div>

            <!-- Elementos deshabilitados -->
            <div v-for="n in 7" :key="n" class="app-item disabled">
              <img src="../../../../../public/img/trabajo-en-progreso.png" alt="Próximo" class="app-icon">
              <span>Próximo</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>

<script>
export default {
  name: 'Apps',
  emits: ['change-view'],
  data() {
    return {
      localShowPopup: false
    }
  },
  directives: {
    clickOutside: {
      mounted(el, binding) {
        el.clickOutsideEvent = (event) => {
          // Excluye el botón que abre el popup y sus elementos hijos
          const menuButton = document.querySelector('.cal-header-left2');
          if (menuButton && (menuButton === event.target || menuButton.contains(event.target))) {
            return;
          }
          // Si el clic no es en el elemento ni en sus hijos, ejecuta el método
          if (!(el === event.target || el.contains(event.target))) {
            binding.value();
          }
        };
        // Añade el event listener con un pequeño retraso para evitar que se active inmediatamente
        setTimeout(() => {
          document.addEventListener('click', el.clickOutsideEvent);
        }, 0);
      },
      unmounted(el) {
        // Limpia el event listener cuando el componente se desmonta
        document.removeEventListener('click', el.clickOutsideEvent);
      },
    }
  },
  methods: {
    togglePopup() {
      this.localShowPopup = !this.localShowPopup;
    },
    closePopup() {
      this.localShowPopup = false;
    },
    changeView(viewName) {
      try {
        this.$emit('change-view', viewName);
        this.localShowPopup = false;
      } catch (error) {
        console.error('Error al cambiar de vista:', error);
      }
    },
    // Método para manejar clics fuera del popup
    handleClickOutside(event) {
      if (this.localShowPopup) {
        const popupContent = this.$el.querySelector('.popup-content');
        const menuButton = this.$el.querySelector('.cal-header-left2');

        if (popupContent && !popupContent.contains(event.target) &&
            menuButton && !menuButton.contains(event.target)) {
          this.closePopup();
        }
      }
    }
  },
  mounted() {
    // Añade event listener para clicks en el documento
    document.addEventListener('click', this.handleClickOutside);
  },
  beforeUnmount() {
    // Limpia el event listener cuando el componente se desmonta
    document.removeEventListener('click', this.handleClickOutside);
  }
}
</script>
<style lang="scss" src="../../../../scss/Apps.scss"></style>
