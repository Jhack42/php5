<?php
// index.php

// Definir una constante de ruta base para evitar problemas con rutas relativas
define('BASE_PATH', __DIR__ . '/../');

// Obtener la vista solicitada desde la URL
$view = isset($_GET['view']) ? $_GET['view'] : 'home';

// Cargar header y footer una sola vez para todas las vistas
require_once BASE_PATH . 'app/Views/header.php';

// Función para cargar la vista solicitada
function loadView($viewName) {
    $viewPath = BASE_PATH . "app/Views/$viewName.php";
    if (file_exists($viewPath)) {
        require_once $viewPath;
    } else {
        require_once BASE_PATH . 'app/Views/404.php';
    }
}

// Gestión de las vistas dinámicas
switch ($view) {
    case 'home':
        loadView('home');
        break;
    case 'equipo':
        loadView('equipo');
        break;
    case 'team':
            loadView('team');
            break;
    default:
        loadView('404');
        break;
}

// Incluir el footer una sola vez
require_once BASE_PATH . 'app/Views/footer.php';
?>
