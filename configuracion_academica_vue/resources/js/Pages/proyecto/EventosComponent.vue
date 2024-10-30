<template>
    <div class="eventos-component columns">
      <!-- Barra lateral de filtros -->
      <div class="column is-one-quarter">
        <aside class="menu">
          <p class="menu-label">Filtros</p>
          <ul class="menu-list">
            <li v-for="filtro in filtrosVisibles" :key="filtro.clave">
              <label>{{ filtro.titulo }}</label>
              <input v-if="filtro.tipo === 'text'" type="text" v-model="valoresFiltros[filtro.clave]" @input="aplicarFiltros" />
              <select v-if="filtro.tipo === 'select'" v-model="valoresFiltros[filtro.clave]" @change="aplicarFiltros">
                <option v-for="opcion in filtro.opciones" :key="opcion" :value="opcion">{{ opcion }}</option>
              </select>
            </li>
          </ul>
          <button @click="mostrarConfiguracionFiltros = true" class="button is-info is-small">Editar Filtros</button>
        </aside>
      </div>

      <!-- Configuración de Filtros (Modal) -->
      <div v-if="mostrarConfiguracionFiltros" class="modal is-active">
        <div class="modal-background" @click="mostrarConfiguracionFiltros = false"></div>
        <div class="modal-content">
          <div class="box">
            <h3 class="title">Configurar Filtros</h3>
            <ul>
              <li v-for="filtro in opcionesFiltros" :key="filtro.clave">
                <input type="checkbox" v-model="filtro.visible" />
                <input type="text" v-model="filtro.titulo" placeholder="Nombre del filtro" />
                <select v-model="filtro.tipo">
                  <option value="text">Texto</option>
                  <option value="select">Seleccionar</option>
                </select>
              </li>
            </ul>
            <button @click="guardarConfiguracionFiltros" class="button is-success">Guardar Filtros</button>
          </div>
        </div>
      </div>

      <!-- Configuración de Columnas (Modal) -->
      <div v-if="mostrarConfiguracionColumnas" class="modal is-active">
        <div class="modal-background" @click="mostrarConfiguracionColumnas = false"></div>
        <div class="modal-content">
          <div class="box">
            <h3 class="title">Configurar Columnas</h3>
            <ul>
              <li v-for="(columna, index) in opcionesColumnas" :key="columna.clave">
                <input type="checkbox" v-model="columna.visible" />
                <input type="text" v-model="columna.titulo" placeholder="Nombre de columna" />
                <button @click="moverColumna(index, -1)" :disabled="index === 0">↑</button>
                <button @click="moverColumna(index, 1)" :disabled="index === opcionesColumnas.length - 1">↓</button>
              </li>
            </ul>
            <button @click="guardarConfiguracionColumnas" class="button is-success">Guardar Configuración</button>
          </div>
        </div>
      </div>

      <!-- Tabla de eventos -->
      <div class="column">
        <button @click="mostrarConfiguracionColumnas = true" class="button is-info">Configurar Tabla</button>
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
    </div>
  </template>

  <script>
  import axios from 'axios';
  import Cookies from 'js-cookie';

  export default {
    data() {
      return {
        mostrarConfiguracionFiltros: false,
        mostrarConfiguracionColumnas: false,
        eventosFiltrados: [],
        valoresFiltros: {},
        opcionesFiltros: [
          { clave: 'periodo', titulo: 'Periodo', tipo: 'text', visible: true },
          { clave: 'codigo_actividad', titulo: 'Código Actividad', tipo: 'text', visible: true },
          { clave: 'estado', titulo: 'Estado', tipo: 'select', visible: true, opciones: ['A', 'I'] },
          // Más filtros aquí
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
        this.mostrarConfiguracionFiltros = false;
      },
      guardarConfiguracionColumnas() {
        Cookies.set('configuracionColumnas', JSON.stringify(this.opcionesColumnas), { expires: 7 });
        this.mostrarConfiguracionColumnas = false;
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
  .modal.is-active {
    display: flex;
    align-items: center;
    justify-content: center;
  }
  </style>
