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
      <div v-if="localShowPopup" class="popup-overlay" @click.self="closePopup">
        <div
          class="popup-content"
          :style="popupStyle"
          :class="popupPositionClass"
          v-click-outside="closePopup"
          ref="popupContent"
        >
          <div class="apps-grid">
            <div class="app-item" @click="changeView('Archivo1')">
              <img src="../../../../../public/img/sitio-web.png" alt="Vista 1" class="app-icon">
              <span>Vista 1</span>
            </div>
            <div class="app-item" @click="changeView('Archivo2')">
              <img src="../../../../../public/img/formularios-de-google.png" alt="Vista 2" class="app-icon">
              <span>Vista 2</span>
            </div>
            <div class="app-item" @click="changeView('Calendario')">
              <img src="../../../../../public/img/calendario.png" alt="Vista 3" class="app-icon">
              <span>Calendario</span>
            </div>
            <div class="app-item" @click="changeView('Noticias')">
              <img src="../../../../../public/img/informe-de-noticias.png" alt="Vista 4" class="app-icon">
              <span>Noticias</span>
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
        localShowPopup: false,
        popupStyle: {},
        popupPositionClass: ''
      }
    },
    directives: {
      clickOutside: {
        mounted(el, binding) {
          el.clickOutsideEvent = (event) => {
            const menuButton = document.querySelector('.cal-header-left2');
            if (menuButton && (menuButton === event.target || menuButton.contains(event.target))) {
              return;
            }
            if (!(el === event.target || el.contains(event.target))) {
              binding.value();
            }
          };
          setTimeout(() => {
            document.addEventListener('click', el.clickOutsideEvent);
          }, 0);
        },
        unmounted(el) {
          document.removeEventListener('click', el.clickOutsideEvent);
        },
      }
    },
    methods: {
      togglePopup() {
        this.localShowPopup = !this.localShowPopup;
        if (this.localShowPopup) {
          this.$nextTick(() => {
            this.positionPopup();
          });
        }
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
      positionPopup() {
        const button = this.$refs.menuButton;
        const popup = this.$refs.popupContent;
        if (!button || !popup) return;

        const buttonRect = button.getBoundingClientRect();
        const popupRect = popup.getBoundingClientRect();
        const windowWidth = window.innerWidth;
        const windowHeight = window.innerHeight;

        let left = buttonRect.left;
        let top = buttonRect.bottom + 10;

        // Ajustar posición horizontal
        if (left + popupRect.width > windowWidth - 20) {
          left = windowWidth - popupRect.width - 20;
          this.popupPositionClass = 'position-right';
        } else if (left < 20) {
          left = 20;
          this.popupPositionClass = 'position-left';
        } else {
          this.popupPositionClass = 'position-center';
        }

        // Ajustar posición vertical
        if (top + popupRect.height > windowHeight - 20) {
          top = buttonRect.top - popupRect.height - 10;
        }

        this.popupStyle = {
          position: 'fixed',
          left: `${left}px`,
          top: `${top}px`
        };
      },
      handleResize() {
        if (this.localShowPopup) {
          this.positionPopup();
        }
      }
    },
    mounted() {
      window.addEventListener('resize', this.handleResize);
    },
    beforeUnmount() {
      window.removeEventListener('resize', this.handleResize);
      document.removeEventListener('click', this.handleClickOutside);
    }
  }
  </script>
<style lang="scss" src="../../../../scss/Apps.scss"></style>
