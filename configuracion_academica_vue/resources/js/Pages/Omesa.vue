<template>
    <div class="smtr-categoria">
      <h1>Categorías</h1>

      <!-- Mensajes de estado -->
      <div v-if="statusMessage" :class="statusClass">
        <p>{{ statusMessage }}</p>
      </div>

      <!-- Formulario para agregar o editar una categoría -->
      <div v-if="isEditMode">
        <h3>Editar Categoría</h3>
        <input v-model="formData.v_titulo" placeholder="Título" />
        <button @click="updateCategoria">Actualizar</button>
        <button @click="cancelEdit">Cancelar</button>
      </div>
      <div v-else>
        <h3>Agregar Nueva Categoría</h3>
        <input v-model="formData.v_titulo" placeholder="Título" />
        <button @click="addCategoria">Agregar</button>
      </div>

      <!-- Tabla de Categorías -->
      <table v-if="categorias.length">
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
              <button @click="editCategoria(categoria)">Editar</button>
              <button @click="deleteCategoria(categoria.n_id_categoria)">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-else>No hay categorías disponibles</p>
    </div>
  </template>

  <script>
  import axios from "axios";

  export default {
    data() {
      return {
        categorias: [], // Para almacenar las categorías
        formData: {
          n_id_categoria: null, // ID de la categoría (para edición)
          v_titulo: "", // Título de la categoría
        },
        isEditMode: false, // Flag para saber si estamos en modo edición
        statusMessage: "", // Mensaje de estado (éxito o error)
        statusClass: "", // Clase CSS para el mensaje de estado
      };
    },
    methods: {
      // Cargar todas las categorías
      async loadCategorias() {
        try {
          const response = await axios.get("http://127.0.0.1:8000/api/SmtrCategoria");
          this.categorias = response.data.data;
        } catch (error) {
          this.showMessage("Error al cargar las categorías: " + this.getErrorMessage(error), "error");
        }
      },

      // Agregar una nueva categoría
      async addCategoria() {
        try {
          const response = await axios.post("http://127.0.0.1:8000/api/SmtrCategoria", {
            v_titulo: this.formData.v_titulo,
          });
          this.categorias.push(response.data.data); // Agregar la categoría a la lista
          this.formData.v_titulo = ""; // Limpiar el campo de entrada
          this.showMessage("Categoría agregada exitosamente", "success");
        } catch (error) {
          this.showMessage("Error al agregar la categoría: " + this.getErrorMessage(error), "error");
        }
      },

      // Editar una categoría existente
      editCategoria(categoria) {
        this.formData = { ...categoria }; // Rellenamos los datos del formulario con la categoría seleccionada
        this.isEditMode = true; // Activar el modo de edición
      },

      // Cancelar la edición
      cancelEdit() {
        this.isEditMode = false;
        this.formData = { n_id_categoria: null, v_titulo: "" }; // Limpiar los campos
      },

      // Actualizar una categoría
      async updateCategoria() {
        try {
          const response = await axios.put(
            `http://127.0.0.1:8000/api/SmtrCategoria/${this.formData.n_id_categoria}`,
            {
              v_titulo: this.formData.v_titulo,
            }
          );
          // Actualizamos la categoría editada en el arreglo
          const index = this.categorias.findIndex(
            (cat) => cat.n_id_categoria === this.formData.n_id_categoria
          );
          this.$set(this.categorias, index, response.data.data);
          this.cancelEdit(); // Salir del modo edición
          this.showMessage("Categoría actualizada exitosamente", "success");
        } catch (error) {
          this.showMessage("Error al actualizar la categoría: " + this.getErrorMessage(error), "error");
        }
      },

      // Eliminar una categoría
      async deleteCategoria(id) {
        try {
          await axios.delete(`http://127.0.0.1:8000/api/SmtrCategoria/${id}`);
          this.categorias = this.categorias.filter(
            (categoria) => categoria.n_id_categoria !== id
          );
          this.showMessage("Categoría eliminada exitosamente", "success");
        } catch (error) {
          this.showMessage("Error al eliminar la categoría: " + this.getErrorMessage(error), "error");
        }
      },

      // Mostrar un mensaje de estado
      showMessage(message, type) {
        this.statusMessage = message;
        this.statusClass = type === "success" ? "success-message" : "error-message";
        setTimeout(() => {
          this.statusMessage = ""; // Limpiar el mensaje después de 5 segundos
          this.statusClass = "";
        }, 5000);
      },

      // Obtener un mensaje de error más detallado
      getErrorMessage(error) {
        if (error.response) {
          // Error de respuesta del servidor
          return error.response.data.message || error.response.data.error || "Error desconocido";
        } else if (error.request) {
          // No se recibió respuesta del servidor
          return "No se recibió respuesta del servidor.";
        } else {
          // Error al configurar la solicitud
          return "Error al configurar la solicitud.";
        }
      },
    },
    mounted() {
      this.loadCategorias(); // Cargar las categorías cuando el componente se monta
    },
  };
  </script>

  <style scoped>
  /* Estilos para los mensajes de estado */
  .success-message {
    color: green;
  }

  .error-message {
    color: red;
  }
  </style>


  <style scoped>
  /* Estilos para la tabla y el formulario */
  .smtr-categoria {
    margin: 20px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }

  table th,
  table td {
    padding: 10px;
    border: 1px solid #ddd;
  }

  button {
    margin-left: 10px;
    padding: 5px 10px;
    cursor: pointer;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
  }

  button:hover {
    background-color: #0056b3;
  }

  input {
    padding: 8px;
    margin: 5px;
    width: 200px;
    border-radius: 5px;
    border: 1px solid #ddd;
  }

  /* Estilos para los mensajes de estado */
  .success-message {
    color: green;
    background-color: #d4edda;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
  }

  .error-message {
    color: red;
    background-color: #f8d7da;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
  }
  </style>
