<template>
    <div class="calendar-app">
      <!-- Header -->
      <header class="header">
        <div class="level">
          <div class="level-left">
            <button class="button is-ghost">
              <span class="icon">
                <i class="fas fa-bars"></i>
              </span>
            </button>
            <h1 class="title is-4 ml-4">{{ selectedMonth }}</h1>
            <div class="buttons ml-4">
              <button class="button is-ghost" @click="goToPreviousMonth">
                <span class="icon">
                  <i class="fas fa-chevron-left"></i>
                </span>
              </button>
              <button class="button is-ghost" @click="goToNextMonth">
                <span class="icon">
                  <i class="fas fa-chevron-right"></i>
                </span>
              </button>
            </div>
          </div>
          <div class="level-right">
            <button class="button is-primary" @click="goToToday">Hoy</button>
          </div>
        </div>
      </header>

      <div class="main-content">
        <!-- Sidebar -->
        <aside class="sidebar">
          <div class="mini-calendar box">
            <div class="header is-flex is-justify-content-space-between">
              <span>{{ miniCalendarMonth }}</span>
              <div class="buttons">
                <button class="button is-small is-ghost" @click="miniCalendarPreviousMonth">
                  <span class="icon">
                    <i class="fas fa-chevron-left"></i>
                  </span>
                </button>
                <button class="button is-small is-ghost" @click="miniCalendarNextMonth">
                  <span class="icon">
                    <i class="fas fa-chevron-right"></i>
                  </span>
                </button>
              </div>
            </div>
            <div class="days">
              <div v-for="day in shortDays" :key="day" class="day is-header has-text-grey-light">
                {{ day }}
              </div>
              <div
                v-for="(day, index) in miniCalendarDays"
                :key="index"
                :class="[
                  'day',
                  { 'is-today': index === currentDay },
                  { 'is-other-month': day.isOtherMonth }
                ]"
                @click="filterByDate(day.date)"
              >
                {{ day.day }}
              </div>
            </div>
          </div>

          <!-- Event Filters -->
          <div class="event-filters">
            <h3 class="title is-6">Filtros</h3>
            <label class="checkbox" v-for="filter in eventFilters" :key="filter.name">
              <input type="checkbox" v-model="filter.active" @change="applyFilters" />
              {{ filter.name }}
            </label>
          </div>
        </aside>

        <!-- FullCalendar -->
        <main class="calendar">
          <FullCalendar :options="calendarOptions" ref="fullCalendar" />
        </main>
      </div>
    </div>
  </template>

  <script>
  import FullCalendar from "@fullcalendar/vue3";
  import dayGridPlugin from "@fullcalendar/daygrid";
  import timeGridPlugin from "@fullcalendar/timegrid";
  import listPlugin from "@fullcalendar/list";
  import multiMonthPlugin from "@fullcalendar/multimonth";

  export default {
    components: {
      FullCalendar,
    },
    data() {
      return {
        // FullCalendar options
        calendarOptions: {
          plugins: [dayGridPlugin, timeGridPlugin, listPlugin, multiMonthPlugin],
          initialView: "dayGridMonth",
          headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek,multiMonthYear",
          },
          events: [
            { title: "Practicas-UNI", start: "2024-11-09", color: "#007bff" },
            { title: "Formación práctica", start: "2024-11-09", color: "#6f42c1" },
            { title: "INFORME SEMANAL - 10", start: "2024-11-02", end: "2024-11-08", color: "#6610f2" },
          ],
          height: "auto",
          contentHeight: "auto",
          aspectRatio: 1.5,
          editable: true,
        },
        // Mini Calendar Data
        shortDays: ["D", "L", "M", "X", "J", "V", "S"],
        miniCalendarDays: [],
        miniCalendarMonth: "",
        selectedMonth: "Noviembre 2024",
        currentDate: new Date(),
        eventFilters: [
          { name: "Practicas-UNI", active: true, color: "#007bff" },
          { name: "Formación práctica", active: true, color: "#6f42c1" },
          { name: "INFORME SEMANAL - 10", active: true, color: "#6610f2" },
        ],
      };
    },
    mounted() {
      this.generateMiniCalendar(this.currentDate);
    },
    methods: {
      // Mini Calendar Methods
      generateMiniCalendar(date) {
        const startOfMonth = new Date(date.getFullYear(), date.getMonth(), 1);
        const startOfWeek = new Date(
          startOfMonth.setDate(startOfMonth.getDate() - startOfMonth.getDay())
        );
        this.miniCalendarMonth = date.toLocaleDateString("es-ES", {
          month: "long",
          year: "numeric",
        });

        this.miniCalendarDays = Array.from({ length: 42 }, (_, index) => {
          const dayDate = new Date(startOfWeek);
          dayDate.setDate(dayDate.getDate() + index);
          return {
            day: dayDate.getDate(),
            isOtherMonth: dayDate.getMonth() !== date.getMonth(),
            date: dayDate,
          };
        });
      },
      miniCalendarPreviousMonth() {
        const newDate = new Date(
          this.currentDate.getFullYear(),
          this.currentDate.getMonth() - 1,
          1
        );
        this.currentDate = newDate;
        this.generateMiniCalendar(newDate);
      },
      miniCalendarNextMonth() {
        const newDate = new Date(
          this.currentDate.getFullYear(),
          this.currentDate.getMonth() + 1,
          1
        );
        this.currentDate = newDate;
        this.generateMiniCalendar(newDate);
      },
      filterByDate(date) {
        this.$refs.fullCalendar.getApi().gotoDate(date);
      },
      // FullCalendar Header Navigation
      goToToday() {
        this.$refs.fullCalendar.getApi().today();
      },
      goToPreviousMonth() {
        this.$refs.fullCalendar.getApi().prev();
      },
      goToNextMonth() {
        this.$refs.fullCalendar.getApi().next();
      },
      applyFilters() {
        const activeFilters = this.eventFilters
          .filter((filter) => filter.active)
          .map((filter) => filter.name);

        const filteredEvents = this.calendarOptions.events.filter((event) =>
          activeFilters.includes(event.title)
        );

        this.$refs.fullCalendar.getApi().removeAllEvents();
        this.$refs.fullCalendar.getApi().addEventSource(filteredEvents);
      },
    },
  };
  </script>

  <style scoped>
  .calendar-app {
    display: flex;
    flex-direction: column;
    height: 100vh;
  }

  .header {
    padding: 16px;
    border-bottom: 1px solid #e5e5e5;
    background-color: white;
    z-index: 10;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .main-content {
    display: flex;
    flex: 1;
    overflow: hidden;
  }

  .sidebar {
    width: 300px;
    border-right: 1px solid #e5e5e5;
    padding: 16px;
    overflow-y: auto;
    background: #f9f9f9;
  }

  .mini-calendar {
    margin-bottom: 24px;
  }

  .mini-calendar .header {
    font-weight: bold;
    margin-bottom: 12px;
  }

  .mini-calendar .days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
  }

  .mini-calendar .day {
    text-align: center;
    padding: 4px;
    cursor: pointer;
  }

  .mini-calendar .day.is-today {
    background: #007bff;
    color: white;
    border-radius: 50%;
  }

  .mini-calendar .day.is-other-month {
    color: lightgray;
  }

  .calendar {
    flex: 1;
    display: flex;
    flex-direction: column;
  }
  </style>
