<template>
    <div>
      <h1>Gestión de Subcategorías</h1>
      <form @submit.prevent="crearSubcategoria">
        <input v-model="nuevaSubcategoria.n_id_categoria" placeholder="ID Categoría" required />
        <input v-model="nuevaSubcategoria.v_descripcion" placeholder="Descripción" required />
        <button type="submit">Crear Subcategoría</button>
      </form>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Categoría</th>
            <th>Descripción</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sub in subcategorias" :key="sub.n_id_sub_categoria">
            <td>{{ sub.n_id_sub_categoria }}</td>
            <td>{{ sub.n_id_categoria }}</td>
            <td>{{ sub.v_descripcion }}</td>
            <td>
              <button @click="editarSubcategoria(sub)">Editar</button>
              <button @click="eliminarSubcategoria(sub.n_id_sub_categoria)">Eliminar</button>
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
        subcategorias: [],
        nuevaSubcategoria: {
          n_id_categoria: "",
          v_descripcion: "",
        },
      };
    },
    methods: {
      async obtenerSubcategorias() {
        try {
          const { data } = await axios.get("/api/supcategoria");
          this.subcategorias = data.subCategorias;
        } catch (error) {
          console.error("Error al obtener subcategorías", error);
        }
      },
      async crearSubcategoria() {
        try {
          await axios.get("/api/supcategoria/nuevo", { params: this.nuevaSubcategoria });
          this.obtenerSubcategorias();
          this.nuevaSubcategoria = { n_id_categoria: "", v_descripcion: "" };
        } catch (error) {
          console.error("Error al crear subcategoría", error);
        }
      },
      async editarSubcategoria(sub) {
        const nuevaDescripcion = prompt("Nueva descripción:", sub.v_descripcion);
        if (!nuevaDescripcion) return;

        try {
          await axios.put(`/api/supcategoria/editar/${sub.n_id_sub_categoria}`, {
            v_descripcion: nuevaDescripcion,
          });
          this.obtenerSubcategorias();
        } catch (error) {
          console.error("Error al editar subcategoría", error);
        }
      },
      async eliminarSubcategoria(id) {
        if (!confirm("¿Estás seguro de eliminar esta subcategoría?")) return;

        try {
          await axios.put(`/api/supcategoria/eliminar/${id}`);
          this.obtenerSubcategorias();
        } catch (error) {
          console.error("Error al eliminar subcategoría", error);
        }
      },
    },
    mounted() {
      this.obtenerSubcategorias();
    },
  };
  </script>

<style>
h1 {
    text-align: center;
    margin-bottom: 20px;
    font-family: Arial, sans-serif;
}

form {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    justify-content: center;
}

input {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

button {
    padding: 8px 12px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

button:hover {
    background-color: #0056b3;
}

ul {
    list-style-type: none;
    padding: 0;
}

li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #f9f9f9;
}

li span {
    font-size: 16px;
}

div[isEditing] {
    margin-top: 20px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #f0f8ff;
}
</style>
