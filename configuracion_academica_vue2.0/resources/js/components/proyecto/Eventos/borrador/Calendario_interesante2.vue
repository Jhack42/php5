<template>
    <div class="calendar-container">
      <!-- Contenedor de la barra interactiva -->
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

      <!-- Contenedor del calendario -->
      <FullCalendar :options="calendarOptions" ref="calendar" />
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
      calendarOptions: {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
        initialView: "dayGridMonth",
        locale: esLocale, // Configura el idioma español
        headerToolbar: false, // Desactiva el encabezado integrado
        events: [
          { title: "Evento 1", start: "2024-11-20", color: "#007bff" },
          { title: "Evento 2", start: "2024-11-21", color: "#6f42c1" },
          { title: "Evento 3", start: "2024-11-22", end: "2024-11-24", color: "#6610f2" },
        ],
        height: "auto",
      },
    };
  },
  mounted() {
    this.updateCalendarTitle();
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
  },
};
</script>


<style scoped>
.calendar-container {
  max-width: 900px;
  margin: 20px auto;
}

.calendar-toolbar {
  margin-bottom: 10px;
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
</style>
