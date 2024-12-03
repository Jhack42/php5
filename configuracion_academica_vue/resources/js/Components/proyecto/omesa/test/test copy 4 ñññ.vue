<template>
    <div>
      <h1>Gestión de Categorías</h1>

      <!-- Formulario para agregar o editar una categoría -->
      <div v-if="showForm">
        <input v-model="category.name" placeholder="Nombre de la categoría" />
        <button @click="saveCategory">Guardar</button>
        <button @click="cancelForm">Cancelar</button>
      </div>

      <!-- Tabla de Categorías -->
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="category in categories" :key="category.id">
            <td>{{ category.id }}</td>
            <td>{{ category.name }}</td>
            <td>
              <button @click="editCategory(category)">Editar</button>
              <button @click="deleteCategory(category.id)">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Mensajes de error -->
      <div v-if="errorMessage" class="error-popup">
        <p>{{ errorMessage }}</p>
        <button @click="closeErrorPopup">Cerrar</button>
      </div>

      <!-- Mensajes de éxito -->
      <div v-if="successMessage" class="success-popup">
        <p>{{ successMessage }}</p>
        <button @click="closeSuccessPopup">Cerrar</button>
      </div>
    </div>
  </template>

  <script>
  import axios from 'axios';

  export default {
    data() {
      return {
        categories: [],
        category: { name: '' },
        showForm: false,
        editingCategoryId: null,
        errorMessage: null,
        successMessage: null,
      };
    },
    created() {
      this.fetchCategories();
    },
    methods: {
      async fetchCategories() {
        try {
          const response = await axios.get('http://localhost:8000/omesa/categorias');
          this.categories = response.data;
        } catch (error) {
          this.showError('Error al cargar las categorías.');
        }
      },

      async saveCategory() {
        try {
          if (this.editingCategoryId) {
            // Actualizar categoría existente
            await axios.put(`http://localhost:8000/omesa/categorias/${this.editingCategoryId}`, {
              name: this.category.name,
            });
            this.successMessage = 'Categoría actualizada con éxito';
          } else {
            // Crear nueva categoría
            await axios.post('http://localhost:8000/omesa/categorias', {
              name: this.category.name,
            });
            this.successMessage = 'Categoría creada con éxito';
          }
          this.resetForm();
          this.fetchCategories();
        } catch (error) {
          this.showError('Error al guardar la categoría.');
        }
      },

      async deleteCategory(id) {
        try {
          await axios.delete(`http://localhost:8000/omesa/categorias/${id}`);
          this.successMessage = 'Categoría eliminada con éxito';
          this.fetchCategories();
        } catch (error) {
          this.showError('Error al eliminar la categoría.');
        }
      },

      editCategory(category) {
        this.category = { ...category };
        this.editingCategoryId = category.id;
        this.showForm = true;
      },

      cancelForm() {
        this.resetForm();
      },

      resetForm() {
        this.category = { name: '' };
        this.editingCategoryId = null;
        this.showForm = false;
      },

      showError(message) {
        this.errorMessage = message;
        setTimeout(() => {
          this.errorMessage = null;
        }, 3000);
      },

      closeErrorPopup() {
        this.errorMessage = null;
      },

      closeSuccessPopup() {
        this.successMessage = null;
      },
    },
  };
  </script>

  <style scoped>
  /* Estilos básicos para el componente */
  table {
    width: 100%;
    border-collapse: collapse;
  }

  table, th, td {
    border: 1px solid #ddd;
  }

  th, td {
    padding: 8px;
    text-align: left;
  }

  button {
    padding: 5px 10px;
    cursor: pointer;
  }

  button:hover {
    background-color: #f0f0f0;
  }

  .error-popup, .success-popup {
    background-color: #ffdddd;
    padding: 15px;
    margin-top: 10px;
    border: 1px solid red;
  }

  .success-popup {
    background-color: #ddffdd;
    border: 1px solid green;
  }

  button {
    margin-top: 10px;
  }
  </style>
