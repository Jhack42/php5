<template>
    <div class="cal-container">
        <!-- Header -->
        <header class="cal-header">
            <div class="cal-header-wrapper">
                <div class="cal-header-left">
                    <button class="cal-menu-btn">
                        <span class="cal-icon">
                            <i class="fas fa-bars"></i>
                        </span>
                    </button>
                    <div class="cal-header-left2">
                        <img src="../../../../../public/img/calendario.png" alt="Anterior" class="nav-arrow-icon2">
                        <h1 class="cal-title">Calendario</h1>
                    </div>
                </div>

                <div class="cal-header-right">
                    <div class="cal-navigation">
                        <button class="cal-nav-btn cal-nav-today" @click="today">Hoy</button>

                        <button class="cal-nav-btn cal-nav-prev" @click="prev">
                            <img src="../../../../../public/img/flecha-correcta.png" alt="Anterior"
                                class="nav-arrow-icon-calendar rotate-180">
                        </button>
                        <button class="cal-nav-btn cal-nav-next" @click="next"><img
                                src="../../../../../public/img/flecha-correcta.png" alt="Anterior"
                                class="nav-arrow-icon-calendar"></button>
                        <h1 class="cal-title">{{ selectedMonth }}</h1>
                    </div>
                    <div class="cal-navigation">
                        <select class="cal-view-select" @change="changeView">
                            <option value="dayGridMonth">Mes</option>
                            <option value="timeGridWeek">Semana</option>
                            <option value="timeGridDay">Día</option>
                            <option value="listWeek">Lista</option>
                            <option value="multiMonthYear">Año</option>
                        </select>
                    </div>
                </div>
                <div class="cal-header-left-derecha">
                    <button class="cal-menu-btn">
                        <span class="cal-icon">
                            <i class="fas fa-bars"></i>
                        </span>
                    </button>
                    <div class="cal-header-left2">
                        <div class="app-launcher-icon">
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="cal-content">
            <!-- Sidebar -->
            <aside class="cal-sidebar">
                <button class="cal-create-btn">
                    <span class="cal-create-icon">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span class="cal-create-text">Crear</span>
                </button>

                <!-- Mini Calendar -->
                <div class="cal-mini">
                    <div class="cal-mini-header">
                        <span class="cal-mini-month">{{ miniCalendarMonth }}</span>
                        <div class="cal-mini-nav">
                            <button class="cal-mini-btn cal-mini-prev" @click="miniCalendarPreviousMonth">
                                <i class="fas fa-chevron-left"></i>
                                <img src="../../../../../public/img/flecha-correcta.png" alt="Anterior"
                                    class="nav-arrow-icon rotate-180">

                            </button>


                            <button class="cal-mini-btn cal-mini-next" @click="miniCalendarNextMonth">
                                <i class="fas fa-chevron-right"></i>
                                <img src="../../../../../public/img/flecha-correcta.png" alt="Anterior"
                                    class="nav-arrow-icon">

                            </button>
                        </div>
                    </div>
                    <div class="cal-mini-grid">
                        <div v-for="day in shortDays" :key="day" class="cal-mini__day-header">
                            {{ day }}
                        </div>
                        <div v-for="(day, index) in miniCalendarDays" :key="index" class="cal-mini__day" :class="{
                            'cal-mini__day--today': day.isToday,
                            'cal-mini__day--other-month': day.isOtherMonth,
                            'cal-mini__day--selected': isDateSelected(day.date)
                        }" @click="selectDate(day.date, $event)">
                            {{ day.day }}
                            <span v-if="day.hasEvents" class="cal-mini__day-event-indicator"></span>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Calendar -->
            <main class="cal-main">
                <FullCalendar class="cal-fullcalendar" :options="calendarOptions" ref="calendar" />
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
import multiMonthPlugin from "@fullcalendar/multimonth"; // Add this import


export default {
    name: 'Calendar',
    components: {
        FullCalendar,
    },
    data() {
        return {
            calendarTitle: "",
            selectedMonth: "",
            calendarOptions: {
                plugins: [
                    dayGridPlugin,
                    timeGridPlugin,
                    listPlugin,
                    multiMonthPlugin,
                ],
                initialView: "dayGridMonth",
                locale: esLocale,
                headerToolbar: false,
                height: '100%',
                handleWindowResize: true,
                editable: true,
                weekends: true,
                firstDay: 1, // Semana empieza en Lunes
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,

                events: [
                    {
                        title: "Presemtacion de Solicitud por traslado Interno",
                        start: "2024-02-05",
                        color: "#007bff",
                        editable: true
                    },
                    {
                        title: "Examen de Admisión Ordinario Presencial / Traslado externo / Otras modalidades",
                        start: "2024-02-12",
                        end: "2024-02-16",
                        color: "#6610f2",
                        editable: true
                    }
                ],

                datesSet: function (info) {
                    this.updateCalendarTitle();
                    this.updateSelectedMonth();
                }.bind(this),

                views: {
                    dayGridMonth: {
                        titleFormat: { year: 'numeric', month: 'long' },
                        dayHeaderFormat: { weekday: 'short' },
                        displayEventTime: false,
                        dayMaxEvents: true,
                        showNonCurrentDates: true,
                        fixedWeekCount: false
                    },

                    timeGridWeek: {
                        titleFormat: { year: 'numeric', month: 'long', day: '2-digit' },
                        slotMinTime: '07:00:00',
                        slotMaxTime: '20:00:00',
                        expandRows: true,
                        slotDuration: '00:30:00',
                        slotLabelInterval: '01:00',
                        allDaySlot: true,
                        allDayText: 'Todo el día',
                        dayHeaderFormat: { weekday: 'long', day: 'numeric' }
                    },

                    timeGridDay: {
                        titleFormat: { year: 'numeric', month: 'long', day: '2-digit' },
                        slotMinTime: '07:00:00',
                        slotMaxTime: '20:00:00',
                        expandRows: true,
                        slotDuration: '00:30:00',
                        slotLabelInterval: '01:00',
                        allDaySlot: true,
                        allDayText: 'Todo el día'
                    },

                    multiMonthYear: {
                        type: 'multiMonth',
                        duration: { months: 12 },
                        multiMonthMaxColumns: 4, // 4 columnas para mostrar los meses
                        multiMonthMinWidth: 250, // Ancho mínimo por mes
                        titleFormat: { year: 'numeric' },
                        dayMaxEvents: 2,
                        showNonCurrentDates: true, // Mostrar días de otros meses
                        format: {
                            month: 'long',
                            year: 'numeric'
                        },
                        fixedWeekCount: true, // Mantener 6 semanas por mes
                        showMonthBeforeYear: true,
                        multiMonthTitleFormat: { month: 'long' }, // Formato del título del mes
                        buttonText: 'Año',
                        eventDisplay: 'block',
                        eventMaxStack: 1,
                    },

                    listWeek: {
                        titleFormat: { year: 'numeric', month: 'long' },
                        noEventsText: 'No hay eventos para mostrar',
                        allDayText: 'Todo el día',
                        listDayFormat: { weekday: 'long', day: 'numeric', month: 'long' },
                        listDaySideFormat: false
                    }
                }
            },

            shortDays: ["D", "L", "M", "X", "J", "V", "S"],
            miniCalendarDays: [],
            miniCalendarMonth: "",
            currentDate: new Date(),
            miniCalendarDate: new Date(),
            selectedDates: [],
        };
    },
    mounted() {
        this.initializeCalendar();
    },
    methods: {
        initializeCalendar() {
            this.$nextTick(() => {
                this.updateCalendarTitle();
                this.updateSelectedMonth();
                this.generateMiniCalendar(this.miniCalendarDate);
            });
        },
        prev() {
            const calendarApi = this.$refs.calendar.getApi();
            calendarApi.prev();
            this.currentDate = calendarApi.getDate();
            this.updateCalendarTitle();
            this.updateSelectedMonth();
        },
        next() {
            const calendarApi = this.$refs.calendar.getApi();
            calendarApi.next();
            this.currentDate = calendarApi.getDate();
            this.updateCalendarTitle();
            this.updateSelectedMonth();
        },
        today() {
            const calendarApi = this.$refs.calendar.getApi();
            calendarApi.today();
            this.currentDate = new Date();
            this.miniCalendarDate = new Date();
            this.updateCalendarTitle();
            this.updateSelectedMonth();
            this.generateMiniCalendar(this.miniCalendarDate);
        },
        changeView(event) {
            const view = event.target.value;
            const calendarApi = this.$refs.calendar.getApi();



            calendarApi.changeView(view);
            this.updateCalendarTitle();
            this.updateSelectedMonth();
        },
        updateCalendarTitle() {
            if (this.$refs.calendar) {
                this.calendarTitle = this.$refs.calendar.getApi().view.title;
            }
        },
        updateSelectedMonth() {
            if (this.$refs.calendar) {
                const date = this.$refs.calendar.getApi().getDate();
                this.selectedMonth = date.toLocaleDateString('es-ES', {
                    month: 'long',
                    year: 'numeric'
                });
                this.currentDate = date;
            }
        },
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
                    const eventEnd = event.end ? new Date(event.end) : new Date(eventStart);
                    return dayDate >= eventStart && dayDate <= eventEnd;
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
                this.miniCalendarDate.getFullYear(),
                this.miniCalendarDate.getMonth() - 1,
                1
            );
            this.miniCalendarDate = newDate;
            this.generateMiniCalendar(newDate);
        },
        miniCalendarNextMonth() {
            const newDate = new Date(
                this.miniCalendarDate.getFullYear(),
                this.miniCalendarDate.getMonth() + 1,
                1
            );
            this.miniCalendarDate = newDate;
            this.generateMiniCalendar(newDate);
        },
        selectDate(date, event) {
            if (event.shiftKey) {
                this.selectedDates.push(date);
            } else {
                this.selectedDates = [date];
            }
            this.updateFullCalendarView();
            this.miniCalendarDate = new Date(date);
            this.generateMiniCalendar(this.miniCalendarDate);
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
                this.currentDate = this.selectedDates[0];
                this.updateCalendarTitle();
                this.updateSelectedMonth();
            } else if (this.selectedDates.length > 1) {
                const startDate = this.selectedDates[0];
                fullCalendarApi.changeView("timeGridWeek", startDate);
                fullCalendarApi.gotoDate(startDate);
                this.currentDate = startDate;
                this.updateCalendarTitle();
                this.updateSelectedMonth();
            }
        },
    },
};
</script>


<style lang="scss" src="../../../../scss/calendar.scss"></style>
