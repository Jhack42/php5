<template>
    <div class="tarjetas-container">
      <!-- Tarjeta 1 -->
      <div class="card">
        <div class="card-image" @click="seleccionarFacultad('Arquitectura, Urbanismo y Artes')">
          <img
            src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/UNI_Pabell%C3%B3n_Central_2017.jpg-uFgAnnnUJyK8hkO7JL4iTXzfJTpoEB.jpeg"
            alt="Arquitectura"
          />
        </div>
        <div class="card-footer">
          <div class="card-title">
            <i class="lucide-graduation-cap"></i>
            <span class="card-acronym">Arquitectura, Urbanismo y Artes</span>
          </div>
          <ul class="card-courses">
            <li @click.stop="seleccionarEspecialidad('Arquitectura')">Arquitectura</li>
          </ul>
        </div>
      </div>

      <!-- Tarjeta 2 -->
      <div class="card">
        <div class="card-image" @click="seleccionarFacultad('Ingeniería Civil')">
          <img
            src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/UNI_Pabell%C3%B3n_Central_2017.jpg-uFgAnnnUJyK8hkO7JL4iTXzfJTpoEB.jpeg"
            alt="Ingeniería Civil"
          />
        </div>
        <div class="card-footer">
          <div class="card-title">
            <i class="lucide-graduation-cap"></i>
            <span class="card-acronym">Ingeniería Civil</span>
          </div>
          <ul class="card-courses">
            <li @click.stop="seleccionarEspecialidad('Ingeniería Civil')">Ingeniería Civil</li>
          </ul>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    methods: {
      seleccionarFacultad(facultad) {
        // Emitir el evento para seleccionar una facultad
        this.$emit('seleccionar-tarjeta', facultad);
      },
      seleccionarEspecialidad(especialidad) {
        // Emitir el evento para seleccionar una especialidad
        this.$emit('seleccionar-especialidad', especialidad);
      },
    },
  };
  </script>

  <style lang="scss" src="./styles.scss"></style>
