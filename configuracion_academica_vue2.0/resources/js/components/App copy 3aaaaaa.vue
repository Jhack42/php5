<template>
    <div class="categoria-crud">
        <h1>CRUD Categorías</h1>

        <!-- Crear o Editar Categoría -->
        <form @submit.prevent="isEditing ? updateCategoria() : createCategoria()">
            <div>
                <label for="titulo">Título:</label>
                <input v-model="form.v_titulo" id="titulo" type="text" placeholder="Ingrese el título" required />
            </div>
            <button type="submit">{{ isEditing ? 'Actualizar' : 'Crear' }}</button>
        </form>

        <!-- Listado de Categorías -->
        <h2>Listado de Categorías</h2>
        <button @click="fetchCategorias">Actualizar Listado</button>
        <ul>
            <li v-for="categoria in categorias" :key="categoria.n_id_categoria">
                <strong>{{ categoria.n_id_categoria }}</strong> - {{ categoria.v_titulo }}
                <button @click="editCategoria(categoria)">Editar</button>
                <button @click="deleteCategoria(categoria.n_id_categoria)">Eliminar</button>
            </li>
        </ul>

        <!-- Buscar Categoría -->
        <h2>Buscar Categoría</h2>
        <form @submit.prevent="searchCategoria">
            <input v-model="searchTerm" type="text" placeholder="Buscar categoría por título" />
            <button type="submit">Buscar</button>
        </form>
        <div v-if="searchResults.length > 0">
            <h3>Resultados de la Búsqueda:</h3>
            <ul>
                <li v-for="categoria in searchResults" :key="categoria.n_id_categoria">
                    <strong>{{ categoria.n_id_categoria }}</strong> - {{ categoria.v_titulo }}
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            categorias: [], // Listado de categorías
            searchResults: [], // Resultados de búsqueda
            form: {
                v_titulo: "", // Datos del formulario
            },
            isEditing: false, // Modo de edición
            editingId: null, // ID de la categoría que se está editando
            searchTerm: "", // Término de búsqueda
        };
    },
    methods: {
        // Obtener todas las categorías
        async fetchCategorias() {
            try {
                const response = await axios.get("/api/categoria");
                this.categorias = response.data.categorias || [];
            } catch (error) {
                console.error("Error al obtener las categorías:", error);
                alert("Error al obtener las categorías");
            }
        },

        // Crear nueva categoría
        async createCategoria() {
            try {
                const response = await axios.post("/api/categoria/create", this.form);
                this.categorias.push(response.data.categoria);
                this.form.v_titulo = ""; // Limpiar formulario
            } catch (error) {
                console.error("Error al crear la categoría:", error);
                alert("Error al crear la categoría");
            }
        },

        // Editar categoría
        editCategoria(categoria) {
            this.form.v_titulo = categoria.v_titulo;
            this.isEditing = true;
            this.editingId = categoria.n_id_categoria;
        },

        // Actualizar categoría
        async updateCategoria() {
            try {
                await axios.put(`/api/categoria/update/${this.editingId}`, this.form);
                this.fetchCategorias(); // Recargar las categorías
                this.form.v_titulo = ""; // Limpiar formulario
                this.isEditing = false;
                this.editingId = null;
            } catch (error) {
                console.error("Error al actualizar la categoría:", error);
                alert("Error al actualizar la categoría");
            }
        },

        // Eliminar categoría
        async deleteCategoria(id) {
            try {
                await axios.delete(`/api/categoria/delete/${id}`);
                this.categorias = this.categorias.filter((cat) => cat.n_id_categoria !== id);
            } catch (error) {
                console.error("Error al eliminar la categoría:", error);
                alert("Error al eliminar la categoría");
            }
        },

        // Buscar categorías por título
        async searchCategoria() {
            try {
                const response = await axios.get(`/api/categoria/search/${this.searchTerm}`);
                this.searchResults = response.data.categorias || [];
            } catch (error) {
                console.error("Error al buscar las categorías:", error);
                alert("Error al buscar las categorías");
            }
        },
    },
    mounted() {
        this.fetchCategorias(); // Cargar las categorías al montar el componente
    },
};
</script>

<style scoped>
.categoria-crud {
    max-width: 600px;
    margin: 0 auto;
    font-family: Arial, sans-serif;
}

form {
    margin-bottom: 20px;
}

form div {
    margin-bottom: 10px;
}

ul {
    list-style: none;
    padding: 0;
}

ul li {
    margin-bottom: 10px;
}

button {
    margin-left: 10px;
}
</style>
