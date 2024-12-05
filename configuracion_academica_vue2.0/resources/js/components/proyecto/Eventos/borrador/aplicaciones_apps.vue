<template>
    <div class="app-container">
      <!-- Botón del menú -->
      <button class="app-button" @click="toggleMenu">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon-grid" viewBox="0 0 24 24">
          <rect x="3" y="3" width="6" height="6" rx="1"></rect>
          <rect x="15" y="3" width="6" height="6" rx="1"></rect>
          <rect x="3" y="15" width="6" height="6" rx="1"></rect>
          <rect x="15" y="15" width="6" height="6" rx="1"></rect>
        </svg>
      </button>

      <!-- Menú desplegable -->
      <div v-if="menuOpen" class="dropdown-menu">
        <div class="app-item" v-for="app in apps" :key="app.name">
          <div class="app-content">
            <img :src="app.icon" alt="app icon" class="app-icon" />
            <p class="app-label">{{ app.name }}</p>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    name: "ChromeAppMenu",
    data() {
      return {
        menuOpen: false, // Estado para mostrar/ocultar el menú
        apps: [
          { name: "Cuenta", icon: "https://static.mercadonegro.pe/wp-content/uploads/2017/10/27160609/22815135_916150281870460_820360796721820140_n.jpg" },
          { name: "Búsqueda", icon: "https://static.mercadonegro.pe/wp-content/uploads/2017/10/27160609/22815135_916150281870460_820360796721820140_n.jpg" },
          { name: "Maps", icon: "https://static.mercadonegro.pe/wp-content/uploads/2017/10/27160609/22815135_916150281870460_820360796721820140_n.jpg" },
          { name: "YouTube", icon: "https://static.mercadonegro.pe/wp-content/uploads/2017/10/27160609/22815135_916150281870460_820360796721820140_n.jpg" },
          { name: "Gmail", icon: "https://static.mercadonegro.pe/wp-content/uploads/2017/10/27160609/22815135_916150281870460_820360796721820140_n.jpg" },
          { name: "Drive", icon: "https://static.mercadonegro.pe/wp-content/uploads/2017/10/27160609/22815135_916150281870460_820360796721820140_n.jpg" },
          { name: "Calendar", icon: "https://static.mercadonegro.pe/wp-content/uploads/2017/10/27160609/22815135_916150281870460_820360796721820140_n.jpg" },
        ],
      };
    },
    methods: {
      toggleMenu() {
        this.menuOpen = !this.menuOpen; // Alterna entre abierto/cerrado
      },
    },
  };
  </script>

  <style scoped>
  /* Contenedor del botón */
  .app-container {
    position: relative;
    display: inline-block;
  }

  /* Botón del menú */
  .app-button {
    background-color: transparent;
    border: none;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: background-color 0.2s ease;
  }

  .app-button:hover {
    background-color: rgba(0, 0, 0, 0.1);
  }

  /* Icono de cuadrícula */
  .icon-grid {
    width: 24px;
    height: 24px;
    fill: #333;
  }

  /* Menú desplegable */
  .dropdown-menu {
    position: absolute;
    top: 40px;
    right: 0;
    background-color: #222;
    color: white;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    width: 260px; /* Ajuste de ancho para columnas simétricas */
    padding: 15px;
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* Tres columnas uniformes */
    gap: 0px; /* Espacio uniforme entre íconos */
    z-index: 1000;
  }

  /* Contenedor individual del ícono y texto */
  .app-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 10px;
    border-radius: 10px;
    transition: background-color 0.2s ease;
    cursor: pointer;
  }

  .app-content:hover {
    background-color: rgba(255, 255, 255, 0.1); /* Color al pasar el cursor */
  }

  /* Estilo del ícono */
  .app-icon {
    width: 50px;
    height: 50px;
    margin-bottom: 10px; /* Espacio entre el ícono y el texto */
    border-radius: 8px; /* Bordes redondeados para los íconos */
  }

  /* Estilo del texto */
  .app-label {
    font-size: 14px;
    font-weight: 500;
    margin: 0;
  }
  </style>
