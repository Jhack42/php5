<script>
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
  data() {
    return {
      categories: [],
      paginatedCategories: [],
      newCategory: '',
      message: '',
      messageClass: '',
      searchQuery: '',
      itemsPerPage: 5,
      itemsPerPageOptions: [1, 3, 5, 7, 10, 100],
      currentPage: 1,
      totalPages: 1,
      isSubmitting: false,
      isUpdating: false,
      isDeleting: false,
    }
  },

  mounted() {
    this.fetchCategories()
  },
  methods: {
    async fetchCategories() {
      try {
        const response = await axios.get('/api/categoria')

        this.categories = response.data?.categorias || []
        this.applySearch()
      }
      catch (error) {
        console.error('Error al obtener las categorías:', error)
        alert(error.response?.data?.message || 'Error al obtener las categorías')
      }
    },

    applySearch() {
      const query = this.searchQuery.trim().toLowerCase()

      const filteredCategories = query
        ? this.categories.filter(category =>
          category.v_titulo.toLowerCase().includes(query),
        )
        : [...this.categories]

      this.paginate(filteredCategories)
    },

    paginate(categories = this.categories) {
      this.totalPages = Math.ceil(categories.length / this.itemsPerPage)
      this.changePage(1, categories) // Reinicia en la primera página al cambiar elementos visibles
    },

    changePage(page, categories = this.categories) {
      this.currentPage = page

      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage

      this.paginatedCategories = categories.slice(start, end)
    },

    updateItemsPerPage() {
      this.currentPage = 1 // Reiniciar a la primera página cuando se cambia el valor
      this.paginate()
    },

    getPaginationButtons() {
      const buttons = []
      for (let i = 1; i <= this.totalPages; i++)
        buttons.push(i)

      return buttons
    },

    async createCategory() {
      this.isSubmitting = true
      try {
        await axios.post('/api/categoria/create', { v_titulo: this.newCategory })
        this.showMessage('Categoría creada exitosamente', 'success')
        this.fetchCategories()
        this.newCategory = ''
      }
      catch (error) {
        this.showMessage(`Error al crear categoría: ${error.message}`, 'error')
      }
      finally {
        this.isSubmitting = false
      }
    },

    async updateCategory(category) {
      this.isUpdating = true
      try {
        // Confirmar antes de realizar la actualización
        const result = await Swal.fire({
          title: '¿Estás seguro?',
          text: '¿Deseas actualizar esta categoría?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, actualizar',
          cancelButtonText: 'Cancelar',
        })

        if (result.isConfirmed) {
          await axios.put(`/api/categoria/update/${category.n_id_categoria}`, { v_titulo: category.v_titulo })

          // Mostrar mensaje de éxito
          Swal.fire('Actualizado', 'Categoría actualizada exitosamente', 'success')

          // Recargar las categorías
          this.fetchCategories()
        }
      }
      catch (error) {
        Swal.fire('Error', `Error al actualizar categoría: ${error.message}`, 'error')
      }
      finally {
        this.isUpdating = false
      }
    },

    async deleteCategory(id) {
      if (!id) {
        console.error('ID de categoría no válido:', id)
        Swal.fire('Error', 'ID de categoría no válido', 'error')

        return
      }

      if (this.isDeleting)
        return // Evitar múltiples eliminaciones simultáneas
      this.isDeleting = true

      try {
        const result = await Swal.fire({
          title: '¿Estás seguro?',
          text: 'Esta acción eliminará la categoría permanentemente.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar',
        })

        if (result.isConfirmed) {
          // Llamada al servidor para eliminar la categoría
          await axios.delete(`/api/categoria/delete/${id}`)

          // Recargar las categorías actualizadas desde el servidor
          this.fetchCategories()

          Swal.fire('Eliminado', 'Categoría eliminada exitosamente', 'success')
        }
      }
      catch (error) {
        console.error('Error al eliminar categoría:', error)
        Swal.fire('Error', `Error al eliminar categoría: ${error.response?.data?.message || error.message}`, 'error')
      }
      finally {
        this.isDeleting = false
      }
    },

    exportToExcel() {
      import('xlsx').then(xlsx => {
        const ws = xlsx.utils.json_to_sheet(this.categories)
        const wb = xlsx.utils.book_new()

        xlsx.utils.book_append_sheet(wb, ws, 'Categorías')
        xlsx.writeFile(wb, 'categorías.xlsx')
      })
    },

    exportToPdf() {
      import('jspdf').then(jsPDF => {
        import('jspdf-autotable').then(() => {
          const doc = new jsPDF.default()

          doc.text('Categorías', 20, 10)
          doc.autoTable({
            head: [['ID', 'Título']],
            body: this.categories.map(category => [category.n_id_categoria, category.v_titulo]),
          })
          doc.save('categorías.pdf')
        })
      })
    },

    showMessage(message, type) {
      this.message = message
      this.messageClass = type === 'success' ? 'success' : 'error'
      setTimeout(() => {
        this.message = ''
        this.messageClass = ''
      }, 3000)
    },
  },
}
</script>

<template>
  <div class="container">
    <h1>Listado de Categorías</h1>
    <div class="toolbar">
      <div class="top-row">
        <input
          v-model="newCategory"
          placeholder="Nueva Categoría"
          :disabled="isSubmitting"
        >
        <button
          :disabled="!newCategory.trim() || isSubmitting"
          @click="createCategory"
        >
          Crear Categoría
        </button>
      </div>
      <div class="second-row">
        <input
          v-model="searchQuery"
          placeholder="Buscar Categoría"
          @input="applySearch"
        >
        <button @click="exportToExcel">
          Exportar a Excel
        </button>
        <button @click="exportToPdf">
          Exportar a PDF
        </button>
      </div>
      <div class="pagination-controls">
        <label for="itemsPerPage">Mostrar</label>
        <select
          v-model="itemsPerPage"
          @change="updateItemsPerPage"
        >
          <option
            v-for="option in itemsPerPageOptions"
            :key="option"
            :value="option"
          >
            {{ option }}
          </option>
        </select>
      </div>
    </div>
    <div>
      <div
        v-for="category in paginatedCategories"
        :key="category.n_idcategoria"
        class="category-item"
      >
        <input v-model="category.v_titulo">
        <button
          class="update"
          :disabled="isUpdating"
          @click="updateCategory(category)"
        >
          Actualizar
        </button>
        <button
          class="delete"
          :disabled="isDeleting"
          @click="deleteCategory(category.n_id_categoria)"
        >
          Eliminar
        </button>
      </div>
      <div class="pagination">
        <button
          :disabled="currentPage === 1"
          @click="changePage(currentPage - 1)"
        >
          &laquo; Anterior
        </button>
        <button
          v-for="page in getPaginationButtons()"
          :key="page"
          :disabled="page === '...'"
          @click="changePage(page)"
        >
          {{ page }}
        </button>
        <button
          :disabled="currentPage === totalPages"
          @click="changePage(currentPage + 1)"
        >
          Siguiente
          &raquo;
        </button>
      </div>
    </div>
    <div
      v-if="message"
      class="notification"
      :class="[messageClass]"
    >
      {{ message }}
    </div>
  </div>
</template>

<style>
.container {
  padding: 20px;
  border-radius: 10px;
  background-color: #f5f5f5;
  box-shadow: 0 0 10px rgba(0, 0, 0, 10%);
  margin-block: 0;
  margin-inline: auto;
  max-inline-size: 800px;
}

h1 {
  color: #711610;

  /* Color institucional */
  text-align: center;
}

.toolbar {
  display: flex;
  flex-direction: column;
  margin-block-end: 20px;
}

.top-row,
.second-row,
.third-row {
  display: flex;
  justify-content: space-between;
  margin-block-end: 10px;
}

.toolbar input,
.toolbar button,
.toolbar select {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-inline-end: 10px;
}

.category-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-block-end: 10px;
}

.category-item input {
  flex: 1;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-inline-end: 10px;
}

.category-item button {
  border: none;
  border-radius: 5px;
  cursor: pointer;
  padding-block: 10px;
  padding-inline: 15px;
}

.category-item button.update {
  background-color: #71a50f;

  /* Color para actualizar */
  color: white;
  margin-inline-end: 10px;
}

.category-item button.delete {
  background-color: #a50f0f;

  /* Color para eliminar */
  color: white;
}

.category-item button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.pagination {
  display: flex;
  justify-content: center;
  margin-block-start: 20px;
}

.pagination button {
  border: 1px solid #ccc;
  background-color: #711610;

  /* Color institucional */
  color: white;
  cursor: pointer;
  margin-block: 0;
  margin-inline: 5px;
  padding-block: 5px;
  padding-inline: 10px;
}

.pagination button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.scroll-container {
  max-block-size: 400px;
  overflow-y: auto;
  padding-inline-end: 10px;
}

.notification {
  position: fixed;
  padding: 15px;
  border-radius: 5px;
  background-color: #711610;

  /* Color institucional */
  box-shadow: 0 0 10px rgba(0, 0, 0, 10%);
  color: white;
  inset-block-end: 20px;
  inset-inline-end: 20px;
}

.success {
  background-color: #71a50f;
}

.error {
  background-color: #a50f0f;
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropbtn {
  padding: 10px;
  border: none;
  border-radius: 5px;
  background-color: #711610;

  /* Color institucional */
  color: white;
  cursor: pointer;
}

.dropdown-content {
  position: absolute;
  z-index: 1;
  display: none;
  background-color: #f9f9f9;
  box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 20%);
  min-inline-size: 160px;
}

.dropdown-content a {
  display: block;
  color: black;
  padding-block: 12px;
  padding-inline: 16px;
  text-decoration: none;
}

.dropdown-content a:hover {
  background-color: #f1f1f1;
}

.dropdown:hover .dropdown-content {
  display: block;
}

.dropdown:hover .dropbtn {
  background-color: #555;
}

.sort-asc::after {
  content: " ▲";
}

.sort-desc::after {
  content: " ▼";
}

table {
  border-collapse: collapse;
  inline-size: 100%;
}

th,
td {
  padding: 10px;
  border: 1px solid #ccc;
}

th {
  cursor: pointer;
}

.pagination button {
  background-color: #711610;

  /* Color institucional */
}

.pagination button.disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.pagination .ellipsis {
  padding-block: 5px;
  padding-inline: 10px;
}

/* Custom scrollbar for WebKit browsers */
.scroll-container::-webkit-scrollbar {
  inline-size: 10px;
  opacity: 0;

  /* Inicialmente oculta el scrollbar */
  transition: opacity 0.3s;

  /* Transición para la opacidad */
}

/* Mostramos el scrollbar solo cuando se pasa el cursor por encima del contenedor */
.scroll-container:hover::-webkit-scrollbar {
  opacity: 1;

  /* Hace visible el scrollbar gradualmente */
}

.scroll-container::-webkit-scrollbar-track {
  background: #f5f5f5;
}

.scroll-container::-webkit-scrollbar-thumb {
  border-radius: 5px;
  background: #8b0202;
  transition: background-color 0.3s;
}

.scroll-container::-webkit-scrollbar-thumb:hover {
  background: #939598;
}

.scroll-container::-webkit-scrollbar-thumb:active {
  background-color: #e8cfa0;
}
</style>
