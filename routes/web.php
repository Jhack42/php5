<?php

require_once __DIR__ . '/../app/Core/Router.php';

$router = new Router();

// Definir tus rutas aquí
$router->add('/', new HomeController(), 'index');
$router->add('/about', new AboutController(), 'index');
$router->add('/contact', new ContactController(), 'index');

return $router;
