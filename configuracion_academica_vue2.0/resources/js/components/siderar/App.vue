<template>
    <div id="app" class="app-container">
      <!-- Barra de Navegación Lateral -->
      <div class="sidebar">
        <ul class="sidebar__menu">
          <li :class="{ 'sidebar__item--active': activeTab === 'home' }" class="sidebar__item">
            <a @click="setActiveTab('home')" class="sidebar__link">Home</a>
          </li>
          <li :class="{ 'sidebar__item--active': activeTab === 'about' }" class="sidebar__item">
            <a @click="setActiveTab('about')" class="sidebar__link">About</a>
          </li>
          <li :class="{ 'sidebar__item--active': activeTab === 'contact' }" class="sidebar__item">
            <a @click="setActiveTab('contact')" class="sidebar__link">Contact</a>
          </li>
        </ul>
      </div>

      <!-- Contenido Dinámico -->
      <div class="content-container">
        <div v-if="activeTab === 'home'">
          <Home />
        </div>
        <div v-if="activeTab === 'about'">
          <About />
        </div>
        <div v-if="activeTab === 'contact'">
          <Contact />
        </div>
      </div>
    </div>
  </template>


  <script>
  import Home from './views/Home.vue';
  import About from './views/About.vue';
  import Contact from './views/Contact.vue';

  export default {
    name: 'App',
    components: {
      Home,
      About,
      Contact
    },
    data() {
      return {
        activeTab: 'home',  // Tab activa por defecto
      };
    },
    methods: {
      setActiveTab(tab) {
        this.activeTab = tab;  // Cambia la pestaña activa
      }
    }
  };
  </script>

  <style>
  /* Barra de navegación */
.app-container {
  display: flex;
}

.sidebar {
  position: fixed;
  width: 200px;
  height: 100%;
  background-color: #333;
  color: white;
  padding-top: 20px;
}

.sidebar__menu {
  list-style: none;
  padding: 0;
}

.sidebar__item {
  padding: 10px;
}

.sidebar__link {
  color: white;
  text-decoration: none;
}

.sidebar__link:hover {
  background-color: #575757;
}

/* Estilo para la pestaña activa */
.sidebar__item--active .sidebar__link {
  background-color: #575757;
}

.content-container {
  margin-left: 200px;
  padding: 20px;
  flex-grow: 1;
}

  </style>
