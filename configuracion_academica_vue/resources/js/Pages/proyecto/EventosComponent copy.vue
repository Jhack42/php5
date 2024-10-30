<template>
    <div class="eventos-component">
      <!-- Barra lateral de filtro -->
      <div class="columns">
        <div class="column is-one-quarter">
          <aside class="menu">
            <p class="menu-label">Filtros</p>
            <ul class="menu-list">
              <li><a @click="aplicarFiltro('Universidad')">Universidad</a></li>
              <li><a @click="aplicarFiltro('Facultad')">Facultad</a></li>
              <li><a @click="aplicarFiltro('Especialidad')">Especialidad</a></li>
            </ul>
          </aside>
        </div>

        <!-- Contenido principal con tabs de vista de Tabla y Calendario -->
        <div class="column">
          <!-- Barra de Tabs para cambiar entre vista de Tabla y Calendario -->
          <div class="tabs is-centered">
            <ul>
              <li :class="{ 'is-active': vistaActiva === 'tabla' }">
                <a @click="cambiarVista('tabla')">Tabla</a>
              </li>
              <li :class="{ 'is-active': vistaActiva === 'calendario' }">
                <a @click="cambiarVista('calendario')">Calendario</a>
              </li>
            </ul>
          </div>

          <!-- Contenido Dinámico según la vista seleccionada -->
          <div v-if="vistaActiva === 'tabla'">
            <!-- Vista de Tabla con datos filtrados -->
            <p>Vista de Tabla con datos filtrados</p>
            <table class="table is-striped">
              <thead>
                <tr>
                  <th>Periodo</th>
                  <th>Cod. Actic</th>
                  <th>Descripción</th>
                  <th>Fecha inicio</th>
                  <th>Fecha fin</th>
                  <th>Estado</th>
                  <th>Jerarquía</th>
                  <th>Opciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="evento in eventosFiltrados" :key="evento.id_actividad">
                  <td>{{ evento.periodo }}</td>
                  <td>{{ evento.codigo_actividad }}</td>
                  <td>{{ evento.fullcalendar.descripcion }}</td>
                  <td>{{ formatoFecha(evento.fullcalendar.fecha_inicio) }}</td>
                  <td>{{ formatoFecha(evento.fullcalendar.fecha_fin) }}</td>
                  <td>{{ evento.estado }}</td>
                  <td>{{ evento.jerarquia.nivel }}</td>
                  <td>
                    <button @click="eliminarEvento(evento.id_actividad)" class="button is-danger is-small">Eliminar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="vistaActiva === 'calendario'">
            <!-- Vista de Calendario con FullCalendar y datos filtrados -->
            <p>Vista de Calendario con FullCalendar y datos filtrados</p>
            <!-- Inserta el componente de FullCalendar aquí y pásale los datos filtrados -->
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  import axios from 'axios';

  export default {
    data() {
      return {
        vistaActiva: 'tabla', // Vista activa inicial: Tabla
        filtroActivo: null,
        eventos: [], // Datos de eventos obtenidos de la API
        eventosFiltrados: [], // Eventos filtrados según el filtro activo
      };
    },
    methods: {
      cambiarVista(vista) {
        this.vistaActiva = vista;
      },
      aplicarFiltro(filtro) {
        this.filtroActivo = filtro;
        // Filtro según el nivel de jerarquía
        this.eventosFiltrados = this.eventos.filter(evento =>
          evento.jerarquia && evento.jerarquia.nivel === filtro
        );
      },
      async cargarDatos() {
        try {
          const response = await axios.get('http://localhost:3000/api/data/para-la-vita1');
          this.eventos = response.data;
          this.eventosFiltrados = this.eventos; // Iniciar con todos los datos
        } catch (error) {
          console.error("Error al cargar los datos:", error);
        }
      },
      formatoFecha(fecha) {
        const date = new Date(fecha);
        return date.toLocaleDateString(); // Cambiar formato si es necesario
      },
      eliminarEvento(id) {
        // Agregar lógica para eliminar evento, por ejemplo, hacer una llamada a la API
        console.log("Eliminar evento con ID:", id);
      },
    },
    mounted() {
      this.cargarDatos();
    }
  };
  </script>

  <style scoped>
  .eventos-component {
    display: flex;
    flex-direction: column;
  }
  </style>
