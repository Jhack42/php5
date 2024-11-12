// contenedor.ts
import { markRaw, DefineComponent, ref } from 'vue';
import axios from 'axios';
import VistaTabla from './php5/VistaTabla.vue';
import VistaDefault from './php5/VistaDefault.vue';
import VistaCalendario from './php5/VistaCalendario.vue';




// Estados compartidos
const tarjetas = ref([]);
const dataFiltrada = ref([]);
const selectedFilters = ref<string[]>([]);

// Función para cargar tarjetas
export async function fetchTarjetas() {
    try {
        const response = await axios.get('http://localhost:3000/api/data/propuesta');
        tarjetas.value = response.data;
        dataFiltrada.value = tarjetas.value;
    } catch (error) {
        console.error('Error al cargar tarjetas:', error);
    }
}

// Función para construir una carta
export function construirCarta(tarjeta: any) {
    return `
        <div class="card">
            <div class="imagen">
                <img src="${tarjeta.imagen_del_card}" alt="Imagen de Facultad" />
            </div>
            <div class="title-container">
                <img src="${tarjeta.imagen_logo}" alt="Icono" />
                <h1>${tarjeta.name_abreviado.map((letra: any) => `<span style="${letra.css}">${letra.letra}</span>`).join('')}</h1>
            </div>
            <p>${tarjeta.name_completo}</p>
            <div class="divider"></div>
            ${tarjeta.especialidades.map((especialidad: any) => `<p class="department">• ${especialidad.name}</p>`).join('')}
        </div>
    `;
}

// Función para aplicar filtros
export function aplicarFiltro(filtros: string[]) {
    selectedFilters.value = filtros;
    dataFiltrada.value = tarjetas.value.filter((tarjeta: any) =>
        filtros.length === 0 || filtros.some(filtro =>
            tarjeta.especialidades.some((especialidad: any) => especialidad.name === filtro)
        )
    );
}
// Función para obtener una vista según el estado
export function obtenerVista(predeterminado: boolean): DefineComponent {
    return predeterminado ? markRaw(VistaDefault) : markRaw(VistaCalendario);
}

// Exportar referencias reactivas
export { tarjetas, dataFiltrada, selectedFilters };
