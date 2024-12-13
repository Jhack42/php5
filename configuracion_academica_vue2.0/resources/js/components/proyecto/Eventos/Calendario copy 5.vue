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
                </div>
                <div class="level-right">
                    <!-- Barra de navegación personalizada -->
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
                    </div>
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
                <div class="mini-calendar">
                    <div class="header">
                        {{ miniCalendarMonth }}
                        <div class="buttons">
                            <button class="button is-small is-ghost" @click="miniCalendarPreviousMonth">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="button is-small is-ghost" @click="miniCalendarNextMonth">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="days">
                        <div v-for="day in shortDays" :key="day" class="day-header">
                            {{ day }}
                        </div>
                        <div v-for="(day, index) in miniCalendarDays" :key="index"
                            :class="['day', { 'is-today': day.isToday }, { 'is-other-month': day.isOtherMonth }, { 'is-selected': isDateSelected(day.date) }]"
                            @click="selectDate(day.date, $event)">
                            {{ day.day }}
                            <span v-if="day.hasEvents" class="event-indicator"></span>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- FullCalendar -->
            <main class="calendar">
                <FullCalendar :options="calendarOptions" ref="calendar" />
            </main>
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
            // FullCalendar options
            calendarOptions: {
                plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
                initialView: "dayGridMonth",
                locale: esLocale, // Idioma español
                headerToolbar: false, // Desactiva la barra predeterminada
                events: [
                    //-----------------Hasta-------------------------
                    //--febrero
                    { title: "Presemtacion de Solicitud por traslado Interno", start: "2024-02-05", color: "#007bff", editable: true },
                    { title: "Examen de Admisión Ordinario Presencial / Traslado externo / Otras modalidades", start: "2024-02-12", end: "2024-02-16", color: "#6610f2", editable: true },
                    { title: "txt", start: "2024-02-22", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-02-22", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-02-23", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-02-25", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-02-26", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-02-29", color: "#007bff", editable: true },
                    //--Marzo
                    { title: "txt", start: "2024-03-01", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-03-01", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-03-03", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-03-05", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-03-05", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-03-05", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-03-06", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-03-08", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-03-08", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-03-10", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-03-11", end: "2024-03-15", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-03-18", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-03-18", end : "2024-03-22", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-03-18", end : "2024-03-22", color: "#007bff", editable: true },

                    //-- Abril
                    { title: "txt", start: "2024-04-12", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-04-15", end: "2024-04-19", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-04-29", end: "2024-05-03", color: "#007bff", editable: true },

                    //-- Mayo
                    { title: "txt", start: "2024-05-06", end : "2024-05-10", color: "#007bff", editable: true },

                    //-- Junio
                    { title: "txt", start: "2024-06-24", end : "2024-06-28", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-06-24", end : "2024-06-28", color: "#007bff", editable: true },

                    //-- Julio
                    { title: "txt", start: "2024-07-01", end : "2024-07-05", color: "#007bff", editable: true },
                    { title: "txt", start: "2024-07-15", end : "2024-07-19", color: "#007bff", editable: true},




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
        this.updateCalendarTitle();
        this.generateMiniCalendar(this.currentDate);
    },
    methods: {
        // Barra personalizada
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

        // Mini calendario
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
            const fullCalendarApi = this.$refs.calendar.getApi();
            if (this.selectedDates.length === 1) {
                fullCalendarApi.gotoDate(this.selectedDates[0]);
            } else if (this.selectedDates.length > 1) {
                const startDate = this.selectedDates[0];
                const endDate = this.selectedDates[this.selectedDates.length - 1];
                fullCalendarApi.changeView("timeGridWeek", startDate);
                fullCalendarApi.gotoDate(startDate);
            }
        },
    },
};
</script>


<style scoped>
:root {
    /* Tema claro */
    --background-color: #ffffff;
    --text-color: #333333;
    --border-color: #e5e5e5;
    --sidebar-background: #f9f9f9;
    --calendar-background: #ffffff;
    --primary-color: #007bff;
    --primary-text: #ffffff;
    --muted-text: #999999;
    --hover-background: #f0f0f0;
    --selected-background: #e6f0ff;
    --selected-text: #007bff;
    --button-background: #ffffff;
    --button-text: #333333;
    --button-hover-background: #f5f5f5;
}

:root.dark {
    /* Tema oscuro */
    --background-color: #1a1a1a;
    --text-color: #ffffff;
    --border-color: #2d2d2d;
    --sidebar-background: #242424;
    --calendar-background: #2d2d2d;
    --primary-color: #3b82f6;
    --primary-text: #ffffff;
    --muted-text: #666666;
    --hover-background: #333333;
    --selected-background: #2d4a7d;
    --selected-text: #ffffff;
    --button-background: #333333;
    --button-text: #ffffff;
    --button-hover-background: #404040;
}

.calendar-app {
    display: flex;
    flex-direction: column;
    height: 100vh;
    background-color: var(--background-color);
    color: var(--text-color);
}

.header {
    padding: 16px;
    border-bottom: 1px solid var(--border-color);
    background-color: var(--background-color);
    color: var(--text-color);
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
    border-right: 1px solid var(--border-color);
    padding: 16px;
    overflow-y: auto;
    background: var(--sidebar-background);
}

.mini-calendar {
    margin-bottom: 24px;
    background-color: var(--calendar-background);
    border-radius: 8px;
    padding: 12px;
}

.mini-calendar .header {
    font-weight: bold;
    margin-bottom: 12px;
    color: var(--text-color);
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
    color: var(--text-color);
    border-radius: 50%;
    position: relative;
}

.mini-calendar .day:hover {
    background-color: var(--hover-background);
}

.mini-calendar .day.is-today {
    background: var(--primary-color);
    color: var(--primary-text);
    border-radius: 50%;
}

.mini-calendar .day.is-other-month {
    color: var(--muted-text);
}

.mini-calendar .day.is-selected {
    background-color: var(--selected-background);
    color: var(--selected-text);
}

.mini-calendar .event-indicator {
    width: 5px;
    height: 5px;
    background-color: var(--primary-color);
    border-radius: 50%;
    position: absolute;
    bottom: 5px;
    left: 50%;
    transform: translateX(-50%);
}

.calendar {
    flex: 1;
    display: flex;
    flex-direction: column;
    background-color: var(--background-color);
}

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
    background-color: var(--button-background);
    color: var(--button-text);
    border: 1px solid var(--border-color);
}

.custom-toolbar .button:hover {
    background-color: var(--button-hover-background);
}

.calendar-title {
    font-weight: bold;
    margin: 0 10px;
    color: var(--text-color);
}

/* Estilos para el select */
select {
    background-color: var(--button-background);
    color: var(--button-text);
    border: 1px solid var(--border-color);
    padding: 5px 10px;
    border-radius: 4px;
}

select:focus {
    outline: none;
    border-color: var(--primary-color);
}
</style>
