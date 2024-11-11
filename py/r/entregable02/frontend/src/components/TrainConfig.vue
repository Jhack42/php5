<template>
    <div>
      <h2>Configuración de Entrenamiento</h2>
      <form @submit.prevent="startTraining">
        <div>
          <label for="model_type">Tipo de Modelo:</label>
          <select v-model="model_type">
            <option value="Neural Network">Red Neuronal</option>
            <option value="Random Forest">Random Forest</option>
          </select>
        </div>
        <div>
          <label for="activation">Función de Activación:</label>
          <select v-model="activation">
            <option>Linear</option>
            <option>Sigmoid</option>
            <option>Tanh</option>
            <option>ReLU</option>
            <!-- Agrega otras opciones aquí -->
          </select>
        </div>
        <button type="submit">Iniciar Entrenamiento</button>
      </form>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        model_type: 'Neural Network',
        activation: 'ReLU'
      };
    },
    methods: {
      async startTraining() {
        try {
          await this.$axios.post('/train', {
            model_type: this.model_type,
            activation: this.activation
          });
          alert("Entrenamiento iniciado");
        } catch (error) {
          console.error("Error iniciando entrenamiento:", error);
        }
      }
    }
  };
  </script>
  
  <style scoped>
  /* Estilos personalizados para este componente */
  </style>
  