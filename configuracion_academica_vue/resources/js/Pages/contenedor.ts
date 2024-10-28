import { markRaw, DefineComponent } from 'vue';
import VistaDefault from './php5/VistaDefault.vue';
import VistaAlternativa from './php5/VistaAlternativa.vue';

export function obtenerVista(predeterminado: boolean): DefineComponent {
    return predeterminado ? markRaw(VistaDefault) : markRaw(VistaAlternativa);
}
