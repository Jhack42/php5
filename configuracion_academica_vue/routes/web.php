<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return Inertia::render('Blank'); // Renderiza un componente vacío de Inertia llamado "Blank"
});

Route::get('/productos', function () {
    return view('app'); // Asegúrate de que 'app' sea la vista correcta para tu frontend Vue
});






// Ruta opcional para probar la conexión con Oracle
Route::get('/test-oracle', function () {
    try {
        DB::connection()->getPdo();
        return 'Conexión con Oracle exitosa.';
    } catch (\Exception $e) {
        return 'Error conectando a Oracle: ' . $e->getMessage();
    }
});
