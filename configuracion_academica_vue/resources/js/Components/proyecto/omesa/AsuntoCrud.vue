<template>
  <div>
    <h1>Test CRUD Categorías a</h1>
    <div>
      <input v-model="newCategory" placeholder="Nueva Categoría" :disabled="isSubmitting">
      <button @click="createCategory" :disabled="!newCategory.trim() || isSubmitting">Crear Categoría</button>
    </div>
    <div v-for="category in categories" :key="category.n_idasunto">
      <input v-model="category.v_descripcion">
      <button @click="updateCategory(category)" :disabled="isUpdating">Actualizar</button>
      <button @click="deleteCategory(category.n_idasunto)" :disabled="isDeleting">Eliminar</button>
    </div>
    <div v-if="message" :class="messageClass">{{ message }}</div>
    <div v-if="pagination" class="pagination">
      <button :disabled="!pagination.prev_page_url" @click="fetchCategories(pagination.prev_page_url)">&laquo; Previous</button>
      <button v-for="link in pagination.links" :key="link.label" :disabled="link.active || !link.url" @click="fetchCategories(link.url)">
        {{ link.label }}
      </button>
      <button :disabled="!pagination.next_page_url" @click="fetchCategories(pagination.next_page_url)">Next &raquo;</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      categories: [],
      newCategory: '',
      message: '',
      messageClass: '',
      pagination: null,
      isSubmitting: false,
      isUpdating: false,
      isDeleting: false,
    };
  },
  methods: {
    async fetchCategories(url = '/api/SmtrAsunto') {
      try {
        const response = await axios.get(url);
        this.categories = response.data;
      } catch (error) {
        this.showMessage('Error al cargar categorías: ' + error.message, 'error');
      }
    },
    async createCategory() {
      this.isSubmitting = true;
      try {
        const response = await axios.post('/api/SmtrAsunto', { v_descripcion: this.newCategory });
        this.showMessage('Categoría creada exitosamente', 'success');
        this.fetchCategories();
        this.newCategory = '';
      } catch (error) {
        this.showMessage('Error al crear categoría: ' + error.message, 'error');
      } finally {
        this.isSubmitting = false;
      }
    },
    async updateCategory(category) {
      this.isUpdating = true;
      try {
        const response = await axios.put(`/api/SmtrAsunto/${category.n_idasunto}`, { v_descripcion: category.v_descripcion });
        this.showMessage('Categoría actualizada exitosamente', 'success');
      } catch (error) {
        this.showMessage('Error al actualizar categoría: ' + error.message, 'error');
      } finally {
        this.isUpdating = false;
      }
    },
    async deleteCategory(id) {
      this.isDeleting = true;
      try {
        await axios.delete(`/api/SmtrAsunto/${id}`);
        this.categories = this.categories.filter(category => category.n_idasunto !== id);
        this.showMessage('Categoría eliminada correctamente', 'success');
      } catch (error) {
        this.showMessage('Error al eliminar categoría: ' + error.message, 'error');
      } finally {
        this.isDeleting = false;
      }
    },
    showMessage(message, type) {
      this.message = message;
      this.messageClass = type === 'success' ? 'success' : 'error';
      setTimeout(() => {
        this.message = '';
        this.messageClass = '';
      }, 3000);
    }
  },
  mounted() {
    this.fetchCategories();
  }
};
</script>

<style>
.success, .error {
  padding: 10px;
  margin-top: 10px;
}
.success {
  color: green;
}
.error {
  color: red;
}
.pagination {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}
.pagination button {
  margin: 0 5px;
  padding: 5px 10px;
  border: 1px solid #ddd;
  background-color: #fff;
  cursor: pointer;
}
.pagination button:disabled {
  cursor: not-allowed;
  opacity: 0.5;
}
</style>
