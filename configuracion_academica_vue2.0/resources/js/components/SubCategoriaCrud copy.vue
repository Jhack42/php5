<template>
    <div class="container">
        <h1>Test CRUD SubCategorías</h1>
        <div class="toolbar">
            <div class="top-row">
                <input v-model="newSubCategory.description" placeholder="Nueva SubCategoría" :disabled="isSubmitting">
                <input v-model="newSubCategory.categoryId" placeholder="ID Categoría" type="number" :disabled="isSubmitting">
                <button @click="createSubCategory" :disabled="!newSubCategory.description.trim() || isSubmitting">Crear SubCategoría</button>
            </div>
            <div class="second-row">
                <input v-model="searchQuery" @input="applySearch" placeholder="Buscar SubCategoría">
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
            <div v-for="subCategory in paginatedSubCategories" :key="subCategory.n_id_sub_categoria" class="sub-category-item">
                <input v-model="subCategory.v_descripcion" placeholder="Descripción">
                <button @click="updateSubCategory(subCategory)" class="update" :disabled="isUpdating">Actualizar</button>
                <button @click="deleteSubCategory(subCategory.n_id_sub_categoria)" class="delete" :disabled="isDeleting">Eliminar</button>
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
            subCategories: [],
            paginatedSubCategories: [],
            newSubCategory: { description: "", categoryId: "" },
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
        async fetchSubCategories() {
            try {
                const response = await axios.get("/api/testsupcategoria");
                this.subCategories = response.data?.subCategories || [];
                this.applySearch();
            } catch (error) {
                console.error("Error al obtener las subcategorías:", error);
                alert(error.response?.data?.message || "Error al obtener las subcategorías");
            }
        },

        applySearch() {
            const query = this.searchQuery.trim().toLowerCase();
            const filteredSubCategories = query
                ? this.subCategories.filter((subCategory) =>
                      subCategory.v_descripcion.toLowerCase().includes(query)
                  )
                : [...this.subCategories];
            this.paginate(filteredSubCategories);
        },

        paginate(subCategories = this.subCategories) {
            this.totalPages = Math.ceil(subCategories.length / this.itemsPerPage);
            this.changePage(1, subCategories);
        },

        changePage(page, subCategories = this.subCategories) {
            this.currentPage = page;
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            this.paginatedSubCategories = subCategories.slice(start, end);
        },

        updateItemsPerPage() {
            this.currentPage = 1;
            this.paginate();
        },

        getPaginationButtons() {
            const buttons = [];
            for (let i = 1; i <= this.totalPages; i++) {
                buttons.push(i);
            }
            return buttons;
        },

        async createSubCategory() {
            this.isSubmitting = true;
            try {
                await axios.post("/api/testsupcategoria/nuevo", {
                    n_id_categoria: this.newSubCategory.categoryId,
                    v_descripcion: this.newSubCategory.description,
                });
                this.showMessage("SubCategoría creada exitosamente", "success");
                this.fetchSubCategories();
                this.newSubCategory = { description: "", categoryId: "" };
            } catch (error) {
                this.showMessage("Error al crear subcategoría: " + error.message, "error");
            } finally {
                this.isSubmitting = false;
            }
        },

        async updateSubCategory(subCategory) {
            this.isUpdating = true;
            try {
                await axios.put(`/api/testsupcategoria/editar/${subCategory.n_id_sub_categoria}`, {
                    v_descripcion: subCategory.v_descripcion,
                });
                this.showMessage("SubCategoría actualizada exitosamente", "success");
                this.fetchSubCategories();
            } catch (error) {
                this.showMessage("Error al actualizar subcategoría: " + error.message, "error");
            } finally {
                this.isUpdating = false;
            }
        },

        async deleteSubCategory(id) {
            this.isDeleting = true;
            try {
                await axios.put(`/api/testsupcategoria/eliminar/${id}`);
                this.showMessage("SubCategoría eliminada correctamente", "success");
                this.fetchSubCategories();
            } catch (error) {
                this.showMessage("Error al eliminar subcategoría: " + error.message, "error");
            } finally {
                this.isDeleting = false;
            }
        },

        exportToExcel() {
            import("xlsx").then((xlsx) => {
                const ws = xlsx.utils.json_to_sheet(this.subCategories);
                const wb = xlsx.utils.book_new();
                xlsx.utils.book_append_sheet(wb, ws, "SubCategorías");
                xlsx.writeFile(wb, "subcategorias.xlsx");
            });
        },

        exportToPdf() {
            import("jspdf").then((jsPDF) => {
                import("jspdf-autotable").then(() => {
                    const doc = new jsPDF.default();
                    doc.text("SubCategorías", 20, 10);
                    doc.autoTable({
                        head: [["ID", "Descripción"]],
                        body: this.subCategories.map((subCategory) => [
                            subCategory.n_id_sub_categoria,
                            subCategory.v_descripcion,
                        ]),
                    });
                    doc.save("subcategorias.pdf");
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
        this.fetchSubCategories();
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
