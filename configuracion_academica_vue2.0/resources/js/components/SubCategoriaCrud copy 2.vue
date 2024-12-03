<template>
    <div>
      <h1>Gestión de Subcategorías</h1>

      <!-- Formulario para crear o editar una subcategoría -->
      <form @submit.prevent="submitForm">
        <div>
          <label for="descripcion">Descripción:</label>
          <input
            type="text"
            id="descripcion"
            v-model="form.v_descripcion"
            placeholder="Descripción de la subcategoría"
          />
        </div>
        <div>
          <label for="categoria">ID de Categoría:</label>
          <input
            type="number"
            id="categoria"
            v-model="form.n_id_categoria"
            placeholder="ID de la categoría"
          />
        </div>
        <button type="submit">{{ isEditing ? "Actualizar" : "Crear" }}</button>
      </form>

      <!-- Tabla para mostrar las subcategorías -->
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Descripción</th>
            <th>ID Categoría</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="subcategoria in subcategorias" :key="subcategoria.n_id_sub_categoria">
            <td>{{ subcategoria.n_id_sub_categoria }}</td>
            <td>{{ subcategoria.v_descripcion }}</td>
            <td>{{ subcategoria.n_id_categoria }}</td>
            <td>
              <button @click="editSubCategoria(subcategoria)">Editar</button>
              <button @click="deleteSubCategoria(subcategoria.n_id_sub_categoria)">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>

  <script>
  import axios from "axios";

  export default {
    data() {
      return {
        subcategorias: [], // Lista de subcategorías
        form: {
          n_id_categoria: "",
          v_descripcion: "",
        },
        isEditing: false,
        editingId: null,
      };
    },
    methods: {
      // Obtener todas las subcategorías
      async fetchSubCategorias() {
        try {
          const response = await axios.get("/api/supcategoria");
          this.subcategorias = response.data.data; // Asegúrate de que el campo coincida con la API
        } catch (error) {
          console.error("Error al obtener subcategorías:", error);
        }
      },
      // Crear o actualizar una subcategoría
      async submitForm() {
        try {
          if (this.isEditing) {
            // Actualizar subcategoría
            await axios.put(`/api/supcategoria/editar/${this.editingId}`, this.form);
            alert("Subcategoría actualizada correctamente");
          } else {
            // Crear nueva subcategoría
            await axios.post("/api/supcategoria/nuevo", this.form);
            alert("Subcategoría creada correctamente");
          }
          this.resetForm();
          this.fetchSubCategorias();
        } catch (error) {
          console.error("Error al guardar la subcategoría:", error);
        }
      },
      // Preparar para editar una subcategoría
      editSubCategoria(subcategoria) {
        this.isEditing = true;
        this.editingId = subcategoria.n_id_sub_categoria;
        this.form.n_id_categoria = subcategoria.n_id_categoria;
        this.form.v_descripcion = subcategoria.v_descripcion;
      },
      // Eliminar una subcategoría
      async deleteSubCategoria(id) {
        try {
          await axios.put(`/api/supcategoria/eliminar/${id}`);
          alert("Subcategoría eliminada correctamente");
          this.fetchSubCategorias();
        } catch (error) {
          console.error("Error al eliminar la subcategoría:", error);
        }
      },
      // Reiniciar el formulario
      resetForm() {
        this.form.n_id_categoria = "";
        this.form.v_descripcion = "";
        this.isEditing = false;
        this.editingId = null;
      },
    },
    mounted() {
      this.fetchSubCategorias();
    },
  };
  </script>

  <style scoped>
  /* Estilos básicos para el componente */
  table {
    width: 100%;
    border-collapse: collapse;
  }

  th, td {
    border: 1px solid #ddd;
    padding: 8px;
  }

  th {
    background-color: #f4f4f4;
    text-align: left;
  }

  button {
    margin: 0 5px;
  }

  form {
    margin-bottom: 20px;
  }

  form div {
    margin-bottom: 10px;
  }
  </style>
