<?php
require_once __DIR__ . '/../config/database.php';

function handleRoutes() {
    // Obtener la ruta solicitada
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Normalizar la ruta
    $uri = rtrim($uri, '/'); // Eliminar barra final si existe
    $uri = $uri === '' || $uri === '/public/index.php' ? '/' : $uri; // Si es vacío o es '/public/index.php', considerarlo como '/'

    // Definir rutas
    $routes = [
        '/' => __DIR__ . 'C:\xampp\htdocs\php5\app\Views\casa.php', // Página por defecto
        '/about' => __DIR__ . '/../app/Views/about.php',
        '/contact' => __DIR__ . '/../app/Views/contact.php',
    ];

    // Verificar si la ruta existe
    if (array_key_exists($uri, $routes)) {
        $filePath = $routes[$uri];
        if (file_exists($filePath)) {
            require_once $filePath;
        } else {
            echo "Error: El archivo solicitado no existe.";
        }
    } else {
        require_once __DIR__ . '/../app/Views/error/404.php'; // Página 404 si la ruta no existe
    }
}
