<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\FacultadesController;
use App\Controllers\APIController;

$routes = [
    '/' => 'HomeController@index',
    '/about' => 'HomeController@about',
    '/contact' => 'HomeController@contact',
    '/facultades' => 'FacultadesController@index',
    '/facultades/add' => 'FacultadesController@store',
    '/api/facultades' => 'APIController@getFacultadesJson',
];

function handleRoute($routes) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if (array_key_exists($uri, $routes)) {
        list($controller, $method) = explode('@', $routes[$uri]);

        $controller = "App\\Controllers\\$controller";
        $controllerInstance = new $controller();
        call_user_func([$controllerInstance, $method]);
    } else {
        echo "Error 404: Página no encontrada.";
    }
}

handleRoute($routes);