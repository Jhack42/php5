<?php
session_start();

require_once __DIR__ . '/../autoload.php';

$baseUrl = "//" . $_SERVER['HTTP_HOST'] . "/php5";
$router = require_once __DIR__ . '/../routes/web.php';
$router->dispatch();
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
        <nav class="sidebar">
            <button data-page="home" class="active">Inicio</button>
            <button data-page="about">Calendario</button>
            <button data-page="contact">Contacto</button>
        </nav>
        <main id="content">
            <!-- El contenido se cargará dinámicamente aquí -->
        </main>
    </div>
    <script src="<?php echo $baseUrl; ?>/public/js/app.js"></script>
</body>
</html>
