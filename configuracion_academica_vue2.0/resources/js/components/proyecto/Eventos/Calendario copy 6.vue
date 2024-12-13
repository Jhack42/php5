<template>
    <div class="calendar">
        <!-- Header -->
        <header class="calendar-header">
            <div class="calendar-header__wrapper">
                <div class="calendar-header__left">
                    <button class="calendar-header__menu-btn">
                        <span class="calendar-header__menu-icon">
                            <i class="fas fa-bars"></i>
                        </span>
                    </button>
                    <div class="calendar-header__title-wrapper">
                        <img src="../../../../../public/img/calendario.png" alt="Calendar Icon" class="calendar-header__icon">
                        <h1 class="calendar-header__title">Calendario</h1>
                    </div>
                </div>

                <div class="calendar-header__center">
                    <div class="calendar-nav">
                        <button class="calendar-nav__btn calendar-nav__btn--today" @click="today">Hoy</button>
                        <button class="calendar-nav__btn calendar-nav__btn--prev" @click="prev">
                            <img src="../../../../../public/img/flecha-correcta.png" alt="Previous"
                                 class="calendar-nav__arrow calendar-nav__arrow--prev">
                        </button>
                        <button class="calendar-nav__btn calendar-nav__btn--next" @click="next">
                            <img src="../../../../../public/img/flecha-correcta.png" alt="Next"
                                 class="calendar-nav__arrow calendar-nav__arrow--next">
                        </button>
                        <h1 class="calendar-nav__current-month">{{ selectedMonth }}</h1>
                    </div>
                    <div class="calendar-view-selector">
                        <select class="calendar-view-selector__dropdown" @change="changeView">
                            <option value="dayGridMonth">Mes</option>
                            <option value="timeGridWeek">Semana</option>
                            <option value="timeGridDay">Día</option>
                            <option value="listWeek">Lista</option>
                            <option value="multiMonthYear">Año</option>
                        </select>
                    </div>
                </div>

                <div class="calendar-header__right">
                    <button class="calendar-header__menu-btn">
                        <div class="calendar-header__app-launcher">
                            <div class="calendar-header__app-dot"></div>
                            <div class="calendar-header__app-dot"></div>
                            <div class="calendar-header__app-dot"></div>
                            <div class="calendar-header__app-dot"></div>
                            <div class="calendar-header__app-dot"></div>
                            <div class="calendar-header__app-dot"></div>
                            <div class="calendar-header__app-dot"></div>
                            <div class="calendar-header__app-dot"></div>
                            <div class="calendar-header__app-dot"></div>
                        </div>
                    </button>
                </div>
            </div>
        </header>

        <div class="calendar-content">
            <!-- Sidebar -->
            <aside class="calendar-sidebar">
                <button class="calendar-sidebar__create-btn">
                    <span class="calendar-sidebar__create-icon">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span class="calendar-sidebar__create-text">Crear</span>
                </button>

                <!-- Mini Calendar -->
                <div class="mini-calendar">
                    <div class="mini-calendar__header">
                        <span class="mini-calendar__month">{{ miniCalendarMonth }}</span>
                        <div class="mini-calendar__nav">
                            <button class="mini-calendar__nav-btn mini-calendar__nav-btn--prev"
                                    @click="miniCalendarPreviousMonth">
                                <img src="../../../../../public/img/flecha-correcta.png" alt="Previous"
                                     class="mini-calendar__arrow mini-calendar__arrow--prev">
                            </button>
                            <button class="mini-calendar__nav-btn mini-calendar__nav-btn--next"
                                    @click="miniCalendarNextMonth">
                                <img src="../../../../../public/img/flecha-correcta.png" alt="Next"
                                     class="mini-calendar__arrow mini-calendar__arrow--next">
                            </button>
                        </div>
                    </div>
                    <div class="mini-calendar__grid">
                        <div v-for="day in shortDays" :key="day" class="mini-calendar__weekday">
                            {{ day }}
                        </div>
                        <div v-for="(day, index) in miniCalendarDays"
                             :key="index"
                             class="mini-calendar__day"
                             :class="{
                                'mini-calendar__day--today': day.isToday,
                                'mini-calendar__day--other-month': day.isOtherMonth,
                                'mini-calendar__day--selected': isDateSelected(day.date)
                             }"
                             @click="selectDate(day.date, $event)">
                            {{ day.day }}
                            <span v-if="day.hasEvents" class="mini-calendar__event-indicator"></span>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Calendar -->
            <main class="calendar-main">
                <FullCalendar class="calendar-main__fullcalendar"
                             :options="calendarOptions"
                             ref="calendar" />
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
import esLocale from "@fullcalendar/core/locales/es";

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
                    multiMonthPlugin
                ],
                initialView: "dayGridMonth",
                locale: esLocale,
                headerToolbar: false,
                events: [
                    { title: "Presemtacion de Solicitud por traslado Interno", start: "2024-02-05", color: "#007bff", editable: true },
                    { title: "Examen de Admisión Ordinario Presencial / Traslado externo / Otras modalidades", start: "2024-02-12", end: "2024-02-16", color: "#6610f2", editable: true },
                ],
                // Configuraciones de tamaño
                // Configuraciones de tamaño ajustadas
                height: '100%', // Cambiado de 85vh a 100%
                contentHeight: '100%', // Cambiado de auto a 100%
                aspectRatio: 2, // Aumentado para mejor uso del espacio horizontal
                expandRows: true,

                // Configuraciones de comportamiento responsivo
                handleWindowResize: true, // Responde a cambios de tamaño de ventana
                windowResizeDelay: 200, // Retraso en milisegundos para el redimensionamiento

                // Configuraciones de encabezados y pie de página
                stickyHeaderDates: true, // Encabezados de fecha fijos al hacer scroll
                stickyFooterScrollbar: true, // Barra de desplazamiento fija en la parte inferior

                // Callbacks y eventos
                windowResize: function (arg) {
                    console.log('Calendar has resized');
                },

                // Configuraciones existentes
                editable: true,
                datesSet: function (info) {
                    this.updateCalendarTitle();
                    this.updateSelectedMonth();
                }.bind(this),

                // Configuraciones de multiMonth
                multiMonthMaxColumns: 3,
                multiMonthMinWidth: 350,
                views: {
                    dayGridMonth: {
                        titleFormat: { year: 'numeric', month: 'long' },
                        dayHeaderFormat: { weekday: 'short' },
                        displayEventTime: false,
                        dayMaxEvents: true // Cuando hay demasiados eventos, muestra un enlace "+más"
                    },
                    multiMonthYear: {
                        type: 'multiMonth',
                        duration: { months: 12 },
                        multiMonthMaxColumns: 3,
                        multiMonthMinWidth: 300,
                        titleFormat: { year: 'numeric' },
                        dayMaxEvents: 2,
                        showNonCurrentDates: false,
                        stickyMonthNames: true,
                        height: '100%'
                    },
                    timeGridWeek: {
                        slotMinTime: '07:00:00',
                        slotMaxTime: '20:00:00',
                        expandRows: true
                    },
                    timeGridDay: {
                        slotMinTime: '07:00:00',
                        slotMaxTime: '20:00:00',
                        expandRows: true
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

            // Configurar las dimensiones según la vista
            if (view === 'multiMonthYear') {
                calendarApi.setOption('height', 'auto');
                calendarApi.setOption('contentHeight', 'auto');
                calendarApi.setOption('aspectRatio', 1.2); // Un valor más alto para mejor distribución
            } else {
                // Mantener las dimensiones originales para todas las otras vistas
                calendarApi.setOption('height', '100%');
                calendarApi.setOption('contentHeight', '100%');
                calendarApi.setOption('aspectRatio', 2);
            }

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
