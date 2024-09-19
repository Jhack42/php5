<?php
$routes = [
    '/' => 'HomeController@index',
    '/about' => 'HomeController@about',
    '/contact' => 'HomeController@contact',
    '/facultades' => 'FacultadesController@index',
    '/facultades/add' => 'FacultadesController@store',
    '/api/facultades' => 'APIController@getFacultadesJson', // Nueva ruta para la API
];

// Función para manejar la ruta actual
function handleRoute($routes) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if (array_key_exists($uri, $routes)) {
        list($controller, $method) = explode('@', $routes[$uri]);

        if ($controller === 'APIController') {
            require_once __DIR__ . '/../public/api/api.php'; // Llamar al archivo API
        } else {
            require_once "app/Controllers/{$controller}.php"; // Llamar a los controladores normales
        }

        $controllerInstance = new $controller();
        call_user_func([$controllerInstance, $method]);
    } else {
        echo "Error 404: Página no encontrada.";
    }
}
