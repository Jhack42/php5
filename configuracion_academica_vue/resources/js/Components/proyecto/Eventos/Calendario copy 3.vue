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
          <button class="button is-fullwidth is-primary mb-4">
            <span class="icon">
              <i class="fas fa-plus"></i>
            </span>
            <span>Crear</span>
          </button>

          <!-- Mini Calendar -->
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
            <div class="days-grid">
              <div v-for="day in shortDays" :key="day" class="day-header">
                {{ day }}
              </div>
              <div
                v-for="(day, index) in miniCalendarDays"
                :key="index"
                :class="[
                  'day',
                  { 'is-today': day.isToday },
                  { 'is-other-month': day.isOtherMonth },
                  { 'is-selected': isDateSelected(day.date) },
                ]"
                @click="selectDate(day.date, $event)"
              >
                {{ day.day }}
                <span v-if="day.hasEvents" class="event-indicator"></span>
              </div>
            </div>
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

  export default {
    components: {
      FullCalendar,
    },
    data() {
      return {
        // FullCalendar options
        calendarOptions: {
          plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
          initialView: "dayGridMonth",
          headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
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
        currentDate: new Date(),
        selectedDates: [],
      };
    },
    mounted() {
      this.generateMiniCalendar(this.currentDate);
    },
    methods: {
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

          const hasEvents = this.calendarOptions.events.some((event) => {
            const eventStart = new Date(event.start);
            return (
              eventStart.toDateString() === dayDate.toDateString() ||
              (event.end && new Date(event.end) >= dayDate && eventStart <= dayDate)
            );
          });

          return {
            day: dayDate.getDate(),
            isOtherMonth: dayDate.getMonth() !== date.getMonth(),
            isToday: dayDate.toDateString() === new Date().toDateString(),
            hasEvents,
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
      selectDate(date, event) {
        if (event.shiftKey) {
          this.selectedDates.push(date);
        } else {
          this.selectedDates = [date];
        }
        this.updateFullCalendarView();
      },
      isDateSelected(date) {
        return this.selectedDates.some(
          (selectedDate) => selectedDate.toDateString() === date.toDateString()
        );
      },
      updateFullCalendarView() {
        const fullCalendarApi = this.$refs.fullCalendar.getApi();
        if (this.selectedDates.length === 1) {
          fullCalendarApi.gotoDate(this.selectedDates[0]);
        } else if (this.selectedDates.length > 1) {
          const startDate = this.selectedDates[0];
          const endDate = this.selectedDates[this.selectedDates.length - 1];
          fullCalendarApi.changeView("timeGridWeek", startDate);
          fullCalendarApi.gotoDate(startDate);
        }
      },
      goToToday() {
        this.$refs.fullCalendar.getApi().today();
      },
      goToPreviousMonth() {
        this.$refs.fullCalendar.getApi().prev();
      },
      goToNextMonth() {
        this.$refs.fullCalendar.getApi().next();
      },
    },
  };
  </script>

  <style scoped>
  .mini-calendar .days-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
  }
  .mini-calendar .day-header {
    text-align: center;
    font-weight: bold;
  }
  .mini-calendar .day {
    text-align: center;
    padding: 4px;
    cursor: pointer;
    position: relative;
  }
  .mini-calendar .day.is-today {
    background-color: #007bff;
    color: white;
    border-radius: 50%;
  }
  .mini-calendar .day.is-other-month {
    color: lightgray;
  }
  .mini-calendar .event-indicator {
    width: 5px;
    height: 5px;
    background-color: #007bff;
    border-radius: 50%;
    position: absolute;
    bottom: 5px;
    left: 50%;
    transform: translateX(-50%);
  }
  </style>
