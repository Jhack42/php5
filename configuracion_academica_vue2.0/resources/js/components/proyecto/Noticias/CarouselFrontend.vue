<!-- CarouselFrontend.vue -->
<template>
    <div class="carousel-container">
      <!-- Carrusel principal -->
      <div class="carousel-content relative overflow-hidden">
        <div class="carousel-slides flex transition-transform duration-500" :style="slideStyle">
          <div v-for="(slide, index) in slides" :key="index" class="carousel-slide w-full flex-shrink-0">
            <div class="p-4 bg-white shadow-lg rounded-lg m-2">
              <h3 class="text-xl font-bold mb-2">{{ slide.title }}</h3>
              <p class="text-gray-600">{{ slide.description }}</p>
              <p class="text-sm text-gray-500 mt-2">{{ slide.date }}</p>
            </div>
          </div>
        </div>

        <!-- Botones de navegación -->
        <button @click="prevSlide" class="carousel-control left-2 bg-white/80 hover:bg-white">&larr;</button>
        <button @click="nextSlide" class="carousel-control right-2 bg-white/80 hover:bg-white">&rarr;</button>

        <!-- Indicadores -->
        <div class="carousel-indicators">
          <button
            v-for="(_, index) in slides"
            :key="index"
            @click="goToSlide(index)"
            :class="['indicator-dot', { active: currentSlide === index }]"
          ></button>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    name: 'CarouselFrontend',
    data() {
      return {
        slides: [],
        currentSlide: 0,
        autoplayInterval: null
      }
    },
    computed: {
      slideStyle() {
        return {
          transform: `translateX(-${this.currentSlide * 100}%)`
        }
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
      nextSlide() {
        this.currentSlide = (this.currentSlide + 1) % this.slides.length
      },
      prevSlide() {
        this.currentSlide = this.currentSlide === 0
          ? this.slides.length - 1
          : this.currentSlide - 1
      },
      goToSlide(index) {
        this.currentSlide = index
      },
      startAutoplay() {
        this.autoplayInterval = setInterval(this.nextSlide, 5000)
      },
      stopAutoplay() {
        clearInterval(this.autoplayInterval)
      }
    },
    mounted() {
      this.fetchSlides()
      this.startAutoplay()
    },
    beforeUnmount() {
      this.stopAutoplay()
    }
  }
  </script>

  <style scoped>
  .carousel-container {
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
  }

  .carousel-content {
    position: relative;
    overflow: hidden;
  }

  .carousel-control {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    padding: 1rem;
    border-radius: 50%;
    border: none;
    cursor: pointer;
  }

  .carousel-indicators {
    position: absolute;
    bottom: 1rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 0.5rem;
  }

  .indicator-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
    cursor: pointer;
  }

  .indicator-dot.active {
    background-color: white;
  }
  </style>
