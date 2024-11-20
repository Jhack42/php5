<template>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Categoría</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="dato in datos" :key="dato.id">
          <td>{{ dato.id }}</td>
          <td>{{ dato.nombre }}</td>
          <td>{{ dato.categoria }}</td>
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
