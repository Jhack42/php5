<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Aplicación</title>
  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="/php5/public/css/styles.css">
</head>
<body>
  <div class="container">
    <div class="row">
      <!-- Barra lateral -->
      <nav class="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a href="#" data-view="home" class="nav-link">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#submenu1" role="button" aria-expanded="false" aria-controls="submenu1">
              Acerca de
            </a>
            <div class="collapse" id="submenu1">
              <ul class="nav">
                <li class="nav-item">
                  <a href="#" data-view="team" class="nav-link">Equipo</a>
                </li>
                <li class="nav-item">
                  <a href="#" data-view="history" class="nav-link">Historia</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#submenu2" role="button" aria-expanded="false" aria-controls="submenu2">
              Servicios
            </a>
            <div class="collapse" id="submenu2">
              <ul class="nav">
                <li class="nav-item">
                  <a href="#" data-view="consulting" class="nav-link">Consultoría</a>
                </li>
                <li class="nav-item">
                  <a href="#" data-view="support" class="nav-link">Soporte</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a href="#" data-view="contact" class="nav-link">Contacto</a>
          </li>
        </ul>
      </nav>
      <!-- Contenido principal -->
      <div class="content">
        <main id="content">
          <!-- Botones con nuevo diseño -->
          <a href="#" class="button">Enviar</a>
          <a href="#" class="button-secondary">Cancelar</a>
        </main>
      </div>
    </div>
  </div>

  <!-- Archivo JavaScript para manejar las peticiones AJAX -->
  <script src="/php5/public/js/app.js"></script>
</body>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Selecciona todos los enlaces que colapsan submenús
    const collapsibleLinks = document.querySelectorAll('[data-toggle="collapse"]');

    collapsibleLinks.forEach(link => {
      link.addEventListener('click', function (event) {
        event.preventDefault(); // Prevenir el comportamiento predeterminado del enlace

        // Obtener el ID del submenú al que se refiere el enlace
        const targetId = link.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);

        // Alternar la clase 'show' para desplegar o colapsar el submenú
        if (targetElement.classList.contains('show')) {
          targetElement.classList.remove('show');
        } else {
          targetElement.classList.add('show');
        }
      });
    });
  });
</script>
</html>
