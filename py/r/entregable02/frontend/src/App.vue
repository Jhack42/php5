<template>
  <div id="app">
    <h1>Sistema de Generación y Entrenamiento de Datos</h1>
    <GenerateData />
    <UploadData />
    <TrainConfig />
  </div>
</template>

<script>
import GenerateData from './components/GenerateData.vue';
import UploadData from './components/UploadData.vue';
import TrainConfig from './components/TrainConfig.vue';

export default {
  components: {
    GenerateData,
    UploadData,
    TrainConfig
  }
};
</script>

<style>
/* Estilos generales para la aplicación */
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
