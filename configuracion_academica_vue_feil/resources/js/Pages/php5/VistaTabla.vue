<template>
    <div>
        <table class="table is-striped is-fullwidth">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Actividad</th>
                    <th>Facultad</th>
                    <th>Especialidad</th>
                    <th>Periodo</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="actividad in data" :key="actividad.id_actividad">
                    <td>{{ actividad.id_actividad }}</td>
                    <td>{{ actividad.nombre_actividad }}</td>
                    <td>{{ actividad.facultad || 'N/A' }}</td>
                    <td>{{ actividad.especialidad || 'N/A' }}</td>
                    <td>{{ actividad.periodo }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';

export default defineComponent({
    name: 'VistaTabla',
    props: {
        data: {
            type: Array as PropType<any[]>,
            required: true
        }
    }
});
</script>
