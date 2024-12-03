<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Las rutas que deben omitir la verificación CSRF.
     *
     * @var array
     */
    protected $except = [
        'api/*', // Omite la verificación CSRF para todas las rutas de la API
    ];
}
