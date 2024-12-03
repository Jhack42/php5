<template>
    <div>
        <h1>CRUD de Categorías</h1>

        <!-- Formulario para agregar/editar categorías -->
        <form @submit.prevent="saveCategory">
            <div>
                <label for="v_titulo">Título de la categoría:</label>
                <input v-model="category.v_titulo" type="text" id="v_titulo" required />
            </div>
            <button type="submit">{{ isEditing ? 'Actualizar' : 'Agregar' }} Categoría</button>
        </form>

        <!-- Listado de categorías -->
        <div>
            <h2>Listado de Categorías</h2>
            <ul>
                <li v-for="category in categories" :key="category.n_id_categoria">
                    {{ category.v_titulo }}
                    <button @click="editCategory(category)">Editar</button>
                    <button @click="deleteCategory(category.n_id_categoria)">Eliminar</button>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            categories: [], // Lista de categorías
            category: { v_titulo: '' }, // Datos de la categoría actual (para agregar/editar)
            isEditing: false, // Indicador de si estamos editando o no
            editingId: null, // ID de la categoría que estamos editando
        };
    },

    mounted() {
        this.fetchCategories(); // Cargar las categorías cuando el componente se monte
    },

    methods: {
        // Obtener todas las categorías
        async fetchCategories() {
            try {
                const response = await axios.get('http://127.0.0.1:8000/SmtrCategoria');
                this.categories = response.data;
            } catch (error) {
                console.error('Error al cargar las categorías:', error);
            }
        },

        // Crear o actualizar categoría
        async saveCategory() {
            if (this.isEditing) {
                await this.updateCategory();
            } else {
                await this.createCategory();
            }
        },

        // Crear una nueva categoría
        async createCategory() {
            this.isSubmitting = true;
            try {
                const response = await axios.post('http://127.0.0.1:8000/SmtrCategoria', {
                    V_TITULO: this.category.v_titulo
                });
                this.categories.push(response.data);
                this.category.v_titulo = ''; // Limpiar el formulario
            } catch (error) {
                console.error('Error al crear la categoría:', error.response ? error.response.data : error.message);
                this.showMessage('Error al crear la categoría: ' + (error.response ? error.response.data : error.message), 'error');
            } finally {
                this.isSubmitting = false;
            }
        }
        ,

        // Editar una categoría (cargar datos para edición)
        editCategory(category) {
            this.category = { v_titulo: category.v_titulo };
            this.isEditing = true;
            this.editingId = category.n_id_categoria;
        },

        // Actualizar una categoría
        async updateCategory() {
            try {
                const response = await axios.put(`http://127.0.0.1:8000/SmtrCategoria/${this.editingId}`, { V_TITULO: this.category.v_titulo });
                const index = this.categories.findIndex(c => c.n_id_categoria === this.editingId);
                this.categories[index] = response.data;
                this.category.v_titulo = ''; // Limpiar el formulario
                this.isEditing = false;
            } catch (error) {
                console.error('Error al actualizar la categoría:', error);
            }
        },

        // Eliminar una categoría
        async deleteCategory(id) {
            try {
                await axios.delete(`http://127.0.0.1:8000/SmtrCategoria/${id}`);
                this.categories = this.categories.filter(c => c.n_id_categoria !== id);
            } catch (error) {
                console.error('Error al eliminar la categoría:', error);
            }
        },
    },
};
</script>

<style scoped>
/* Estilos básicos */
form {
    margin-bottom: 20px;
}

button {
    margin-left: 10px;
    padding: 5px 10px;
    cursor: pointer;
}

ul {
    list-style-type: none;
    padding: 0;
}

li {
    margin: 10px 0;
}
</style>
