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

use App\Http\Controllers\UsuarioController;

Route::get('user', [UsuarioController::class, 'index']);        // Listar todas las categorías

use App\Http\Controllers\ProductoController;

Route::get('productos', [ProductoController::class, 'index']);
Route::post('productos', [ProductoController::class, 'store']);
Route::put('productos/{id}', [ProductoController::class, 'update']);
Route::delete('productos/{id}', [ProductoController::class, 'destroy']);
Route::get('productos/search', [ProductoController::class, 'search']);

use App\Http\Controllers\ProductoTestController;

Route::get('test/nuevo', [ProductoTestController::class, 'testNuevo']);
Route::put('test/editar/{id}', [ProductoTestController::class, 'testEditar']);
Route::put('test/eliminar/{id}', [ProductoTestController::class, 'testEliminar']);
Route::get('test/buscar/{nombre}', [ProductoTestController::class, 'testBuscar']);

use App\Http\Controllers\CategoriaTestController;

Route::get('testcategoria/nuevo', [CategoriaTestController::class, 'testNuevo']);
Route::put('testcategoria/editar/{id}', [CategoriaTestController::class, 'testEditar']);
Route::put('testcategoria/eliminar/{id}', [CategoriaTestController::class, 'testEliminar']);
Route::get('testcategoria/buscar/{nombre}', [CategoriaTestController::class, 'testBuscar']);

use App\Http\Controllers\SubCategoriaTestController;

Route::get('testsupcategoria/nuevo', [SubCategoriaTestController::class, 'testNuevo']);
Route::put('testsupcategoria/editar/{id}', [SubCategoriaTestController::class, 'testEditar']);
Route::put('testsupcategoria/eliminar/{id}', [SubCategoriaTestController::class, 'testEliminar']);
Route::get('testsupcategoria/buscar/{nombre}', [SubCategoriaTestController::class, 'testBuscar']);

use App\Http\Controllers\CategoriaController;

Route::get('categoria', [CategoriaController::class, 'index']); // Obtener todas las categorías
Route::post('categoria/create', [CategoriaController::class, 'create']);
Route::put('categoria/update/{id}', [CategoriaController::class, 'update']);
Route::delete('categoria/delete/{id}', [CategoriaController::class, 'delete']);

use App\Http\Controllers\SubCategoriaController;

Route::get('supcategoria', [SubCategoriaController::class, 'index']); // Obtener todas las categorías
Route::post('supcategoria/nuevo', [SubCategoriaController::class, 'testNuevo']);
Route::put('supcategoria/{id}/editar', [SubCategoriaController::class, 'editarSubCategoria']);
Route::delete('supcategoria/{id}/eliminar', [SubCategoriaController::class, 'eliminar']);
