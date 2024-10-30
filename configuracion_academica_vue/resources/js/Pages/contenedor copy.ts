import VistaDefault from './php5/VistaDefault.vue';
import VistaAlternativa from './php5/VistaCalendario.vue';

export function obtenerVista(predeterminado: boolean) {
    return predeterminado ? VistaDefault : VistaAlternativa;
}
