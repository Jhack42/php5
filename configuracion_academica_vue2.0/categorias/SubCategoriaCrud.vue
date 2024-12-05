<script>
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
  data() {
    return {
      subCategories: [],
      filteredSubCategories: [],
      paginatedSubCategories: [],
      categories: [],
      newSubCategory: {
        description: '',
        categoryId: '',
      },
      message: '',
      messageClass: '',
      pagination: null,
      viewMode: 'scroll',
      searchQuery: '',
      itemsPerPage: 5,
      itemsPerPageOptions: [1, 3, 5, 7, 10, 100],
      currentPage: 1,
      totalPages: 1,
      isSubmitting: false,
      isUpdating: false,
      isDeleting: false,
      sortKey: '',
      sortOrder: 'asc',
    }
  },
  mounted() {
    this.fetchCategories()
    this.fetchSubCategories()
  },
  methods: {
    async fetchSubCategories(url = '/api/supcategoria') {
      try {
        const response = await axios.get(url)

        this.processSubCategories(response.data.subCategorias)
      }
      catch (error) {
        this.showMessage(`Error al cargar sub categorías: ${error.message}`, 'error')
      }
    },
    async fetchCategories() {
      try {
        const response = await axios.get('/api/categoria')

        this.categories = response.data.categorias
      }
      catch (error) {
        this.showMessage(`Error al cargar categorías: ${error.message}`, 'error')
      }
    },
    processSubCategories(data) {
      const uniqueSubCategories = Array.from(new Map(data.map(item => [item.n_id_sub_categoria, item])).values())

      this.subCategories = uniqueSubCategories
      this.applySearch()
    },
    paginate() {
      this.totalPages = Math.ceil(this.filteredSubCategories.length / this.itemsPerPage)
      this.changePage(1)
    },
    changePage(page) {
      this.currentPage = page

      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage

      this.paginatedSubCategories = this.filteredSubCategories.slice(start, end)
    },
    applySearch() {
      if (this.searchQuery.trim() === '') {
        this.filteredSubCategories = [...this.subCategories]
      }
      else {
        this.filteredSubCategories = this.subCategories.filter(subCategory =>
          subCategory.v_descripcion.toLowerCase().includes(this.searchQuery.toLowerCase()),
        )
      }
      this.sortSubCategories()
      if (this.viewMode === 'pagination')
        this.paginate()
    },
    sortSubCategories() {
      if (this.sortKey) {
        this.filteredSubCategories.sort((a, b) => {
          let result = 0
          if (a[this.sortKey] < b[this.sortKey])
            result = -1
          if (a[this.sortKey] > b[this.sortKey])
            result = 1

          return this.sortOrder === 'asc' ? result : -result
        })
      }
    },
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
      }
      else {
        this.sortKey = key
        this.sortOrder = 'asc'
      }
      this.sortSubCategories()
      this.paginate()
    },
    getSortClass(key) {
      if (this.sortKey !== key)
        return ''

      return this.sortOrder === 'asc' ? 'sort-asc' : 'sort-desc'
    },
    async createSubCategory() {
      this.isSubmitting = true
      try {
        // Confirmar antes de crear la subcategoría
        const result = await Swal.fire({
          title: '¿Estás seguro?',
          text: '¿Deseas crear esta subcategoría?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Sí, crear',
          cancelButtonText: 'Cancelar',
        })

        if (result.isConfirmed) {
          // Realizar la petición para crear la subcategoría
          await axios.post('/api/supcategoria/nuevo', {
            n_id_categoria: this.newSubCategory.categoryId,
            v_descripcion: this.newSubCategory.description,
          })

          // Mostrar mensaje de éxito
          Swal.fire('Creada', 'Sub Categoría creada exitosamente', 'success')

          // Recargar las subcategorías
          this.fetchSubCategories()

          // Limpiar los campos del formulario
          this.newSubCategory = { description: '', categoryId: '' }
        }
      }
      catch (error) {
        // Mostrar mensaje de error en caso de fallo
        Swal.fire('Error', `Error al crear sub categoría: ${error.message}`, 'error')
      }
      finally {
        this.isSubmitting = false
      }
    },
    async updateSubCategory(subCategory) {
      this.isUpdating = true
      try {
        // Confirmar antes de realizar la actualización
        const result = await Swal.fire({
          title: '¿Estás seguro?',
          text: '¿Deseas actualizar esta subcategoría?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, actualizar',
          cancelButtonText: 'Cancelar',
        })

        if (result.isConfirmed) {
          // Realizar la petición para actualizar la subcategoría
          await axios.put(`/api/supcategoria/${subCategory.n_id_sub_categoria}/editar`, {
            v_descripcion: subCategory.v_descripcion,
          })

          // Mostrar mensaje de éxito
          Swal.fire('Actualizada', 'Sub Categoría actualizada exitosamente', 'success')
        }
      }
      catch (error) {
        // Mostrar mensaje de error en caso de fallo
        Swal.fire('Error', `Error al actualizar sub categoría: ${error.message}`, 'error')
      }
      finally {
        this.isUpdating = false
      }
    },
    async deleteSubCategory(id) {
      this.isDeleting = true
      try {
        // Confirmar la eliminación en cascada antes de proceder
        const result = await Swal.fire({
          title: '¿Estás seguro?',
          text: 'Esta acción eliminará la subcategoría y sus elementos relacionados en cascada. ¿Deseas continuar?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar',
        })

        if (result.isConfirmed) {
          // Realizar la eliminación de la subcategoría
          await axios.delete(`/api/supcategoria/${id}/eliminar`)

          // Filtrar la subcategoría eliminada
          this.subCategories = this.subCategories.filter(subCategory => subCategory.n_id_sub_categoria !== id)

          // Aplicar búsqueda si es necesario
          this.applySearch()

          // Mostrar mensaje de éxito
          Swal.fire('Eliminada', 'Sub Categoría eliminada correctamente', 'success')
        }
      }
      catch (error) {
        // Mostrar mensaje de error
        Swal.fire('Error', `Error al eliminar sub categoría: ${error.message}`, 'error')
      }
      finally {
        this.isDeleting = false
      }
    },
    exportToExcel() {
      import('xlsx').then(XLSX => {
        const ws = XLSX.utils.json_to_sheet(this.filteredSubCategories)
        const wb = XLSX.utils.book_new()

        XLSX.utils.book_append_sheet(wb, ws, 'Sub Categorías')
        XLSX.writeFile(wb, 'subcategorias.xlsx')
      })
    },
    exportToPdf() {
      import('jspdf').then(jsPDF => {
        const doc = new jsPDF()

        doc.autoTable({ html: '#table' })
        doc.save('subcategorias.pdf')
      })
    },
    showMessage(message, type) {
      this.message = message
      this.messageClass = type
      setTimeout(() => {
        this.message = ''
      }, 3000)
    },
    setViewMode(mode) {
      this.viewMode = mode
      this.paginate()
    },
    getPaginationButtons() {
      const pages = []
      for (let i = 1; i <= this.totalPages; i++) {
        if (i === 1 || i === this.totalPages || Math.abs(i - this.currentPage) <= 2)
          pages.push(i)
        else if (pages[pages.length - 1] !== '...')
          pages.push('...')
      }

      return pages
    },
  },
}
</script>

<template>
  <div class="container">
    <h1>Listado de Sub Categorías</h1>
    <div class="toolbar">
      <div class="top-row">
        <select
          v-model="newSubCategory.categoryId"
          :disabled="isSubmitting"
        >
          <option
            disabled
            value=""
          >
            Seleccionar Categoría
          </option>
          <option
            v-for="category in categories"
            :key="category.n_id_categoria"
            :value="category.n_id_categoria"
          >
            {{ category.v_titulo }}
          </option>
        </select>
        <input
          v-model="newSubCategory.description"
          placeholder="Nueva Sub Categoría"
          :disabled="isSubmitting"
        >
        <button
          :disabled="!newSubCategory.description.trim() || isSubmitting"
          @click="createSubCategory"
        >
          Crear
          Sub
          Categoría
        </button>
      </div>
      <div class="second-row">
        <input
          v-model="searchQuery"
          placeholder="Buscar Sub Categoría"
          @input="applySearch"
        >
        <button @click="exportToExcel">
          Exportar a Excel
        </button>
        <button @click="exportToPdf">
          Exportar a PDF
        </button>
      </div>
      <div class="third-row">
        <div class="dropdown">
          <button class="dropbtn">
            Vista: {{ viewMode === 'scroll' ? 'Barra de desplazamiento' : 'Paginación'
            }}
          </button>
          <div class="dropdown-content">
            <a @click="setViewMode('scroll')">Barra de desplazamiento</a>
            <a @click="setViewMode('pagination')">Paginación</a>
          </div>
        </div>
        <div
          v-if="viewMode === 'pagination'"
          class="dropdown"
        >
          <label for="itemsPerPage">Mostrar</label>
          <select
            v-model="itemsPerPage"
            @change="paginate"
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
    </div>
    <div v-if="viewMode === 'pagination'">
      <div
        v-for="subCategory in paginatedSubCategories"
        :key="subCategory.n_id_sub_categoria"
        class="subcategory-item"
      >
        <select v-model="subCategory.n_id_categoria">
          <option
            v-for="category in categories"
            :key="category.n_id_categoria"
            :value="category.n_id_categoria"
          >
            {{ category.v_titulo }}
          </option>
        </select>
        <input v-model="subCategory.v_descripcion">
        <button
          class="update"
          :disabled="isUpdating"
          @click="updateSubCategory(subCategory)"
        >
          Actualizar
        </button>
        <button
          class="delete"
          :disabled="isDeleting"
          @click="deleteSubCategory(subCategory.n_id_sub_categoria)"
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
      v-else
      class="subcategories-container"
    >
      <div
        v-for="subCategory in filteredSubCategories"
        :key="subCategory.n_id_sub_categoria"
        class="subcategories-item"
      >
        <select v-model="subCategory.n_id_categoria">
          <option
            v-for="category in categories"
            :key="category.n_id_categoria"
            :value="category.n_id_categoria"
          >
            {{ category.v_titulo }}
          </option>
        </select>
        <input v-model="subCategory.v_descripcion">
        <button
          :disabled="isUpdating"
          @click="updateSubCategory(subCategory)"
        >
          Actualizar
        </button>
        <button
          :disabled="isDeleting"
          @click="deleteSubCategory(subCategory.n_id_sub_categoria)"
        >
          Eliminar
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
  max-inline-size: 1100px;
}

h1 {
  color: #711610;
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

.subcategory-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-block-end: 10px;
}

.subcategory-item select {
  flex: 1;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-inline-end: 10px;
}

.subcategory-item input {
  flex: 2;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-inline-end: 10px;
}

.subcategory-item button {
  border: none;
  border-radius: 5px;
  cursor: pointer;
  padding-block: 10px;
  padding-inline: 15px;
}

.subcategory-item button.update {
  background-color: #71a50f;
  color: white;
  margin-inline-end: 10px;
}

.subcategory-item button.delete {
  background-color: #a50f0f;
  color: white;
}

.subcategory-item button:disabled {
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

.subcategories-container {
  padding: 10px;
  border: 1px solid #ddd;
  margin-block-start: 20px;
  max-block-size: 400px;
  overflow-y: auto;
}

.subcategories-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-block-end: 10px;
}

.subcategories-item select {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-inline-end: 10px;
}

.subcategories-item input {
  flex: 2;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-inline-end: 10px;
}

.subcategories-item button {
  border: none;
  border-radius: 5px;
  cursor: pointer;
  padding-block: 10px;
  padding-inline: 15px;
}

.subcategories-item button.update {
  background-color: #71a50f;
  color: white;
  margin-inline-end: 10px;
}

.subcategories-item button.delete {
  background-color: #a50f0f;
  color: white;
}

.subcategories-item button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

/* Custom scrollbar for WebKit browsers */
.subcategories-container::-webkit-scrollbar {
  inline-size: 10px;
  opacity: 0;

  /* Inicialmente oculta el scrollbar */
  transition: opacity 0.3s;

  /* Transición para la opacidad */
}

/* Mostramos el scrollbar solo cuando se pasa el cursor por encima del contenedor */
.subcategories-container:hover::-webkit-scrollbar {
  opacity: 1;

  /* Hace visible el scrollbar gradualmente */
}

.subcategories-container::-webkit-scrollbar-track {
  background: #f5f5f5;
}

.subcategories-container::-webkit-scrollbar-thumb {
  border-radius: 5px;
  background: #8b0202;
  transition: background-color 0.3s;
}

.subcategories-container::-webkit-scrollbar-thumb:hover {
  background: #939598;
}

.subcategories-container::-webkit-scrollbar-thumb:active {
  background-color: #e8cfa0;
}
</style>
