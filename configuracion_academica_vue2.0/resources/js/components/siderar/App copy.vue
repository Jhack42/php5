<template>
    <div id="app" class="app-container">
      <!-- Barra de Navegación Lateral -->
      <div class="sidebar">
        <ul>
          <li :class="{ 'active': activeTab === 'home' }">
            <a @click="setActiveTab('home')">Home</a>
          </li>
          <li :class="{ 'active': activeTab === 'about' }">
            <a @click="setActiveTab('about')">About</a>
          </li>
          <li :class="{ 'active': activeTab === 'contact' }">
            <a @click="setActiveTab('contact')">Contact</a>
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

  .sidebar ul {
    list-style: none;
    padding: 0;
  }

  .sidebar li {
    padding: 10px;
  }

  .sidebar a {
    color: white;
    text-decoration: none;
  }

  .sidebar a:hover {
    background-color: #575757;
  }

  .content-container {
    margin-left: 200px;
    padding: 20px;
    flex-grow: 1;
  }

  .active a {
    background-color: #575757;
  }
  </style>
