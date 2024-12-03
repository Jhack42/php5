<template>
    <div>
      <h1>Gestión de Productos</h1>

      <!-- Formulario para agregar/editar productos -->
      <div class="formulario">
        <h2>{{ isEdit ? 'Editar Producto' : 'Nuevo Producto' }}</h2>
        <form @submit.prevent="guardarProducto">
          <div>
            <label>Nombre:</label>
            <input v-model="producto.nombre" required />
          </div>
          <div>
            <label>Descripción:</label>
            <input v-model="producto.descripcion" />
          </div>
          <div>
            <label>Precio:</label>
            <input v-model="producto.precio" type="number" step="0.01" required />
          </div>
          <div>
            <label>Stock:</label>
            <input v-model="producto.stock" type="number" required />
          </div>
          <div>
            <label>Fecha de Expiración:</label>
            <input v-model="producto.fecha_expiracion" type="date" />
          </div>
          <button type="submit">{{ isEdit ? 'Actualizar' : 'Agregar' }}</button>
          <button type="button" @click="cancelarEdicion">Cancelar</button>
        </form>
      </div>

      <!-- Barra de búsqueda -->
      <div class="busqueda">
        <label>Buscar:</label>
        <input v-model="search" @input="buscarProductos" placeholder="Buscar por nombre o ID" />
      </div>

      <!-- Tabla de productos -->
      <div class="tabla">
        <table border="1">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Precio</th>
              <th>Stock</th>
              <th>Fecha de Expiración</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="prod in productos" :key="prod.id">
              <td>{{ prod.id }}</td>
              <td>{{ prod.nombre }}</td>
              <td>{{ prod.descripcion }}</td>
              <td>{{ prod.precio }}</td>
              <td>{{ prod.stock }}</td>
              <td>{{ prod.fecha_expiracion }}</td>
              <td>
                <button @click="editarProducto(prod)">Editar</button>
                <button @click="eliminarProducto(prod.id)">Eliminar</button>
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
        productos: [], // Lista de productos
        producto: {
          id: null,
          nombre: "",
          descripcion: "",
          precio: "",
          stock: "",
          fecha_expiracion: "",
        }, // Producto en edición o creación
        isEdit: false, // Indicador de edición
        search: "", // Término de búsqueda
      };
    },
    methods: {
      // Cargar productos desde la API
      cargarProductos() {
        axios.get("/api/productos").then((response) => {
          this.productos = response.data;
        });
      },
      // Guardar un nuevo producto o actualizar uno existente
      guardarProducto() {
        if (this.isEdit) {
          axios
            .put(`/api/productos/${this.producto.id}`, this.producto)
            .then(() => {
              this.cargarProductos();
              this.cancelarEdicion();
            });
        } else {
          axios.post("/api/productos", this.producto).then(() => {
            this.cargarProductos();
            this.cancelarEdicion();
          });
        }
      },
      // Cargar datos del producto en el formulario para editar
      editarProducto(prod) {
        this.producto = { ...prod };
        this.isEdit = true;
      },
      // Eliminar un producto por ID
      eliminarProducto(id) {
        if (confirm("¿Estás seguro de eliminar este producto?")) {
          axios.delete(`/api/productos/${id}`).then(() => {
            this.cargarProductos();
          });
        }
      },
      // Cancelar edición y limpiar formulario
      cancelarEdicion() {
        this.producto = {
          id: null,
          nombre: "",
          descripcion: "",
          precio: "",
          stock: "",
          fecha_expiracion: "",
        };
        this.isEdit = false;
      },
      // Buscar productos por nombre o ID
      buscarProductos() {
        if (this.search.trim() === "") {
          this.cargarProductos();
        } else {
          axios
            .get(`/api/productos/search?search=${this.search}`)
            .then((response) => {
              this.productos = response.data.productos || [];
            });
        }
      },
    },
    mounted() {
      this.cargarProductos(); // Cargar productos al iniciar el componente
    },
  };
  </script>

  <style scoped>
  h1 {
    text-align: center;
  }

  form div {
    margin: 10px 0;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
  }

  th, td {
    padding: 10px;
    text-align: left;
  }

  th {
    background-color: #f2f2f2;
  }

  button {
    margin: 0 5px;
  }
  </style>
