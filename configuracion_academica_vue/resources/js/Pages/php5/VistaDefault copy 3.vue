<template>
    <div class="container">
      <div v-if="columnas.length" class="columnas">
        <!-- Iterar sobre cada columna de tarjetas -->
        <div v-for="(columna, index) in columnas" :key="index" class="columna">
          <!-- Mostrar cada tarjeta en la columna -->
          <div v-for="tarjeta in columna" :key="tarjeta.id" class="card">
            <div class="imagen">
              <img :src="tarjeta.imagen_del_card" alt="Imagen de Facultad" />
            </div>
            <div class="title-container">
              <img :src="tarjeta.imagen_logo" alt="Icono" />
              <h1>
                <span v-for="letra in tarjeta.name_abreviado" :key="letra.id" :style="letra.css">
                  {{ letra.letra }}
                </span>
              </h1>
            </div>
            <p>{{ tarjeta.name_completo }}</p>
            <div class="divider"></div>
            <p class="department" v-for="especialidad in tarjeta.especialidades" :key="especialidad.id">
              • {{ especialidad.name }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    name: 'VistaDefault',
    data() {
      return {
        tarjetas: [],
        columnas: []
      };
    },
    created() {
      this.fetchTarjetas();
    },
    methods: {
      async fetchTarjetas() {
        try {
          const response = await fetch('http://localhost:3000/api/data/propuesta');
          this.tarjetas = await response.json();
          this.organizarEnColumnas(3); // Cambia a la cantidad de columnas deseada
        } catch (error) {
          console.error('Error al cargar las tarjetas:', error);
        }
      },
      organizarEnColumnas(numColumnas) {
        const columnas = Array.from({ length: numColumnas }, () => []);
        this.tarjetas.forEach((tarjeta, index) => {
          columnas[index % numColumnas].push(tarjeta);
        });
        this.columnas = columnas;
      }
    }
  };
  </script>

  <style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@200&family=Julius+Sans+One&display=swap');

  .container {
    display: flex;
    justify-content: center;
    gap: 20px;
  }

  .columnas {
    display: flex;
    gap: 20px;
  }

  .columna {
    display: flex;
    flex-direction: column;
    gap: 20px;
    flex: 1;
  }

  .card {
    background-color: #DCDBDC;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    text-align: center;
    padding-bottom: 20px;
  }

  .imagen {
    padding: 0.5rem;
  }

  .card img {
    width: 100%;
    height: auto;
    border-radius: 8px;
  }

  .title-container {
    position: relative;
    display: inline-block;
  }

  .title-container img {
    position: absolute;
    left: -30px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
  }

  .card h1 {
    font-family: 'Nunito', sans-serif;
    font-weight: 200;
    font-size: 2em;
    margin: 0 0 5px;
  }

  .card p {
    font-family: 'Julius Sans One', sans-serif;
    font-size: 0.9em;
    color: #333333;
    margin: 0;
    line-height: 1.5;
  }

  .card .divider {
    width: 80%;
    height: 1px;
    background-color: #ffffff;
    margin: 10px auto;
  }

  .card .department {
    font-family: Arial, sans-serif;
    font-size: 0.9em;
    font-style: italic;
    color: #333333;
    margin-top: 10px;
  }
  </style>
