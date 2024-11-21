<template>
    <div class="eventos-component columns">
      <!-- Barra lateral de filtros -->
      <div class="column is-one-quarter">
        <aside class="menu">
          <div class="config-header">
            <p class="menu-label">Filtros</p>
            <span @click="mostrarConfiguracion = true" class="config-icon">⚙️</span>
          </div>
          <ul class="menu-list">
            <li v-for="filtro in filtrosVisibles" :key="filtro.clave">
              <label>{{ filtro.titulo }}</label>
              <input v-if="filtro.tipo === 'text'" type="text" class="input is-small" v-model="valoresFiltros[filtro.clave]" @input="aplicarFiltros" />
              <select v-if="filtro.tipo === 'select'" class="select is-small" v-model="valoresFiltros[filtro.clave]" @change="aplicarFiltros">
                <option v-for="opcion in filtro.opciones" :key="opcion" :value="opcion">{{ opcion }}</option>
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
                <li :class="{ 'is-active': configuracionActiva === 'filtros' }" @click="configuracionActiva = 'filtros'">
                  <a>Filtros</a>
                </li>
                <li :class="{ 'is-active': configuracionActiva === 'columnas' }" @click="configuracionActiva = 'columnas'">
                  <a>Columnas</a>
                </li>
              </ul>
            </div>
            <div v-if="configuracionActiva === 'filtros'">
              <ul>
                <li v-for="filtro in opcionesFiltros" :key="filtro.clave" class="config-item">
                  <input type="checkbox" v-model="filtro.visible" class="checkbox" />
                  <input type="text" v-model="filtro.titulo" class="input is-small" placeholder="Nombre del filtro" />
                  <select v-model="filtro.tipo" class="select is-small">
                    <option value="text">Texto</option>
                    <option value="select">Seleccionar</option>
                  </select>
                </li>
              </ul>
              <button @click="guardarConfiguracionFiltros" class="button is-success is-small">Guardar Filtros</button>
            </div>
            <div v-if="configuracionActiva === 'columnas'">
              <ul>
                <li v-for="(columna, index) in opcionesColumnas" :key="columna.clave" class="config-item">
                  <input type="checkbox" v-model="columna.visible" class="checkbox" />
                  <input type="text" v-model="columna.titulo" class="input is-small" placeholder="Nombre de columna" />
                  <button @click="moverColumna(index, -1)" :disabled="index === 0" class="button is-small">↑</button>
                  <button @click="moverColumna(index, 1)" :disabled="index === opcionesColumnas.length - 1" class="button is-small">↓</button>
                </li>
              </ul>
              <button @click="guardarConfiguracionColumnas" class="button is-success is-small">Guardar Columnas</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de eventos -->
      <div class="column">
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
        ]
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
      cargarConfiguracion() {
        const filtrosGuardados = Cookies.get('configuracionFiltros');
        const columnasGuardadas = Cookies.get('configuracionColumnas');
        if (filtrosGuardados) this.opcionesFiltros = JSON.parse(filtrosGuardados);
        if (columnasGuardadas) this.opcionesColumnas = JSON.parse(columnasGuardadas);
      },
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
  }
  .config-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .config-icon {
    cursor: pointer;
    font-size: 1.2rem;
    color: #3273dc; /* Color azul */
  }
  .config-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .modal.is-active {
    display: flex;
    align-items: center;
    justify-content: center;
  }
  </style>
