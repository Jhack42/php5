<template>
    <div class="eventos-component columns">
        <!-- Barra lateral de filtros -->
        <div :class="posicionBarra.includes('lateral') ? 'column is-one-quarter' : ''">
            <aside v-if="posicionBarra.includes('lateral')" :class="['menu', { 'is-left': posicionBarra === 'lateral-izquierda', 'is-right': posicionBarra === 'lateral-derecha' }]">
                <div class="config-header">
                    <p class="menu-label">Filtros</p>
                    <span @click="mostrarConfiguracion = true" class="config-icon">⚙️</span>
                </div>
                <ul class="menu-list">
                    <li v-for="filtro in filtrosVisibles" :key="filtro.clave">
                        <label>{{ filtro.titulo }}</label>
                        <input v-if="filtro.tipo === 'text'" type="text" class="input is-small"
                            v-model="valoresFiltros[filtro.clave]" @input="aplicarFiltros" />
                        <select v-if="filtro.tipo === 'select'" class="select is-small"
                            v-model="valoresFiltros[filtro.clave]" @change="aplicarFiltros">
                            <option v-for="opcion in filtro.opciones" :key="opcion" :value="opcion">{{ opcion }}
                            </option>
                        </select>
                    </li>
                </ul>
            </aside>
        </div>

        <!-- Configuración (Modal) -->
        <div v-if="mostrarConfiguracion" class="modal is-active">
            <div class="modal-background" @click="mostrarConfiguracion = false"></div>
            <div class="modal-content">
                <div class="box">
                    <h3 class="title">Configuración</h3>
                    <div class="tabs is-centered is-boxed">
                        <ul>
                            <li :class="{ 'is-active': configuracionActiva === 'filtros' }"
                                @click="configuracionActiva = 'filtros'">
                                <a>Filtros</a>
                            </li>
                            <li :class="{ 'is-active': configuracionActiva === 'columnas' }"
                                @click="configuracionActiva = 'columnas'">
                                <a>Columnas</a>
                            </li>
                            <li :class="{ 'is-active': configuracionActiva === 'posicion' }"
                                @click="configuracionActiva = 'posicion'">
                                <a>Posición</a>
                            </li>
                        </ul>
                    </div>
                    <div v-if="configuracionActiva === 'filtros'">
                        <ul>
                            <li v-for="filtro in opcionesFiltros" :key="filtro.clave" class="config-item">
                                <input type="checkbox" v-model="filtro.visible" class="checkbox" />
                                <input type="text" v-model="filtro.titulo" class="input is-small"
                                    placeholder="Nombre del filtro" />
                                <select v-model="filtro.tipo" class="select is-small">
                                    <option value="text">Texto</option>
                                    <option value="select">Seleccionar</option>
                                </select>
                            </li>
                        </ul>
                        <button @click="guardarConfiguracionFiltros" class="button is-success is-small">Guardar
                            Filtros</button>
                    </div>
                    <div v-if="configuracionActiva === 'columnas'">
                        <ul>
                            <li v-for="(columna, index) in opcionesColumnas" :key="columna.clave" class="config-item">
                                <input type="checkbox" v-model="columna.visible" class="checkbox" />
                                <input type="text" v-model="columna.titulo" class="input is-small"
                                    placeholder="Nombre de columna" />
                                <button @click="moverColumna(index, -1)" :disabled="index === 0"
                                    class="button is-small">↑</button>
                                <button @click="moverColumna(index, 1)"
                                    :disabled="index === opcionesColumnas.length - 1" class="button is-small">↓</button>
                            </li>
                        </ul>
                        <button @click="guardarConfiguracionColumnas" class="button is-success is-small">Guardar
                            Columnas</button>
                    </div>
                    <!-- Nueva sección para la configuración de la posición -->
                    <div v-if="configuracionActiva === 'posicion'">
                        <label>Posición de la barra lateral:</label>
                        <div class="select">
                            <select v-model="posicionBarra">
                                <option value="lateral-izquierda">Izquierda</option>
                                <option value="lateral-derecha">Derecha</option>
                                <option value="horizontal-arriba">Arriba</option>
                                <option value="horizontal-abajo">Abajo</option>
                            </select>
                        </div>
                        <button @click="guardarPosicion" class="button is-success is-small">Guardar Posición</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de eventos -->
        <div class="column" :class="posicionBarra.includes('horizontal') ? 'is-narrow' : ''">
            <table class="table is-striped is-fullwidth">
                <thead>
                    <tr>
                        <th v-for="columna in columnasVisibles" :key="columna.clave">{{ columna.titulo }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="evento in eventosFiltrados" :key="evento.id_actividad">
                        <td v-for="columna in columnasVisibles" :key="columna.clave">
                            {{ obtenerValor(evento, columna.clave) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Cookies from 'js-cookie';

export default {
    data() {
        return {
            mostrarConfiguracion: false,
            configuracionActiva: 'filtros', // Opción activa en el modal de configuración
            eventosFiltrados: [],
            valoresFiltros: {},
            posicionBarra: 'lateral-izquierda', // Posición inicial de la barra de filtros
            opcionesFiltros: [
                { clave: 'periodo', titulo: 'Periodo', tipo: 'text', visible: true },
                { clave: 'codigo_actividad', titulo: 'Código Actividad', tipo: 'text', visible: true },
                { clave: 'estado', titulo: 'Estado', tipo: 'select', visible: true, opciones: ['A', 'I'] },
            ],
            opcionesColumnas: [
                { clave: 'id_actividad', titulo: 'ID Actividad', visible: true },
                { clave: 'nombre_actividad', titulo: 'Nombre Actividad', visible: true },
                { clave: 'jerarquia.nivel', titulo: 'Jerarquía', visible: true },
                { clave: 'facultad', titulo: 'Facultad', visible: false },
                { clave: 'especialidad', titulo: 'Especialidad', visible: false },
                { clave: 'periodo', titulo: 'Periodo', visible: true },
                { clave: 'codigo_actividad', titulo: 'Código Actividad', visible: true },
                { clave: 'estado', titulo: 'Estado', visible: true },
                { clave: 'medio.nombre', titulo: 'Medio', visible: false },
                { clave: 'responsable.nombre', titulo: 'Responsable', visible: true },
                { clave: 'procesa.nombre', titulo: 'Procesa', visible: false },
                { clave: 'observacion', titulo: 'Observación', visible: false },
                { clave: 'fullcalendar.descripcion', titulo: 'Descripción', visible: true },
                { clave: 'fullcalendar.fecha_inicio', titulo: 'Fecha Inicio', visible: true },
                { clave: 'fullcalendar.fecha_fin', titulo: 'Fecha Fin', visible: true },
                { clave: 'fullcalendar.tipo_actividad', titulo: 'Tipo Actividad', visible: false },
                { clave: 'fullcalendar.todo_el_dia', titulo: 'Todo el Día', visible: false },
            ],
        };
    },
    computed: {
        filtrosVisibles() {
            return this.opcionesFiltros.filter(filtro => filtro.visible);
        },
        columnasVisibles() {
            return this.opcionesColumnas.filter(col => col.visible);
        },
    },
    methods: {
        async cargarDatos() {
            try {
                const response = await axios.get('http://localhost:3000/api/data/para-la-vita1');
                this.eventosFiltrados = response.data;
            } catch (error) {
                console.error("Error al cargar los datos:", error);
            }
        },
        obtenerValor(obj, key) {
            return key.split('.').reduce((acc, k) => (acc ? acc[k] : ''), obj);
        },
        aplicarFiltros() {
            const filtrosAplicados = this.eventosFiltrados.filter(evento => {
                return this.filtrosVisibles.every(filtro => {
                    const valorFiltro = this.valoresFiltros[filtro.clave];
                    if (!valorFiltro) return true;
                    const valorCampo = this.obtenerValor(evento, filtro.clave);
                    return valorCampo.toString().includes(valorFiltro);
                });
            });
            this.eventosFiltrados = filtrosAplicados;
        },
        moverColumna(index, direccion) {
            const newIndex = index + direccion;
            if (newIndex >= 0 && newIndex < this.opcionesColumnas.length) {
                const temp = this.opcionesColumnas[index];
                this.opcionesColumnas[index] = this.opcionesColumnas[newIndex];
                this.opcionesColumnas[newIndex] = temp;
            }
        },
        guardarConfiguracionFiltros() {
            Cookies.set('configuracionFiltros', JSON.stringify(this.opcionesFiltros), { expires: 7 });
            this.mostrarConfiguracion = false;
        },
        guardarConfiguracionColumnas() {
            Cookies.set('configuracionColumnas', JSON.stringify(this.opcionesColumnas), { expires: 7 });
            this.mostrarConfiguracion = false;
        },
        guardarPosicion() {
            // Guardar la configuración de la posición de la barra en cookies
            Cookies.set('posicionBarra', this.posicionBarra, { expires: 7 });
            this.mostrarConfiguracion = false;
        },
        cargarConfiguracion() {
            const filtrosGuardados = Cookies.get('configuracionFiltros');
            const columnasGuardadas = Cookies.get('configuracionColumnas');
            const posicionGuardada = Cookies.get('posicionBarra');

            if (filtrosGuardados) this.opcionesFiltros = JSON.parse(filtrosGuardados);
            if (columnasGuardadas) this.opcionesColumnas = JSON.parse(columnasGuardadas);
            if (posicionGuardada) this.posicionBarra = posicionGuardada;
        },
    },
    mounted() {
        this.cargarDatos();
        this.cargarConfiguracion();
    },
};
</script>

<style scoped>
/* Paleta de colores */
:root {
    --color-primary: #8B0202;
    --color-background: #E8CFA0;
    --color-header: #F1F1F1;
    --color-text: #333;
    --color-border: #939598;
    --color-button: #8B0202;
    --color-button-hover: #7a0202;
    --color-disabled: #C4C4C4;
}

body {
    font-family: 'Lato', sans-serif;
    background-color: #fafafa;
    color: var(--color-text);
    margin: 0;
    padding: 0;
}

/* Contenedor principal con Flexbox */
.eventos-component {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin: 20px;
}

/* Modal */
.modal.is-active {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
}

.box {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    width: 80%;
    max-width: 800px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Cabecera de Configuración */
.config-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: var(--color-background);
    padding: 10px;
    border-radius: 5px;
    color: var(--color-primary);
}

.config-icon {
    cursor: pointer;
    font-size: 1.5rem;
    color: var(--color-primary);
}

.config-icon:hover {
    color: #f4b400; /* Color de hover */
}

/* Sección de configuración */
.config-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
}

.config-item input,
.config-item select {
    padding: 6px;
    font-size: 1rem;
    border: 1px solid var(--color-border);
    border-radius: 4px;
    width: 100%;
    max-width: 200px;
}

.config-item label {
    font-weight: 500;
}

/* Contenedor de Filtros */
.menu {
    background-color: var(--color-header);
    padding: 10px;
    border-radius: 5px;
}

.menu ul {
    list-style: none;
    padding: 0;
}

.menu li {
    margin-bottom: 10px;
}

/* Barra de filtros lateral */
.menu.is-left {
    order: -1; /* Barra de filtros a la izquierda */
}

.menu.is-right {
    order: 1; /* Barra de filtros a la derecha */
}

/* Barra de filtros horizontal */
.is-narrow {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 10px;
    width: 100%;
}

/* Tabla de eventos */
.table {
    width: 100%;
    border-collapse: collapse;
    background-color: #ffffff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

th, td {
    padding: 12px;
    text-align: left;
    font-size: 1rem;
}

th {
    background-color: var(--color-background);
    font-weight: 600;
}

td {
    border-bottom: 1px solid #f1f1f1;
}

/* Botones */
button {
    background-color: var(--color-button);
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
}

button:hover {
    background-color: var(--color-button-hover);
    transition: background-color 0.3s ease;
}

button:disabled {
    background-color: var(--color-disabled);
    cursor: not-allowed;
}

/* Pestañas de Configuración */
.tabs {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.tabs ul {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
}

.tabs li {
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.tabs li.is-active {
    background-color: var(--color-background);
    color: var(--color-primary);
}

.tabs li:hover {
    background-color: #f4f4f4;
}

.is-active a {
    color: var(--color-primary);
}

/* Animaciones y transiciones */
.fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
