<template>
    <div class="carousel-design-editor">
      <div class="design-header">
        <h2>Editor de Diseño del Carrusel</h2>
        <div class="header-actions">
          <button @click="loadCarouselContent" class="load-btn">Cargar Contenido</button>
          <button @click="saveDesign" class="save-btn">Guardar Diseño</button>
        </div>
      </div>

      <div class="design-container">
        <!-- Panel de configuración -->
        <div class="design-panel">
          <div class="panel-section">
            <h3>Dimensiones</h3>
            <div class="input-group">
              <label>Tipo de Ancho:</label>
              <select v-model="design.width_type">
                <option value="responsive">Responsive</option>
                <option value="fixed">Fijo</option>
                <option value="fluid">Fluido</option>
              </select>
            </div>

            <div v-if="design.width_type === 'fixed'" class="input-group">
              <label>Ancho:</label>
              <input type="text" v-model="design.width_value" placeholder="ej: 1200px">
            </div>

            <div class="input-group">
              <label>Tipo de Alto:</label>
              <select v-model="design.height_type">
                <option value="fixed">Fijo</option>
                <option value="aspect-ratio">Relación de Aspecto</option>
              </select>
            </div>

            <div class="input-group">
              <label>Alto:</label>
              <input type="text" v-model="design.height_value" :placeholder="heightPlaceholder">
            </div>
          </div>

          <div class="panel-section">
            <h3>Espaciado</h3>
            <div class="input-group">
              <label>Margen Superior:</label>
              <input type="text" v-model="design.margin_top" placeholder="ej: 20px">
            </div>
            <div class="input-group">
              <label>Margen Inferior:</label>
              <input type="text" v-model="design.margin_bottom" placeholder="ej: 20px">
            </div>
            <div class="input-group">
              <label>Padding:</label>
              <input type="text" v-model="design.padding" placeholder="ej: 20px">
            </div>
          </div>

          <div class="panel-section">
            <h3>Fondo</h3>
            <div class="input-group">
              <label>Tipo de Fondo:</label>
              <select v-model="design.background_type">
                <option value="color">Color</option>
                <option value="image">Imagen</option>
                <option value="video">Video</option>
              </select>
            </div>

            <!-- Color de fondo -->
            <div v-if="design.background_type === 'color'" class="input-group">
              <label>Color:</label>
              <div class="color-picker">
                <input type="color" v-model="design.background_color">
                <input type="checkbox" v-model="design.is_color_active"> Activo
              </div>
            </div>

            <!-- Imagen de fondo -->
            <div v-if="design.background_type === 'image'" class="input-group">
              <label>Imagen:</label>
              <input type="file" @change="handleImageUpload" accept="image/*">
              <div v-if="design.background_image" class="preview-image">
                <img :src="design.background_image" alt="Background preview">
                <input type="checkbox" v-model="design.is_image_active"> Activo
              </div>
            </div>

            <!-- Video de fondo -->
            <div v-if="design.background_type === 'video'" class="input-group">
              <label>Video:</label>
              <input type="file" @change="handleVideoUpload" accept="video/*">
              <div v-if="design.background_video" class="preview-video">
                <video :src="design.background_video" controls></video>
                <input type="checkbox" v-model="design.is_video_active"> Activo
              </div>
            </div>

            <!-- Overlay -->
            <div class="input-group">
              <label>Color de Overlay:</label>
              <input type="color" v-model="design.overlay_color">
              <input type="range" v-model="design.overlay_opacity" min="0" max="1" step="0.1">
            </div>
          </div>

          <div class="panel-section">
            <h3>Estilos Adicionales</h3>
            <div class="input-group">
              <label>Border Radius:</label>
              <input type="text" v-model="design.border_radius" placeholder="ej: 10px">
            </div>
            <div class="input-group">
              <label>Box Shadow:</label>
              <input type="text" v-model="design.box_shadow" placeholder="ej: 0 2px 4px rgba(0,0,0,0.1)">
            </div>
          </div>

          <div class="panel-section">
            <h3>CSS Personalizado</h3>
            <textarea v-model="design.custom_css" placeholder="Ingrese CSS personalizado aquí"></textarea>
          </div>

          <div class="panel-section">
            <h3>Transiciones</h3>
            <div class="input-group">
              <label>Efecto:</label>
              <select v-model="design.transition_effect">
                <option value="fade">Fade</option>
                <option value="slide">Slide</option>
                <option value="zoom">Zoom</option>
              </select>
            </div>
            <div class="input-group">
              <label>Duración:</label>
              <input type="text" v-model="design.transition_duration" placeholder="ej: 0.5s">
            </div>
          </div>
        </div>

        <!-- Preview en tiempo real -->
        <div class="design-preview">
          <div class="preview-container" :style="previewStyles">
            <div v-if="selectedContent" v-html="selectedContent.html_content"></div>
            <div v-else class="preview-placeholder">
              Seleccione contenido del carrusel para previsualizar
              <div class="preview-dimensions">
                {{ previewDimensions }}
              </div>
            </div>
          </div>

          <!-- Lista de contenidos del carrusel -->
          <div class="carousel-content-list">
            <h3>Contenido del Carrusel</h3>
            <div v-for="item in carouselContents" :key="item.id"
                 class="content-item"
                 :class="{ active: selectedContent?.id === item.id }"
                 @click="selectContent(item)">
              <div class="content-name">{{ item.name }}</div>
              <div class="content-description">{{ item.description }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  import { ref, computed, onMounted } from 'vue';
  import axios from 'axios';

  export default {
    name: 'PreviewFinal',

    setup() {
      const design = ref({
        name: '',
        width_type: 'responsive',
        width_value: '',
        height_type: 'fixed',
        height_value: '400px',
        margin_top: '0px',
        margin_bottom: '0px',
        padding: '0px',
        background_type: 'color',
        background_color: '#ffffff',
        is_color_active: true,
        background_image: '',
        is_image_active: false,
        background_video: '',
        is_video_active: false,
        overlay_color: '#000000',
        overlay_opacity: 0,
        border_radius: '0px',
        box_shadow: '',
        custom_css: '',
        transition_effect: 'fade',
        transition_duration: '0.5s',
        is_responsive: true,
        active: true
      });

      const carouselContents = ref([]);
      const selectedContent = ref(null);

      const heightPlaceholder = computed(() => {
        return design.value.height_type === 'fixed' ? 'ej: 400px' : 'ej: 16/9';
      });

      const previewStyles = computed(() => {
        const styles = {
          width: design.value.width_type === 'fixed' ? design.value.width_value : '100%',
          height: design.value.height_value,
          marginTop: design.value.margin_top,
          marginBottom: design.value.margin_bottom,
          padding: design.value.padding,
          borderRadius: design.value.border_radius,
          boxShadow: design.value.box_shadow,
          position: 'relative'
        };

        if (design.value.background_type === 'color' && design.value.is_color_active) {
          styles.backgroundColor = design.value.background_color;
        }

        if (design.value.background_type === 'image' && design.value.is_image_active) {
          styles.backgroundImage = `url(${design.value.background_image})`;
          styles.backgroundSize = 'cover';
          styles.backgroundPosition = 'center';
        }

        return styles;
      });

      const previewDimensions = computed(() => {
        return `${design.value.width_type === 'fixed' ? design.value.width_value : 'responsive'} x ${design.value.height_value}`;
      });

      async function loadCarouselContent() {
        try {
          const response = await axios.get('/api/v1/carousel');
          carouselContents.value = response.data.data;
        } catch (error) {
          console.error('Error al cargar contenido del carrusel:', error);
        }
      }

      async function loadDesigns() {
        try {
          const response = await axios.get('/api/carousel-design');
          if (response.data.data.length > 0) {
            design.value = { ...design.value, ...response.data.data[0] };
          }
        } catch (error) {
          console.error('Error al cargar diseños:', error);
        }
      }

      function selectContent(content) {
        selectedContent.value = content;
      }

      async function handleImageUpload(event) {
        const file = event.target.files[0];
        if (file) {
          const formData = new FormData();
          formData.append('image', file);

          try {
            const response = await axios.post('/api/carousel-design/upload-image', formData);
            design.value.background_image = response.data.url;
            design.value.is_image_active = true;
          } catch (error) {
            console.error('Error al subir imagen:', error);
          }
        }
      }

      async function handleVideoUpload(event) {
        const file = event.target.files[0];
        if (file) {
          const formData = new FormData();
          formData.append('video', file);

          try {
            const response = await axios.post('/api/carousel-design/upload-video', formData);
            design.value.background_video = response.data.url;
            design.value.is_video_active = true;
          } catch (error) {
            console.error('Error al subir video:', error);
          }
        }
      }

      async function saveDesign() {
        try {
          const response = await axios.post('/api/carousel-design', design.value);
          console.log('Diseño guardado:', response.data);
        } catch (error) {
          console.error('Error al guardar diseño:', error);
        }
      }

      onMounted(() => {
        loadDesigns();
        loadCarouselContent();
      });

      return {
        design,
        carouselContents,
        selectedContent,
        heightPlaceholder,
        previewStyles,
        previewDimensions,
        handleImageUpload,
        handleVideoUpload,
        saveDesign,
        loadCarouselContent,
        selectContent
      };
    }
  };
  </script>

<style lang="scss" src="../../../../scss/preview_final.scss"></style>
