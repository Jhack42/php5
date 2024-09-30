<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Aplicación</title>
  <!-- Incluir Bulma para estilos -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
  <!-- Estilos personalizados opcionales -->
  <link rel="stylesheet" href="/php5/public/css/styles.css">
</head>
<body>
  <div class="container">
    <div class="columns">
      <!-- Incluir el menú desde menu.php -->
      <div class="column is-one-quarter">
        <?php include('menu.php'); ?>
      </div>

      <!-- Contenido principal -->
      <div class="column">
        <div class="content">
          <main id="content">
            <h1>Bienvenido a mi aplicación</h1>
            <a href="#" class="button is-primary">Enviar</a>
            <a href="#" class="button is-secondary">Cancelar</a>
          </main>
        </div>
      </div>
    </div>
  </div>

  <!-- Archivo JavaScript para manejar las peticiones AJAX -->
  <script src="/php5/public/js/app.js"></script>
</body>
</html>
