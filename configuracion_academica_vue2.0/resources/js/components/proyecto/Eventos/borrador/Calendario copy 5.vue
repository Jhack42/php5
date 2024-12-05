<template>
    <div class="calendar-page">
      <!-- Barra personalizada -->
      <div class="calendar-toolbar">
        <div class="custom-toolbar">
          <button @click="prev" class="button">Anterior</button>
          <span class="calendar-title">{{ calendarTitle }}</span>
          <button @click="next" class="button">Siguiente</button>
          <button @click="today" class="button">Hoy</button>
          <select @change="changeView($event)">
            <option value="dayGridMonth">Mes</option>
            <option value="timeGridWeek">Semana</option>
            <option value="timeGridDay">Día</option>
            <option value="listWeek">Lista</option>
          </select>
          <button @click="extraAction" class="button">Botón Extra</button>
        </div>
      </div>

      <!-- Contenedor principal -->
      <div class="calendar-container">
        <!-- Barra lateral de filtros -->
        <div class="sidebar">
          <h3>Filtros</h3>
          <label>
            <input type="checkbox" v-model="filters.events1" /> Evento 1
          </label>
          <label>
            <input type="checkbox" v-model="filters.events2" /> Evento 2
          </label>
          <label>
            <input type="checkbox" v-model="filters.events3" /> Evento 3
          </label>
          <button @click="applyFilters">Aplicar Filtros</button>
        </div>

        <!-- Tabla del calendario -->
        <div class="calendar-table">
          <FullCalendar :options="calendarOptions" ref="calendar" />
        </div>
      </div>
    </div>
  </template>
<script>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import esLocale from "@fullcalendar/core/locales/es";

export default {
  components: {
    FullCalendar,
  },
  data() {
    return {
      calendarTitle: "",
      filters: {
        events1: true,
        events2: true,
        events3: true,
      },
      calendarOptions: {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
        initialView: "dayGridMonth",
        locale: esLocale,
        headerToolbar: false,
        events: [],
        height: "auto",
      },
    };
  },
  methods: {
    prev() {
      this.$refs.calendar.getApi().prev();
      this.updateCalendarTitle();
    },
    next() {
      this.$refs.calendar.getApi().next();
      this.updateCalendarTitle();
    },
    today() {
      this.$refs.calendar.getApi().today();
      this.updateCalendarTitle();
    },
    changeView(event) {
      const view = event.target.value;
      this.$refs.calendar.getApi().changeView(view);
      this.updateCalendarTitle();
    },
    updateCalendarTitle() {
      this.calendarTitle = this.$refs.calendar.getApi().view.title;
    },
    extraAction() {
      alert("Botón Extra Presionado");
    },
    getFilteredEvents() {
      const allEvents = [
        { id: 1, title: "Evento 1", start: "2024-11-20", color: "#007bff" },
        { id: 2, title: "Evento 2", start: "2024-11-21", color: "#6f42c1" },
        { id: 3, title: "Evento 3", start: "2024-11-22", end: "2024-11-24", color: "#6610f2" },
      ];
      return allEvents.filter(event => {
        if (event.id === 1 && !this.filters.events1) return false;
        if (event.id === 2 && !this.filters.events2) return false;
        if (event.id === 3 && !this.filters.events3) return false;
        return true;
      });
    },
    applyFilters() {
      const filteredEvents = this.getFilteredEvents();
      this.calendarOptions.events = filteredEvents;
      this.$refs.calendar.getApi().removeAllEventSources();
      this.$refs.calendar.getApi().addEventSource(filteredEvents);
    },
  },
  mounted() {
    this.calendarOptions.events = this.getFilteredEvents();
    this.updateCalendarTitle();
  },
};
</script>
<style scoped>
.calendar-page {
  margin: 20px auto;
}

.calendar-toolbar {
  margin-bottom: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.custom-toolbar .button {
  margin-right: 10px;
}

.calendar-title {
  font-weight: bold;
  margin: 0 10px;
}

.calendar-container {
  display: flex;
  max-width: 1200px;
  margin: 0 auto;
}

.sidebar {
  width: 200px;
  padding: 10px;
  border-right: 1px solid #ddd;
  background-color: #f9f9f9;
}

.sidebar h3 {
  margin-bottom: 10px;
}

.sidebar label {
  display: block;
  margin-bottom: 5px;
}

.calendar-table {
  flex: 1;
  padding: 10px;
}
</style>
