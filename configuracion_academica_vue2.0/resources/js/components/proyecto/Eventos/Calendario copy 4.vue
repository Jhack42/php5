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


<style lang="scss" src="../../../../scss/calendar.scss"></style>
