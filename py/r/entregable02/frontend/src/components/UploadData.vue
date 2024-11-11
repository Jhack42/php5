<template>
    <div>
      <h2>Cargar Datos desde Archivo CSV</h2>
      <form @submit.prevent="uploadData">
        <input type="file" @change="handleFileUpload" accept=".csv" />
        <button type="submit" :disabled="!file">Cargar Datos</button>
      </form>
      <div v-if="data.length">
        <h3>Datos Cargados:</h3>
        <table>
          <thead>
            <tr>
              <th v-for="(value, key) in data[0]" :key="key">{{ key }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, index) in data" :key="index">
              <td v-for="value in row" :key="value">{{ value }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        file: null,
        data: []
      };
    },
    methods: {
      handleFileUpload(event) {
        this.file = event.target.files[0];
      },
      async uploadData() {
        const formData = new FormData();
        formData.append('file', this.file);
  
        try {
          const response = await this.$axios.post('/upload_data', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          });
          this.data = response.data;
        } catch (error) {
          console.error("Error al cargar el archivo:", error);
        }
      }
    }
  };
  </script>
  
  <style scoped>
  /* Agrega aquí estilos personalizados si es necesario */
  </style>
  