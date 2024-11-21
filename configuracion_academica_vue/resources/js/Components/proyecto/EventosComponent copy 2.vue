<template>
    <div class="eventos-component">
        <button @click="mostrarConfiguracion = true" class="button is-info">Configurar Tabla</button>

        <!-- Modal de Configuración de Columnas -->
        <div v-if="mostrarConfiguracion" class="modal is-active">
            <div class="modal-background" @click="mostrarConfiguracion = false"></div>
            <div class="modal-content">
                <div class="box">
                    <h3 class="title">Configurar Columnas</h3>
                    <ul>
                        <li v-for="(columna, index) in opcionesColumnas" :key="columna.clave">
                            <input type="checkbox" v-model="columna.visible" />
                            <input type="text" v-model="columna.titulo" placeholder="Nombre de columna" />
                            <button @click="moverColumna(index, -1)" :disabled="index === 0">↑</button>
                            <button @click="moverColumna(index, 1)"
                                :disabled="index === opcionesColumnas.length - 1">↓</button>
                        </li>
                    </ul>
                    <button @click="guardarConfiguracion" class="button is-success">Guardar Configuración</button>
                </div>
            </div>
        </div>

        <!-- Tabla de eventos -->
        <table class="table is-striped">
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
</template>

<script>
import axios from 'axios';
import Cookies from 'js-cookie';

export default {
    data() {
        return {
            mostrarConfiguracion: false,
            eventosFiltrados: [],
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
            ]
        };
    },
    computed: {
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
        moverColumna(index, direccion) {
            const newIndex = index + direccion;
            if (newIndex >= 0 && newIndex < this.opcionesColumnas.length) {
                const temp = this.opcionesColumnas[index];
                this.opcionesColumnas[index] = this.opcionesColumnas[newIndex];
                this.opcionesColumnas[newIndex] = temp;
            }
        },
        guardarConfiguracion() {
            Cookies.set('configuracionColumnas', JSON.stringify(this.opcionesColumnas), { expires: 7 });
            this.mostrarConfiguracion = false;
        },
        cargarConfiguracion() {
            const configuracionGuardada = Cookies.get('configuracionColumnas');
            if (configuracionGuardada) {
                this.opcionesColumnas = JSON.parse(configuracionGuardada);
            }
        }
    },
    mounted() {
        this.cargarDatos();
        this.cargarConfiguracion();
    }
};
</script>

<style scoped>
.eventos-component {
    display: flex;
    flex-direction: column;
}

.modal.is-active {
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
