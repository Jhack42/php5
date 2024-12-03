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
            <div class="pagination-controls">
                <label for="itemsPerPage">Mostrar</label>
                <select v-model="itemsPerPage" @change="updateItemsPerPage">
                    <option v-for="option in itemsPerPageOptions" :key="option" :value="option">{{ option }}</option>
                </select>
            </div>
        </div>
        <div>
            <div v-for="category in paginatedCategories" :key="category.n_idcategoria" class="category-item">
                <input v-model="category.v_titulo">
                <button @click="updateCategory(category)" class="update" :disabled="isUpdating">Actualizar</button>
                <button @click="deleteCategory(category.n_idcategoria)" class="delete" :disabled="isDeleting">Eliminar</button>
            </div>
            <div class="pagination">
                <button :disabled="currentPage === 1" @click="changePage(currentPage - 1)">&laquo; Anterior</button>
                <button v-for="page in getPaginationButtons()" :key="page" :disabled="page === '...'" @click="changePage(page)">
                    {{ page }}
                </button>
                <button :disabled="currentPage === totalPages" @click="changePage(currentPage + 1)">Siguiente &raquo;</button>
            </div>
        </div>
        <div v-if="message" :class="['notification', messageClass]">{{ message }}</div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            categories: [],
            paginatedCategories: [],
            newCategory: "",
            message: "",
            messageClass: "",
            searchQuery: "",
            itemsPerPage: 5,
            itemsPerPageOptions: [1, 3, 5, 7, 10, 100],
            currentPage: 1,
            totalPages: 1,
            isSubmitting: false,
            isUpdating: false,
            isDeleting: false,
        };
    },
    methods: {
        async fetchCategories() {
            try {
                const response = await axios.get("/api/categoria");
                this.categories = response.data?.categorias || [];
                this.applySearch();
            } catch (error) {
                console.error("Error al obtener las categorías:", error);
                alert(error.response?.data?.message || "Error al obtener las categorías");
            }
        },

        applySearch() {
            const query = this.searchQuery.trim().toLowerCase();
            const filteredCategories = query
                ? this.categories.filter((category) =>
                      category.v_titulo.toLowerCase().includes(query)
                  )
                : [...this.categories];
            this.paginate(filteredCategories);
        },

        paginate(categories = this.categories) {
            this.totalPages = Math.ceil(categories.length / this.itemsPerPage);
            this.changePage(1, categories); // Reinicia en la primera página al cambiar elementos visibles
        },

        changePage(page, categories = this.categories) {
            this.currentPage = page;
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            this.paginatedCategories = categories.slice(start, end);
        },

        updateItemsPerPage() {
            this.currentPage = 1; // Reiniciar a la primera página cuando se cambia el valor
            this.paginate();
        },

        getPaginationButtons() {
            const buttons = [];
            for (let i = 1; i <= this.totalPages; i++) {
                buttons.push(i);
            }
            return buttons;
        },

        async createCategory() {
            this.isSubmitting = true;
            try {
                await axios.post("/api/categoria/create", { v_titulo: this.newCategory });
                this.showMessage("Categoría creada exitosamente", "success");
                this.fetchCategories();
                this.newCategory = "";
            } catch (error) {
                this.showMessage("Error al crear categoría: " + error.message, "error");
            } finally {
                this.isSubmitting = false;
            }
        },

        async updateCategory(category) {
            this.isUpdating = true;
            try {
                await axios.put(`/api/categoria/update/${category.n_id_categoria}`, { v_titulo: category.v_titulo });
                this.showMessage("Categoría actualizada exitosamente", "success");
                this.fetchCategories();
            } catch (error) {
                this.showMessage("Error al actualizar categoría: " + error.message, "error");
            } finally {
                this.isUpdating = false;
            }
        },

        async deleteCategory(id) {
            this.isDeleting = true;
            try {
                await axios.delete(`/api/categoria/delete/${id}`);
                this.showMessage("Categoría eliminada correctamente", "success");
                this.fetchCategories();
            } catch (error) {
                this.showMessage("Error al eliminar categoría: " + error.message, "error");
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
                        body: this.categories.map((category) => [category.n_id_categoria, category.v_titulo]),
                    });
                    doc.save("categorías.pdf");
                });
            });
        },

        showMessage(message, type) {
            this.message = message;
            this.messageClass = type === "success" ? "success" : "error";
            setTimeout(() => {
                this.message = "";
                this.messageClass = "";
            }, 3000);
        },
    },

    mounted() {
        this.fetchCategories();
    },
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
