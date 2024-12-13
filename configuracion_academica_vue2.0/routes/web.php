<?php

use Illuminate\Support\Facades\Route;

Route::get('{any}', function () {
    return view('welcome');
})->where('any', '.*');

use Illuminate\Support\Facades\DB;

Route::get('/test-db', function () {
    try {
        $facultades = DB::table('FACULTAD')->get();
        return response()->json($facultades);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});



Route::get('/facultades', function () {
    // Realizar la consulta directamente a la tabla FACULTAD en Oracle
    $facultades = DB::select('SELECT * FROM PHP5.CAROUSEL_ITEMS');

    // Devolver los resultados (puedes convertirlos a un formato adecuado si lo necesitas)
    return response()->json($facultades);
});

Route::get('/actividad', function () {
    // Realizar la consulta utilizando el Query Builder de Laravel
    $actividad = DB::table('PHP5.CAROUSEL_ITEMS')->get();

    // Devolver los resultados como JSON
    return response()->json($actividad);
});
Route::get('/carrusel', function () {
    // Realizar la consulta utilizando el Query Builder de Laravel
    $carrusel = DB::table('PHP5.CAROUSEL_ITEMS')->get();

    // Devolver los resultados como JSON
    return response()->json($carrusel);
});
