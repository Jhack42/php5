<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menú Desplegable Universidad</title>
  <style>
    /* Estilos generales */
    body {
      font-family: Arial, sans-serif;
    }

    /* Botón principal */
    .menu-btn {
      padding: 10px 20px;
      font-size: 16px;
      background-color: #8B0202; /* Color del botón */
      color: #fff;
      border: none;
      cursor: pointer;
      border-radius: 5px;
      display: inline-block;
    }

    /* Contenedor del menú desplegable */
    .dropdown {
      position: relative;
      display: inline-block;
    }

    /* Menú desplegable oculto */
    .dropdown-content {
      display: none;
      position: absolute;
      background-color: #f9f9f9;
      box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.1);
      padding: 20px;
      border-radius: 5px;
      z-index: 1;
      min-width: 200px;
    }

    /* Opciones de la primera columna (Categorías principales) */
    .dropdown-content .category {
      display: flex;
    }

    .category-list {
      margin: 0;
      padding: 0;
      list-style: none;
      margin-right: 30px;
    }

    .category-list li {
      margin: 10px 0;
      cursor: pointer;
    }

    /* Submenú (opciones internas de cada categoría) */
    .sub-category-list {
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .sub-category-list li {
      margin: 5px 0;
    }

    /* Mostrar el menú al hacer clic */
    .show {
      display: block;
    }
  </style>
</head>
<body>

  <!-- Menú desplegable -->
  <div class="dropdown">
    <button class="menu-btn">Categorías Universidad</button>
    <div class="dropdown-content">
      <div class="category">
        <!-- Primera columna (Categorías principales) -->
        <ul class="category-list">
          <li>Facultades</li>
          <li>Servicios</li>
          <li>Administración</li>
          <li>Vida Estudiantil</li>
        </ul>

        <!-- Segunda columna (Subcategorías) -->
        <ul class="sub-category-list">
          <li>Ingeniería</li>
          <li>Medicina</li>
          <li>Ciencias Sociales</li>
          <li>Artes</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Script para controlar el comportamiento del menú desplegable -->
  <script>
    // Seleccionamos el botón del menú
    var menuBtn = document.querySelector('.menu-btn');
    var dropdownContent = document.querySelector('.dropdown-content');

    // Al hacer clic, alternamos la clase 'show' para mostrar/ocultar el menú
    menuBtn.addEventListener('click', function () {
      dropdownContent.classList.toggle('show');
    });

    // Cerrar el menú si se hace clic fuera de él
    window.addEventListener('click', function (e) {
      if (!menuBtn.contains(e.target) && !dropdownContent.contains(e.target)) {
        dropdownContent.classList.remove('show');
      }
    });
  </script>

</body>
</html>
