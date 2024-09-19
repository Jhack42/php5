<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\HomeController;

$controller = new HomeController();
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        $controller->home();
        break;
    case 'about':
        $controller->about();
        break;
    case 'contact':
        $controller->contact();
        break;
    default:
        echo "<h1>Página no encontrada</h1>";
}
