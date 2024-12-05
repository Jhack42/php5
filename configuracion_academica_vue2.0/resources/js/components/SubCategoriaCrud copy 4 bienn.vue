<template>
    <div>
      <h1>Subcategorías</h1>

      <!-- Formulario para crear nueva subcategoría -->
      <div>
        <h2>Crear Subcategoría</h2>
        <form @submit.prevent="crearSubCategoria">
          <label for="n_id_categoria">ID Categoría:</label>
          <input type="number" v-model="n_id_categoria" required />

          <label for="v_descripcion">Descripción:</label>
          <input type="text" v-model="v_descripcion" required />

          <button type="submit">Crear Subcategoría</button>
        </form>
      </div>

      <!-- Tabla de subcategorías -->
      <h2>Lista de Subcategorías</h2>
      <table border="1" cellpadding="5" cellspacing="0">
        <thead>
          <tr>
            <th>ID Subcategoría</th>
            <th>ID Categoría</th>
            <th>Descripción</th>
            <th>Permiso</th>
            <th>ID Análisis</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <!-- Mostrar las subcategorías -->
          <tr v-for="subCategoria in subCategorias" :key="subCategoria.n_id_sub_categoria">
            <td>{{ subCategoria.n_id_sub_categoria }}</td>
            <td>{{ subCategoria.n_id_categoria }}</td>
            <td>{{ subCategoria.v_descripcion }}</td>
            <td>{{ subCategoria.c_permiso || 'N/A' }}</td>
            <td>{{ subCategoria.n_id_analisis || 'N/A' }}</td>
            <td>
              <button @click="editarSubCategoria(subCategoria)">Editar</button>
              <button @click="eliminarSubCategoria(subCategoria.n_id_sub_categoria)">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Formulario para editar subcategoría -->
      <div v-if="editarFormVisible">
        <h2>Editar Subcategoría</h2>
        <form @submit.prevent="actualizarSubCategoria">
          <input type="hidden" v-model="subCategoriaId" />

          <label for="v_descripcion_editar">Descripción:</label>
          <input type="text" v-model="v_descripcion_editar" required />

          <button type="submit">Actualizar Subcategoría</button>
          <button @click="cancelarEdicion">Cancelar</button>
        </form>
      </div>
    </div>
  </template>

  <script>
  export default {
    data() {
      return {
        subCategorias: [],
        n_id_categoria: '',
        v_descripcion: '',
        // Datos para edición
        editarFormVisible: false,
        subCategoriaId: null,
        v_descripcion_editar: '',
      };
    },
    mounted() {
      this.fetchSubCategorias();
    },
    methods: {
      // Método para obtener las subcategorías
      async fetchSubCategorias() {
        try {
          const response = await fetch('http://127.0.0.1:8000/api/supcategoria');
          const data = await response.json();

          if (data.subCategorias) {
            this.subCategorias = data.subCategorias;
          } else {
            console.error('No se encontraron subcategorías.');
          }
        } catch (error) {
          console.error('Error al obtener las subcategorías:', error);
        }
      },

      // Método para crear una nueva subcategoría
      async crearSubCategoria() {
        try {
          const response = await fetch('http://127.0.0.1:8000/api/supcategoria/nuevo', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              n_id_categoria: this.n_id_categoria,
              v_descripcion: this.v_descripcion,
            }),
          });

          const data = await response.json();

          if (response.ok) {
            // Agregar la nueva subcategoría a la lista
            this.subCategorias.push(data.subCategoria);
            // Limpiar los campos del formulario
            this.n_id_categoria = '';
            this.v_descripcion = '';
            alert(data.message);
          } else {
            alert(data.message || 'Error al crear la subcategoría');
          }
        } catch (error) {
          console.error('Error al crear la subcategoría:', error);
        }
      },

      // Método para editar subcategoría
      editarSubCategoria(subCategoria) {
        this.subCategoriaId = subCategoria.n_id_sub_categoria;
        this.v_descripcion_editar = subCategoria.v_descripcion;
        this.editarFormVisible = true;
      },

      // Método para actualizar la subcategoría
      async actualizarSubCategoria() {
        try {
          const response = await fetch(`http://127.0.0.1:8000/api/supcategoria/${this.subCategoriaId}/editar`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              v_descripcion: this.v_descripcion_editar,
            }),
          });

          const data = await response.json();

          if (response.ok) {
            // Actualizar la subcategoría en la lista
            const index = this.subCategorias.findIndex(sub => sub.n_id_sub_categoria === this.subCategoriaId);
            this.subCategorias[index].v_descripcion = this.v_descripcion_editar;

            // Limpiar campos y ocultar formulario
            this.v_descripcion_editar = '';
            this.editarFormVisible = false;

            alert(data.message);
          } else {
            alert(data.message || 'Error al actualizar la subcategoría');
          }
        } catch (error) {
          console.error('Error al actualizar la subcategoría:', error);
        }
      },

      // Método para eliminar subcategoría
      async eliminarSubCategoria(id) {
        try {
          const response = await fetch(`http://127.0.0.1:8000/api/supcategoria/${id}/eliminar`, {
            method: 'DELETE',
          });

          const data = await response.json();

          if (response.ok) {
            // Eliminar la subcategoría de la lista
            this.subCategorias = this.subCategorias.filter(sub => sub.n_id_sub_categoria !== id);
            alert(data.message);
          } else {
            alert(data.message || 'Error al eliminar la subcategoría');
          }
        } catch (error) {
          console.error('Error al eliminar la subcategoría:', error);
        }
      },

      // Cancelar la edición
      cancelarEdicion() {
        this.editarFormVisible = false;
      },
    },
  };
  </script>

  <style scoped>
  table {
    width: 100%;
    border-collapse: collapse;
  }

  th, td {
    text-align: left;
    padding: 8px;
  }

  th {
    background-color: #f2f2f2;
  }

  button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
  }

  button:hover {
    background-color: #45a049;
  }
  </style>
