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
            <h1 class="title is-4 ml-4">Noviembre 2024</h1>
            <div class="buttons ml-4">
              <button class="button is-ghost">
                <span class="icon">
                  <i class="fas fa-chevron-left"></i>
                </span>
              </button>
              <button class="button is-ghost">
                <span class="icon">
                  <i class="fas fa-chevron-right"></i>
                </span>
              </button>
            </div>
          </div>
          <div class="level-right">
            <button class="button is-primary">Hoy</button>
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
              <span>Diciembre 2024</span>
              <div class="buttons">
                <button class="button is-small is-ghost">
                  <span class="icon">
                    <i class="fas fa-chevron-left"></i>
                  </span>
                </button>
                <button class="button is-small is-ghost">
                  <span class="icon">
                    <i class="fas fa-chevron-right"></i>
                  </span>
                </button>
              </div>
            </div>
            <div class="days">
              <div
                v-for="day in shortDays"
                :key="day"
                class="day is-header has-text-grey-light"
              >
                {{ day }}
              </div>
              <div
                v-for="(day, index) in miniCalendarDays"
                :key="index"
                :class="[
                  'day',
                  { 'is-today': index === 16 },
                  { 'is-other-month': index < 4 || index > 30 }
                ]"
              >
                {{ day }}
              </div>
            </div>
          </div>

          <!-- Events -->
          <div class="events">
            <div class="field">
              <p class="control has-icons-left">
                <input class="input" type="text" placeholder="Buscar personas" />
                <span class="icon is-left">
                  <i class="fas fa-search"></i>
                </span>
              </p>
            </div>

            <ul>
              <li v-for="event in events" :key="event.title">
                <span :style="{ backgroundColor: event.color }" class="dot"></span>
                {{ event.title }}
              </li>
            </ul>
          </div>
        </aside>

        <!-- Calendar -->
        <main class="calendar">
          <div class="calendar-grid">
            <div class="time-column">
              <div v-for="hour in hours" :key="hour" class="time-slot">
                {{ hour }}
              </div>
            </div>
            <div class="day-column" v-for="day in days" :key="day">
              <div class="day-header">{{ day }}</div>
              <div
                v-for="slot in timeSlots"
                :key="slot"
                class="day-slot"
              >
                <div v-if="slot === 1 && day === 'LUN'" class="event">
                  8am Practicas-UNI
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </template>

  <script>
  export default {
    data() {
      return {
        days: ["DOM", "LUN", "MAR", "MIÉ", "JUE", "VIE", "SÁB"],
        shortDays: ["D", "L", "M", "X", "J", "V", "S"],
        miniCalendarDays: Array.from({ length: 35 }, (_, i) => ((i + 28) % 31) + 1),
        hours: Array.from({ length: 12 }, (_, i) => `${i + 7} AM`),
        timeSlots: Array.from({ length: 5 }),
        events: [
          { title: "Practicas-UNI", color: "#007bff" },
          { title: "Formación práctica", color: "#6f42c1" },
          { title: "INFORME SEMANAL - 10", color: "#6610f2" },
        ],
      };
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
  }

  .mini-calendar .day.is-header {
    font-size: 12px;
    color: gray;
  }

  .mini-calendar .day.is-today {
    background: #007bff;
    color: white;
    border-radius: 50%;
  }

  .mini-calendar .day.is-other-month {
    color: lightgray;
  }

  .events ul {
    margin-top: 16px;
    list-style: none;
    padding: 0;
  }

  .events li {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
  }

  .events .dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 8px;
  }

  .calendar {
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .calendar-grid {
    display: flex;
    flex: 1;
    overflow-y: auto;
  }

  .time-column {
    width: 60px;
    border-right: 1px solid #e5e5e5;
  }

  .time-slot {
    height: 80px;
    font-size: 12px;
    text-align: center;
    color: gray;
  }

  .day-column {
    flex: 1;
    border-right: 1px solid #e5e5e5;
  }

  .day-header {
    height: 50px;
    text-align: center;
    font-weight: bold;
    border-bottom: 1px solid #e5e5e5;
  }

  .day-slot {
    height: 80px;
    padding: 4px;
    position: relative;
  }

  .event {
    background: #007bff;
    color: white;
    padding: 4px;
    border-radius: 4px;
    font-size: 12px;
  }
  </style>
