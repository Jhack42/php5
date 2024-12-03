<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


use App\Http\Controllers\BakiCategoriaController;

// Rutas para las categorías
Route::get('categorias', [BakiCategoriaController::class, 'index']);        // Listar todas las categorías
Route::get('categorias/{id}', [BakiCategoriaController::class, 'show']);     // Mostrar una categoría específica
Route::post('categorias', [BakiCategoriaController::class, 'store']);       // Crear una nueva categoría
Route::put('categorias/{id}', [BakiCategoriaController::class, 'update']);  // Actualizar una categoría
Route::delete('categorias/{id}', [BakiCategoriaController::class, 'destroy']);  // Eliminar una categoría
