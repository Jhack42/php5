<template>
    <div class="calendar-container">
      <FullCalendar :options="calendarOptions" />
    </div>
  </template>

  <script>
  import FullCalendar from "@fullcalendar/vue3";
  import dayGridPlugin from "@fullcalendar/daygrid";
  import timeGridPlugin from "@fullcalendar/timegrid";
  import listPlugin from "@fullcalendar/list";
  import esLocale from "@fullcalendar/core/locales/es"; // Importa el idioma español

  export default {
    components: {
      FullCalendar,
    },
    data() {
      return {
        calendarOptions: {
          plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
          initialView: "dayGridMonth",
          locale: esLocale, // Configura el idioma español
          headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
          },
          events: [
            { title: "Evento 1", start: "2024-11-20", color: "#007bff" },
            { title: "Evento 2", start: "2024-11-21", color: "#6f42c1" },
            { title: "Evento 3", start: "2024-11-22", end: "2024-11-24", color: "#6610f2" },
          ],
          height: "auto",
        },
      };
    },
  };
  </script>

  <style scoped>
  .calendar-container {
    max-width: 900px;
    margin: 20px auto;
  }
  </style>
