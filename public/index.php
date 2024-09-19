<?php
session_start();

// Reemplaza localhost con la dirección IP de tu servidor en la red local
$baseUrl = "//" . $_SERVER['HTTP_HOST'] . "/proyecto_gestion";

// Cargar las rutas desde la carpeta 'routes'
require_once __DIR__ . '/../routes/routes.php'; // Ajusta la ruta al archivo routes.php

// Despachar la ruta actual
handleRoute($routes);
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Aplicación</title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/public/css/styles.css">
</head>
<body>
    <div class="container">
        <!-- Barra de navegación en la izquierda -->
        <nav class="sidebar">
            <button data-page="home" class="active">Inicio</button>
            <button data-page="about">Acerca de</button>
            <button data-page="contact">Contacto</button>
        </nav>

        <!-- Contenedor donde se cargará el contenido dinámicamente -->
        <main id="content">
            <h1>Bienvenido</h1>
            <p>Selecciona una opción del menú para cargar el contenido.</p>
        </main>
    </div>

    <!-- Archivo JS para manejar la navegación -->
    <script src="<?php echo $baseUrl; ?>/public/js/app.js"></script>
</body>
</html>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Aplicación</title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/public/css/styles.css">
</head>
<body>
    <div class="container">
        <!-- Barra de navegación en la izquierda -->
        <nav class="sidebar">
            <button data-page="home" class="active">Inicio</button>
            <button data-page="about">Acerca de</button>
            <button data-page="contact">Contacto</button>
        </nav>

        <!-- Contenedor donde se cargará el contenido dinámicamente -->
        <main id="content">
            <!-- Aquí se cargará el contenido dinámicamente -->
            <h1>Bienvenido</h1>
            <p>Selecciona una opción del menú para cargar el contenido.</p>
        </main>
    </div>

    <!-- Archivo JS para manejar la navegación -->
    <script src="<?php echo $baseUrl; ?>/public/js/app.js"></script>
</body>
</html>
