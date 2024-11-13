import { markRaw, DefineComponent } from 'vue';
import VistaTabla from './php5/VistaTabla.vue';
import VistaDefault from './php5/VistaDefault.vue';
import VistaCalendario from './php5/VistaCalendario.vue';

export function obtenerVista(predeterminado: boolean): DefineComponent {
    return predeterminado ? markRaw(VistaDefault) : markRaw(VistaCalendario); markRaw(VistaTabla);
}
