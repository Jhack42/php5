<template>
  <div>
    <h2>Generar Datos</h2>
    <form @submit.prevent="generateData">
      <div>
        <label for="formula">Fórmula:</label>
        <input v-model="formula" id="formula" type="text" placeholder="Ej: a + b**2 + sqrt(c)" />
      </div>
      <div>
        <label for="variables">Variables (separadas por coma):</label>
        <input v-model="variables" id="variables" type="text" placeholder="Ej: a, b, c" />
      </div>
      <button type="submit">Generar Datos</button>
    </form>
    <div v-if="data.length">
      <h3>Datos Generados:</h3>
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
      formula: '',
      variables: '',
      data: []
    };
  },
  methods: {
    async generateData() {
      try {
        const response = await this.$axios.post('/generate_data', {
          formula: this.formula,
          variables: this.variables.split(',').map(v => v.trim())
        });
        this.data = response.data;
      } catch (error) {
        console.error("Error generando datos:", error);
      }
    }
  }
};
</script>

<style scoped>
/* Añade aquí estilos personalizados para el componente */
</style>
