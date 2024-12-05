<template>
    <div class="container">
        <h1>Test CRUD Sub Categorías</h1>
        <div class="toolbar">
            <div class="top-row">
                <select v-model="newSubCategory.subjectId" :disabled="isSubmitting">
                    <option disabled value="">Seleccionar Asunto</option>
                    <option v-for="subject in subjects" :key="subject.n_idasunto" :value="subject.n_idasunto">
                        {{ subject.v_descripcion }}
                    </option>
                </select>
                <select v-model="newSubCategory.categoryId" :disabled="isSubmitting">
                    <option disabled value="">Seleccionar Categoría</option>
                    <option v-for="category in categories" :key="category.n_idcategoria"
                        :value="category.n_idcategoria">
                        {{ category.v_titulo }}
                    </option>
                </select>
                <input v-model="newSubCategory.description" placeholder="Nueva Sub Categoría" :disabled="isSubmitting">
                <button @click="createSubCategory" :disabled="!newSubCategory.description.trim() || isSubmitting">Crear
                    Sub
                    Categoría</button>
            </div>
            <div class="second-row">
                <input v-model="searchQuery" @input="applySearch" placeholder="Buscar Sub Categoría">
                <button @click="exportToExcel">Exportar a Excel</button>
                <button @click="exportToPdf">Exportar a PDF</button>
            </div>
            <div class="third-row">
                <div class="dropdown">
                    <button class="dropbtn">Vista: {{ viewMode === 'scroll' ? 'Barra de desplazamiento' : 'Paginación'
                        }}</button>
                    <div class="dropdown-content">
                        <a @click="setViewMode('scroll')">Barra de desplazamiento</a>
                        <a @click="setViewMode('pagination')">Paginación</a>
                    </div>
                </div>
                <div class="dropdown" v-if="viewMode === 'pagination'">
                    <label for="itemsPerPage">Mostrar</label>
                    <select v-model="itemsPerPage" @change="paginate">
                        <option v-for="option in itemsPerPageOptions" :key="option" :value="option">{{ option }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div v-if="viewMode === 'pagination'">
            <div v-for="subCategory in paginatedSubCategories" :key="subCategory.n_idsub_categoria"
                class="subcategory-item">
                <select v-model="subCategory.n_idasunto">
                    <option v-for="subject in subjects" :key="subject.n_idasunto" :value="subject.n_idasunto">
                        {{ subject.v_descripcion }}
                    </option>
                </select>
                <select v-model="subCategory.n_idcategoria">
                    <option v-for="category in categories" :key="category.n_idcategoria"
                        :value="category.n_idcategoria">
                        {{ category.v_titulo }}
                    </option>
                </select>
                <input v-model="subCategory.subcategoria_descripcion">
                <button @click="updateSubCategory(subCategory)" class="update"
                    :disabled="isUpdating">Actualizar</button>
                <button @click="deleteSubCategory(subCategory.n_idsub_categoria)" class="delete"
                    :disabled="isDeleting">Eliminar</button>
            </div>
            <div class="pagination">
                <button :disabled="currentPage === 1" @click="changePage(currentPage - 1)">&laquo; Anterior</button>
                <button v-for="page in getPaginationButtons()" :key="page" :disabled="page === '...'"
                    @click="changePage(page)">
                    {{ page }}
                </button>
                <button :disabled="currentPage === totalPages" @click="changePage(currentPage + 1)">Siguiente
                    &raquo;</button>
            </div>
        </div>
        <div class="subcategories-container" v-else>
            <div v-for="subCategory in filteredSubCategories" :key="subCategory.n_idsub_categoria"
                class="subcategories-item">
                <select v-model="subCategory.n_idasunto">
                    <option v-for="subject in subjects" :key="subject.n_idasunto" :value="subject.n_idasunto">
                        {{ subject.v_descripcion }}
                    </option>
                </select>
                <select v-model="subCategory.n_idcategoria">
                    <option v-for="category in categories" :key="category.n_idcategoria"
                        :value="category.n_idcategoria">
                        {{ category.v_titulo }}
                    </option>
                </select>
                <input v-model="subCategory.subcategoria_descripcion">
                <button @click="updateSubCategory(subCategory)" :disabled="isUpdating">Actualizar</button>
                <button @click="deleteSubCategory(subCategory.n_idsub_categoria)"
                    :disabled="isDeleting">Eliminar</button>
            </div>
        </div>
        <div v-if="message" :class="['notification', messageClass]">{{ message }}</div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            subCategories: [],
            filteredSubCategories: [],
            paginatedSubCategories: [],
            categories: [],
            subjects: [],
            newSubCategory: {
                description: '',
                categoryId: '',
                subjectId: '',
            },
            message: '',
            messageClass: '',
            pagination: null,
            viewMode: 'scroll',
            searchQuery: '',
            itemsPerPage: 5,
            itemsPerPageOptions: [1, 3, 5, 7, 10],
            currentPage: 1,
            totalPages: 1,
            isSubmitting: false,
            isUpdating: false,
            isDeleting: false,
            sortKey: '',
            sortOrder: 'asc',
        };
    },
    methods: {
        async fetchSubCategories(url = '/api/SmtrSubCategoria') {
            try {
                const response = await axios.get(url);
                this.processSubCategories(response.data);
            } catch (error) {
                this.showMessage('Error al cargar sub categorías: ' + error.message, 'error');
            }
        },
        async fetchCategories() {
            try {
                const response = await axios.get('/api/SmtrCategoria');
                this.categories = response.data;
            } catch (error) {
                this.showMessage('Error al cargar categorías: ' + error.message, 'error');
            }
        },
        async fetchSubjects() {
            try {
                const response = await axios.get('/api/SmtrAsunto');
                this.subjects = response.data;
            } catch (error) {
                this.showMessage('Error al cargar asuntos: ' + error.message, 'error');
            }
        },
        processSubCategories(data) {
            const uniqueSubCategories = Array.from(new Map(data.map(item => [item['n_idsub_categoria'], item])).values());
            this.subCategories = uniqueSubCategories;
            this.applySearch();
        },
        paginate() {
            this.totalPages = Math.ceil(this.filteredSubCategories.length / this.itemsPerPage);
            this.changePage(1);
        },
        changePage(page) {
            this.currentPage = page;
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            this.paginatedSubCategories = this.filteredSubCategories.slice(start, end);
        },
        applySearch() {
            if (this.searchQuery.trim() === '') {
                this.filteredSubCategories = [...this.subCategories];
            } else {
                this.filteredSubCategories = this.subCategories.filter(subCategory =>
                    subCategory.subcategoria_descripcion.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            }
            this.sortSubCategories();
            if (this.viewMode === 'pagination') {
                this.paginate();
            }
        },
        sortSubCategories() {
            if (this.sortKey) {
                this.filteredSubCategories.sort((a, b) => {
                    let result = 0;
                    if (a[this.sortKey] < b[this.sortKey]) result = -1;
                    if (a[this.sortKey] > b[this.sortKey]) result = 1;
                    return this.sortOrder === 'asc' ? result : -result;
                });
            }
        },
        sortBy(key) {
            if (this.sortKey === key) {
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortKey = key;
                this.sortOrder = 'asc';
            }
            this.sortSubCategories();
            this.paginate();
        },
        getSortClass(key) {
            if (this.sortKey !== key) return '';
            return this.sortOrder === 'asc' ? 'sort-asc' : 'sort-desc';
        },
        async createSubCategory() {
            this.isSubmitting = true;
            try {
                await axios.post('/api/SmtrSubCategoria', {
                    v_descripcion: this.newSubCategory.description,
                    n_idcategoria: this.newSubCategory.categoryId,
                    n_idasunto: this.newSubCategory.subjectId,
                });
                this.showMessage('Sub Categoría creada exitosamente', 'success');
                this.fetchSubCategories();
                this.newSubCategory = { description: '', categoryId: '', subjectId: '' };
            } catch (error) {
                this.showMessage('Error al crear sub categoría: ' + error.message, 'error');
            } finally {
                this.isSubmitting = false;
            }
        },
        async updateSubCategory(subCategory) {
            this.isUpdating = true;
            try {
                await axios.put(`/api/SmtrSubCategoria/${subCategory.n_idsub_categoria}`, {
                    v_descripcion: subCategory.subcategoria_descripcion,
                    n_idcategoria: subCategory.n_idcategoria,
                    n_idasunto: subCategory.n_idasunto,
                });
                this.showMessage('Sub Categoría actualizada exitosamente', 'success');
            } catch (error) {
                this.showMessage('Error al actualizar sub categoría: ' + error.message, 'error');
            } finally {
                this.isUpdating = false;
            }
        },
        async deleteSubCategory(id) {
            this.isDeleting = true;
            try {
                await axios.delete(`/api/SmtrSubCategoria/${id}`);
                this.subCategories = this.subCategories.filter(subCategory => subCategory.n_idsub_categoria !== id);
                this.applySearch();
                this.showMessage('Sub Categoría eliminada correctamente', 'success');
            } catch (error) {
                this.showMessage('Error al eliminar sub categoría: ' + error.message, 'error');
            } finally {
                this.isDeleting = false;
            }
        },
        exportToExcel() {
            import("xlsx").then((xlsx) => {
                const ws = xlsx.utils.json_to_sheet(this.subCategories);
                const wb = xlsx.utils.book_new();
                xlsx.utils.book_append_sheet(wb, ws, "Sub Categorías");
                xlsx.writeFile(wb, "sub_categorías.xlsx");
            });
        },
        exportToPdf() {
            import("jspdf").then((jsPDF) => {
                import("jspdf-autotable").then(() => {
                    const doc = new jsPDF.default();
                    doc.text("Sub Categorías", 20, 10);
                    doc.autoTable({
                        head: [["ID", "Descripción"]],
                        body: this.subCategories.map((subCategory) => [subCategory.n_idsub_categoria, subCategory.subcategoria_descripcion]),
                    });
                    doc.save("sub_categorías.pdf");
                });
            });
        },
        setViewMode(mode) {
            this.viewMode = mode;
            this.fetchSubCategories();
        },
        showMessage(message, type) {
            this.message = message;
            this.messageClass = type === 'success' ? 'success' : 'error';
            setTimeout(() => {
                this.message = '';
                this.messageClass = '';
            }, 3000);
        },
        getPaginationButtons() {
            const buttons = [];
            const totalPages = this.totalPages;
            const currentPage = this.currentPage;

            if (totalPages <= 5) {
                for (let i = 1; i <= totalPages; i++) {
                    buttons.push(i);
                }
            } else {
                if (currentPage <= 3) {
                    buttons.push(1, 2, 3, 4, '...', totalPages);
                } else if (currentPage >= totalPages - 2) {
                    buttons.push(1, '...', totalPages - 3, totalPages - 2, totalPages - 1, totalPages);
                } else {
                    buttons.push(1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages);
                }
            }
            return buttons;
        }
    },
    mounted() {
        this.fetchSubCategories();
        this.fetchCategories();
        this.fetchSubjects();
    }
};
</script>

<style>
.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    max-width: 1100px;
}

h1 {
    color: #711610;
    text-align: center;
}

.toolbar {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.top-row,
.second-row,
.third-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.toolbar input,
.toolbar button,
.toolbar select {
    margin-right: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.subcategory-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.subcategory-item select {
    flex: 1;
    margin-right: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.subcategory-item input {
    flex: 2;
    margin-right: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.subcategory-item button {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.subcategory-item button.update {
    background-color: #71a50f;
    color: white;
    margin-right: 10px;
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
    margin-top: 20px;
}

.pagination button {
    margin: 0 5px;
    padding: 5px 10px;
    border: 1px solid #ccc;
    background-color: #711610;
    color: white;
    cursor: pointer;
}

.pagination button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.scroll-container {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 10px;
}

.notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 15px;
    border-radius: 5px;
    color: white;
    background-color: #711610;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
    background-color: #711610;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
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
    content: ' ▲';
}

.sort-desc::after {
    content: ' ▼';
}

table {
    width: 100%;
    border-collapse: collapse;
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
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #ddd;
    margin-top: 20px;
    padding: 10px;
}

.subcategories-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.subcategories-item select {
    margin-right: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.subcategories-item input {
    flex: 2;
    margin-right: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.subcategories-item button {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.subcategories-item button.update {
    background-color: #71a50f;
    color: white;
    margin-right: 10px;
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
    width: 10px;
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
    background: #8B0202;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.subcategories-container::-webkit-scrollbar-thumb:hover {
    background: #939598;
}

.subcategories-container::-webkit-scrollbar-thumb:active {
    background-color: #E8CFA0;
}
</style>
