<template>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Nivel</th>
          <th>Facultad</th>
          <th>Especialidad</th>
          <th>Periodo</th>
          <th>Código</th>
          <th>Estado</th>
          <th>Medio</th>
          <th>Responsable</th>
          <th>Procesa</th>
          <th>Observación</th>
          <th>Descripción</th>
          <th>Fecha Inicio</th>
          <th>Fecha Fin</th>
          <th>Tipo Actividad</th>
          <th>Todo el Día</th>
          <th>Color</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="dato in datos" :key="dato.id_actividad">
          <td>{{ dato.id_actividad || 'No disponible' }}</td>
          <td>{{ dato.nombre_actividad || 'No disponible' }}</td>
          <td>{{ dato.jerarquia?.nivel || 'No disponible' }}</td>
          <td>{{ dato.facultad || 'No disponible' }}</td>
          <td>{{ dato.especialidad || 'No disponible' }}</td>
          <td>{{ dato.periodo || 'No disponible' }}</td>
          <td>{{ dato.codigo_actividad || 'No disponible' }}</td>
          <td>{{ dato.estado || 'No disponible' }}</td>
          <td>{{ dato.medio?.nombre || 'No disponible' }}</td>
          <td>{{ dato.responsable?.nombre || 'No disponible' }}</td>
          <td>{{ dato.procesa?.nombre || 'No disponible' }}</td>
          <td>{{ dato.observacion || 'No disponible' }}</td>
          <td>{{ dato.fullcalendar?.descripcion || 'No disponible' }}</td>
          <td>{{ dato.fullcalendar?.fecha_inicio || 'No disponible' }}</td>
          <td>{{ dato.fullcalendar?.fecha_fin || 'No disponible' }}</td>
          <td>{{ dato.fullcalendar?.tipo_actividad || 'No disponible' }}</td>
          <td>{{ dato.fullcalendar?.todo_el_dia === 'S' ? 'Sí' : 'No' }}</td>
          <td>
            <div
              :style="{ backgroundColor: dato.fullcalendar?.color || '#FFFFFF', height: '20px', width: '20px', borderRadius: '50%' }"
            ></div>
          </td>
        </tr>
      </tbody>
    </table>
  </template>

  <script>
  export default {
    props: {
      datos: {
        type: Array,
        required: true,
      },
    },
  };
  </script>

  <style>
  table {
    width: 100%;
    border-collapse: collapse;
    table-layout: auto;
  }

  th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
  }

  th {
    background-color: #f2f2f2;
  }

  tbody tr:nth-child(even) {
    background-color: #f9f9f9;
  }

  tbody tr:hover {
    background-color: #f1f1f1;
  }

  div[style*="backgroundColor"] {
    display: inline-block;
    border: 1px solid #ccc;
  }
  </style>
