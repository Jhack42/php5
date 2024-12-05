<template>
    <div class="container">
        <h1>Test CRUD Categorías</h1>
        <div class="toolbar">
            <div class="top-row">
                <input v-model="newCategory" placeholder="Nueva Categoría" :disabled="isSubmitting">
                <button @click="createCategory" :disabled="!newCategory.trim() || isSubmitting">Crear Categoría</button>
            </div>
            <div class="second-row">
                <input v-model="searchQuery" @input="applySearch" placeholder="Buscar Categoría">
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
            <div v-for="category in paginatedCategories" :key="category.n_idcategoria" class="category-item">
                <input v-model="category.v_titulo">
                <button @click="updateCategory(category)" class="update" :disabled="isUpdating">Actualizar</button>
                <button @click="deleteCategory(category.n_idcategoria)" class="delete"
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
        <div v-else class="scroll-container">
            <table>
                <thead>
                    <tr>
                        <th @click="sortBy('n_id_categoria')">ID <span :class="getSortClass('n_id_categoria')"></span>
                        </th>
                        <th @click="sortBy('v_titulo')">Título <span :class="getSortClass('v_titulo')"></span></th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="category in filteredCategories" :key="category.n_idcategoria">
                        <td>{{ category.n_idcategoria }}</td>
                        <td><input v-model="category.v_titulo"></td>
                        <td>
                            <button @click="updateCategory(category)" class="update"
                                :disabled="isUpdating">Actualizar</button>
                            <button @click="deleteCategory(category.n_idcategoria)" class="delete"
                                :disabled="isDeleting">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="message" :class="['notification', messageClass]">{{ message }}</div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            categories: [],
            filteredCategories: [],
            paginatedCategories: [],
            newCategory: '',
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
        async fetchCategories(url = '/SmtrCategoria') {
            try {
                const response = await axios.get(url);
                this.processCategories(response.data);
            } catch (error) {
                this.showMessage('Error al cargar categorías: ' + error.message, 'error');
            }
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
        },
        processCategories(data) {
            const uniqueCategories = Array.from(new Map(data.map(item => [item['n_id_categoria'], item])).values());
            this.categories = uniqueCategories;
            this.applySearch();
        },
        paginate() {
            this.totalPages = Math.ceil(this.filteredCategories.length / this.itemsPerPage);
            this.changePage(1);
        },
        changePage(page) {
            this.currentPage = page;
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            this.paginatedCategories = this.filteredCategories.slice(start, end);
        },
        applySearch() {
            if (this.searchQuery.trim() === '') {
                this.filteredCategories = [...this.categories];
            } else {
                this.filteredCategories = this.categories.filter(category =>
                    category.v_titulo.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            }
            this.sortCategories();
            this.paginate();
        },
        sortCategories() {
            if (this.sortKey) {
                this.filteredCategories.sort((a, b) => {
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
            this.sortCategories();
            this.paginate();
        },
        getSortClass(key) {
            if (this.sortKey !== key) return '';
            return this.sortOrder === 'asc' ? 'sort-asc' : 'sort-desc';
        },
        async createCategory() {
            this.isSubmitting = true;
            try {
                const response = await axios.post('/SmtrCategoria', { v_titulo: this.newCategory });
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
                const response = await axios.put(`/SmtrCategoria/${category.n_idcategoria}`, { v_titulo: category.v_titulo });
                this.showMessage('Categoría actualizada exitosamente', 'success');
                this.fetchCategories();
            } catch (error) {
                this.showMessage('Error al actualizar categoría: ' + error.message, 'error');
            } finally {
                this.isUpdating = false;
            }
        },
        async deleteCategory(id) {
            this.isDeleting = true;
            try {
                await axios.delete(`/SmtrCategoria/${id}`);
                this.showMessage('Categoría eliminada correctamente', 'success');
                this.fetchCategories();
            } catch (error) {
                this.showMessage('Error al eliminar categoría: ' + error.message, 'error');
            } finally {
                this.isDeleting = false;
            }
        },
        exportToExcel() {
            import("xlsx").then((xlsx) => {
                const ws = xlsx.utils.json_to_sheet(this.categories);
                const wb = xlsx.utils.book_new();
                xlsx.utils.book_append_sheet(wb, ws, "Categorías");
                xlsx.writeFile(wb, "categorías.xlsx");
            });
        },
        exportToPdf() {
            import("jspdf").then((jsPDF) => {
                import("jspdf-autotable").then(() => {
                    const doc = new jsPDF.default();
                    doc.text("Categorías", 20, 10);
                    doc.autoTable({
                        head: [["ID", "Título"]],
                        body: this.categories.map((category) => [category.n_idcategoria, category.v_titulo]),
                    });
                    doc.save("categorías.pdf");
                });
            });
        },
        setViewMode(mode) {
            this.viewMode = mode;
            this.fetchCategories();
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
.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
    color: #711610;
    /* Color institucional */
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

.category-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.category-item input {
    flex: 1;
    margin-right: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.category-item button {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.category-item button.update {
    background-color: #71a50f;
    /* Color para actualizar */
    color: white;
    margin-right: 10px;
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
    margin-top: 20px;
}

.pagination button {
    margin: 0 5px;
    padding: 5px 10px;
    border: 1px solid #ccc;
    background-color: #711610;
    /* Color institucional */
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
    /* Color institucional */
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
    /* Color institucional */
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

.pagination button {
    background-color: #711610;
    /* Color institucional */
}

.pagination button.disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.pagination .ellipsis {
    padding: 5px 10px;
}

/* Custom scrollbar for WebKit browsers */
.scroll-container::-webkit-scrollbar {
    width: 10px;
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
    background: #8B0202;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.scroll-container::-webkit-scrollbar-thumb:hover {
    background: #939598;
}

.scroll-container::-webkit-scrollbar-thumb:active {
    background-color: #E8CFA0;
}
</style>
