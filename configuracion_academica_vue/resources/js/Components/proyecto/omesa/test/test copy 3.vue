<template>
    <div class="container">
        <h1 class="title">Test CRUD Categorías</h1>

        <div class="field has-addons">
            <div class="control is-expanded">
                <input class="input" v-model="newCategory" placeholder="Nueva Categoría" :disabled="isSubmitting">
            </div>
            <div class="control">
                <button class="button is-primary" @click="createCategory"
                    :disabled="!newCategory.trim() || isSubmitting">
                    Crear Categoría
                </button>
            </div>
        </div>

        <div class="field has-addons">
            <div class="control is-expanded">
                <input class="input" v-model="searchQuery" @input="applySearch" placeholder="Buscar Categoría">
            </div>
            <div class="control">
                <button class="button is-info" @click="exportToExcel">Exportar a Excel</button>
                <button class="button is-info" @click="exportToPdf">Exportar a PDF</button>
            </div>
        </div>

        <div class="field">
            <div class="select">
                <select v-model="viewMode" @change="fetchCategories">
                    <option value="scroll">Barra de desplazamiento</option>
                    <option value="pagination">Paginación</option>
                </select>
            </div>
            <div v-if="viewMode === 'pagination'" class="control">
                <label>Mostrar:</label>
                <select v-model="itemsPerPage" @change="paginate">
                    <option v-for="option in itemsPerPageOptions" :key="option" :value="option">{{ option }}</option>
                </select>
            </div>
        </div>

        <div v-if="viewMode === 'pagination'">
            <div v-for="category in paginatedCategories" :key="category.n_id_categoria" class="box">
                <input v-model="category.v_titulo" class="input">
                <button class="button is-success is-small" @click="updateCategory(category)"
                    :disabled="isUpdating">Actualizar</button>
                <button class="button is-danger is-small" @click="deleteCategory(category.n_id_categoria)"
                    :disabled="isDeleting">Eliminar</button>
            </div>
            <div class="buttons">
                <button class="button" :disabled="currentPage === 1"
                    @click="changePage(currentPage - 1)">Anterior</button>
                <button v-for="page in getPaginationButtons()" :key="page" :disabled="page === '...'"
                    @click="changePage(page)">
                    {{ page }}
                </button>
                <button class="button" :disabled="currentPage === totalPages"
                    @click="changePage(currentPage + 1)">Siguiente</button>
            </div>
        </div>

        <div v-else class="box">
            <table class="table is-bordered is-striped is-hoverable">
                <thead>
                    <tr>
                        <th @click="sortBy('n_id_categoria')">ID <span :class="getSortClass('n_id_categoria')"></span>
                        </th>
                        <th @click="sortBy('v_titulo')">Título <span :class="getSortClass('v_titulo')"></span></th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="category in filteredCategories" :key="category.n_id_categoria">
                        <td>{{ category.n_id_categoria }}</td>
                        <td><input v-model="category.v_titulo" class="input"></td>
                        <td>
                            <button class="button is-success is-small" @click="updateCategory(category)"
                                :disabled="isUpdating">Actualizar</button>
                            <button class="button is-danger is-small" @click="deleteCategory(category.n_id_categoria)"
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
        async fetchCategories() {
            try {
                const response = await axios.get('/omesa/categorias');
                this.processCategories(response.data);
            } catch (error) {
                this.showMessage('Error al cargar categorías: ' + error.message, 'is-danger');
            }
        },
        processCategories(data) {
            // Mapea los datos de la API a las categorías usando las claves correctas.
            this.categories = Array.from(new Map(data.map(item => [item['n_id_categoria'], item])).values());
            this.applySearch();
        },
        applySearch() {
            const query = this.searchQuery.trim().toLowerCase();
            this.filteredCategories = query ? this.categories.filter(c => c.v_titulo.toLowerCase().includes(query)) : [...this.categories];
            this.sortCategories();
            this.paginate();
        },
        sortCategories() {
            if (this.sortKey) {
                this.filteredCategories.sort((a, b) => {
                    return (a[this.sortKey] < b[this.sortKey] ? -1 : a[this.sortKey] > b[this.sortKey] ? 1 : 0) * (this.sortOrder === 'asc' ? 1 : -1);
                });
            }
        },
        sortBy(key) {
            this.sortKey = this.sortKey === key ? key : key;
            this.sortOrder = this.sortKey === key ? (this.sortOrder === 'asc' ? 'desc' : 'asc') : 'asc';
            this.sortCategories();
            this.paginate();
        },
        getSortClass(key) {
            return this.sortKey === key ? `is-${this.sortOrder}` : '';
        },
        async createCategory() {
            this.isSubmitting = true;
            try {
                await axios.post('/omesa/categorias', { v_titulo: this.newCategory });
                this.showMessage('Categoría creada exitosamente', 'is-success');
                this.newCategory = '';
                this.fetchCategories();
            } catch (error) {
                this.showMessage('Error al crear categoría: ' + error.message, 'is-danger');
            } finally {
                this.isSubmitting = false;
            }
        },
        async updateCategory(category) {
            this.isUpdating = true;
            try {
                await axios.put(`/omesa/categorias${category.n_id_categoria}`, { v_titulo: category.v_titulo });
                this.showMessage('Categoría actualizada exitosamente', 'is-success');

                // Actualizar la categoría localmente sin hacer una nueva petición
                const index = this.categories.findIndex(c => c.n_id_categoria === category.n_id_categoria);
                if (index !== -1) {
                    this.categories[index] = category;
                }
            } catch (error) {
                this.showMessage('Error al actualizar categoría: ' + error.message, 'is-danger');
            } finally {
                this.isUpdating = false;
            }
        }
        ,
        async deleteCategory(id) {
            if (!window.confirm('¿Estás seguro de que quieres eliminar esta categoría?')) {
                return; // Si el usuario cancela, no hacemos nada
            }

            this.isDeleting = true;
            try {
                await axios.delete(`/omesa/categorias${id}`);
                this.showMessage('Categoría eliminada correctamente', 'is-success');
                this.fetchCategories();
            } catch (error) {
                this.showMessage('Error al eliminar categoría: ' + error.message, 'is-danger');
            } finally {
                this.isDeleting = false;
            }
        },
        paginate() {
            this.totalPages = Math.ceil(this.filteredCategories.length / this.itemsPerPage);
            this.changePage(1);
        },
        changePage(page) {
            this.currentPage = page;
            const start = (page - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            this.paginatedCategories = this.filteredCategories.slice(start, end);
        },
        getPaginationButtons() {
            const pages = [];
            const range = 2;
            for (let i = 1; i <= this.totalPages; i++) {
                if (i === 1 || i === this.totalPages || (i >= this.currentPage - range && i <= this.currentPage + range)) {
                    pages.push(i);
                } else if (i === this.currentPage - range - 1 || i === this.currentPage + range + 1) {
                    pages.push('...');
                }
            }
            return pages;
        },
        exportToExcel() {
            import("xlsx").then(xlsx => {
                const ws = xlsx.utils.json_to_sheet(this.categories);
                const wb = xlsx.utils.book_new();
                xlsx.utils.book_append_sheet(wb, ws, "Categorías");
                xlsx.writeFile(wb, "categorías.xlsx");
            });
        },
        exportToPdf() {
            import("jspdf").then(jsPDF => {
                import("jspdf-autotable").then(() => {
                    const doc = new jsPDF.default();
                    doc.text("Categorías", 20, 10);
                    doc.autoTable({
                        head: [["ID", "Título"]],
                        body: this.categories.map(category => [category.n_idcategoria, category.v_titulo]),
                    });
                    doc.save("categorías.pdf");
                });
            });
        },
        showMessage(message, type) {
            this.message = message;
            this.messageClass = type;
            setTimeout(() => {
                this.message = '';
            }, 3000);
        },
    },
    mounted() {
        this.fetchCategories();
    }
};
</script>
