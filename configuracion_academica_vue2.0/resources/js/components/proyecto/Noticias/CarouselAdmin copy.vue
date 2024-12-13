<!-- CarouselAdmin.vue -->
<template>
    <div class="admin-container p-4">
      <h2 class="text-2xl font-bold mb-4">Administrar Carrusel</h2>

      <!-- Formulario para agregar/editar slides -->
      <form @submit.prevent="saveSlide" class="mb-8 bg-white p-4 rounded-lg shadow">
        <div class="mb-4">
          <label class="block text-gray-700 mb-2">Título:</label>
          <input
            v-model="currentSlide.title"
            class="w-full p-2 border rounded"
            required
          >
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 mb-2">Descripción:</label>
          <textarea
            v-model="currentSlide.description"
            class="w-full p-2 border rounded"
            rows="3"
            required
          ></textarea>
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 mb-2">Fecha:</label>
          <input
            type="date"
            v-model="currentSlide.date"
            class="w-full p-2 border rounded"
            required
          >
        </div>

        <button
          type="submit"
          class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
        >
          {{ editingIndex === null ? 'Agregar' : 'Actualizar' }}
        </button>
      </form>

      <!-- Lista de slides existentes -->
      <div class="slides-list">
        <div
          v-for="(slide, index) in slides"
          :key="index"
          class="slide-item bg-white p-4 rounded-lg shadow mb-4"
        >
          <h3 class="font-bold">{{ slide.title }}</h3>
          <p class="text-gray-600">{{ slide.description }}</p>
          <p class="text-sm text-gray-500">{{ slide.date }}</p>

          <div class="mt-4 flex gap-2">
            <button
              @click="editSlide(index)"
              class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600"
            >
              Editar
            </button>
            <button
              @click="deleteSlide(index)"
              class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
            >
              Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    name: 'CarouselAdmin',
    data() {
      return {
        slides: [],
        currentSlide: {
          title: '',
          description: '',
          date: ''
        },
        editingIndex: null
      }
    },
    methods: {
      async fetchSlides() {
        try {
          // Aquí conectarías con tu backend
          const response = await fetch('/api/slides')
          this.slides = await response.json()
        } catch (error) {
          console.error('Error al cargar las diapositivas:', error)
        }
      },
      async saveSlide() {
        try {
          const slideData = { ...this.currentSlide }

          if (this.editingIndex === null) {
            // Agregar nuevo slide
            await fetch('/api/slides', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify(slideData)
            })
          } else {
            // Actualizar slide existente
            await fetch(`/api/slides/${this.editingIndex}`, {
              method: 'PUT',
              headers: {
                'Content-Type': 'application/json'
              },
              body: JSON.stringify(slideData)
            })
          }

          await this.fetchSlides()
          this.resetForm()
        } catch (error) {
          console.error('Error al guardar la diapositiva:', error)
        }
      },
      editSlide(index) {
        this.editingIndex = index
        this.currentSlide = { ...this.slides[index] }
      },
      async deleteSlide(index) {
        if (confirm('¿Estás seguro de que deseas eliminar esta diapositiva?')) {
          try {
            await fetch(`/api/slides/${index}`, {
              method: 'DELETE'
            })
            await this.fetchSlides()
          } catch (error) {
            console.error('Error al eliminar la diapositiva:', error)
          }
        }
      },
      resetForm() {
        this.currentSlide = {
          title: '',
          description: '',
          date: ''
        }
        this.editingIndex = null
      }
    },
    mounted() {
      this.fetchSlides()
    }
  }
  </script>
