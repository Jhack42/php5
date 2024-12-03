<template>
    <div class="container mt-5">
        <h1 class="title has-text-centered">Gestión de Categorías</h1>

        <!-- Formulario de creación o edición -->
        <div class="box">
            <form @submit.prevent="submitForm">
                <div class="field">
                    <label class="label">Título de la Categoría</label>
                    <div class="control">
                        <input class="input" type="text" v-model="categoriaForm.v_titulo"
                            placeholder="Ingresa el título de la categoría" required />
                    </div>
                </div>

                <div class="control">
                    <button class="button is-primary" type="submit">
                        {{ isEdit ? 'Actualizar' : 'Crear' }} Categoría
                    </button>
                </div>
            </form>
        </div>

        <!-- Mostrar categorías -->
        <div class="box">
            <h2 class="subtitle">Categorías existentes</h2>
            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="categoria in categorias" :key="categoria.n_id_categoria">
                        <td>{{ categoria.n_id_categoria }}</td>
                        <td>{{ categoria.v_titulo }}</td>
                        <td>
                            <button class="button is-warning is-small" @click="editCategoria(categoria)">
                                Editar
                            </button>
                            <button class="button is-danger is-small"
                                @click="deleteCategoria(categoria.n_id_categoria)">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            categorias: [],
            categoriaForm: {
                v_titulo: "",
            },
            isEdit: false,
            editId: null,
        };
    },
    methods: {
        // Cargar todas las categorías
        async fetchCategorias() {
            try {
                const response = await axios.get("http://127.0.0.1:8000/SmtrCategoria");
                this.categorias = response.data;
            } catch (error) {
                console.error("Error al cargar categorías:", error);
            }
        },

        // Crear o actualizar una categoría
        async submitForm() {
            if (this.isEdit) {
                // Actualizar categoría
                await this.updateCategoria(this.editId);
            } else {
                // Crear nueva categoría
                await this.createCategoria();
            }
            this.resetForm();
        },

        // Crear una nueva categoría
        async createCategoria() {
            try {
                await axios.post("http://127.0.0.1:8000/SmtrCategoria", {
                    v_titulo: this.categoriaForm.v_titulo,
                });
                this.fetchCategorias(); // Refrescar la lista
            } catch (error) {
                console.error("Error al crear categoría:", error);
            }
        },

        // Editar una categoría
        editCategoria(categoria) {
            this.categoriaForm.v_titulo = categoria.v_titulo;
            this.isEdit = true;
            this.editId = categoria.n_id_categoria;
        },

        // Actualizar una categoría
        async updateCategoria(id) {
            try {
                await axios.put(`http://127.0.0.1:8000/SmtrCategoria/${id}`, {
                    v_titulo: this.categoriaForm.v_titulo,
                });
                this.fetchCategorias(); // Refrescar la lista
            } catch (error) {
                console.error("Error al actualizar categoría:", error);
            }
        },

        // Eliminar una categoría
        async deleteCategoria(id) {
            try {
                await axios.delete(`http://127.0.0.1:8000/SmtrCategoria/${id}`);
                this.fetchCategorias(); // Refrescar la lista
            } catch (error) {
                console.error("Error al eliminar categoría:", error);
            }
        },

        // Restablecer el formulario
        resetForm() {
            this.categoriaForm.v_titulo = "";
            this.isEdit = false;
            this.editId = null;
        },
    },
    mounted() {
        this.fetchCategorias();
    },
};
</script>

<style scoped>
/* Puedes agregar estilos personalizados aquí */
.container {
    max-width: 800px;
}

.table th,
.table td {
    text-align: center;
}
</style>
