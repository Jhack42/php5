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

















use App\Http\Controllers\CarouselController;
Route::get('carousel', [CarouselController::class, 'index']); // Obtener todas las categorías

/*
Route::prefix('carousel')->group(function () {
    Route::get('/', [CarouselController::class, 'index']);
    Route::post('/', [CarouselController::class, 'store']);
    Route::get('/{id}', [CarouselController::class, 'show']);
    Route::put('/{id}', [CarouselController::class, 'update']);
    Route::delete('/{id}', [CarouselController::class, 'destroy']);
    Route::post('/reorder', [CarouselController::class, 'reorder']);
});
*/







use App\Http\Controllers\API\CarouselItemController;
/*
Route::prefix('v1')->group(function () {
    Route::apiResource('carousel-items', CarouselItemController::class);
});
*/
/*
Route::prefix('v1')->group(function () {
    Route::get('/', [CarouselItemController::class, 'index']); // Ver todos los items
    Route::post('/nuevo', [CarouselItemController::class, 'testNuevo']); // Crear un item de prueba
    Route::put('/{id}/editar', [CarouselItemController::class, 'editarSubCategoria']); // Editar un item
    Route::delete('/{id}/eliminar', [CarouselItemController::class, 'eliminar']); // Eliminar un item
    Route::post('/crear-varios/{cantidad?}', [CarouselItemController::class, 'crearVariosTest']); // Crear varios items de prueba
});

*/

// Rutas para testing del carousel
Route::prefix('v1/carousel')->group(function () {
    Route::get('/', [CarouselItemController::class, 'index']);
    Route::post('/', [CarouselItemController::class, 'store']);
    Route::put('/{id}', [CarouselItemController::class, 'update']);
    Route::delete('/{id}', [CarouselItemController::class, 'destroy']);
});



use App\Http\Controllers\api\CarouselDesignController;


Route::prefix('carousel-design')->group(function () {
    Route::get('/', [CarouselDesignController::class, 'index']);
    Route::post('/', [CarouselDesignController::class, 'store']);
    Route::put('/{id}', [CarouselDesignController::class, 'update']);
    Route::delete('/{id}', [CarouselDesignController::class, 'destroy']);
});
/*
use App\Http\Controllers\CarouselDesignController;

Route::prefix('carousel-design')->group(function () {
    Route::get('/', [CarouselDesignController::class, 'index']);
    Route::post('/', [CarouselDesignController::class, 'store']);
    Route::get('/{id}', [CarouselDesignController::class, 'show']);
    Route::put('/{id}', [CarouselDesignController::class, 'update']);
    Route::delete('/{id}', [CarouselDesignController::class, 'destroy']);
    Route::post('/upload-image', [CarouselDesignController::class, 'uploadImage']);
    Route::post('/upload-video', [CarouselDesignController::class, 'uploadVideo']);
    Route::get('/active', [CarouselDesignController::class, 'getActiveDesign']);
});
*/









//----------------Inicio--------------------\Controllers\Multimedia-----------------------------------------
// Importar los controladores
use App\Http\Controllers\Multimedia\ControllerCategoriaImagen;
use App\Http\Controllers\Multimedia\ControllerCategoriaVideo;
use App\Http\Controllers\Multimedia\ControllerImagen;
use App\Http\Controllers\Multimedia\ControllerVideo;

// Rutas para Categorías de Imágenes
Route::prefix('categoria-imagen')->group(function () {
    Route::get('/', [ControllerCategoriaImagen::class, 'index']);
    Route::post('/', [ControllerCategoriaImagen::class, 'store']);
    Route::put('/{id}', [ControllerCategoriaImagen::class, 'update']);
    Route::delete('/{id}', [ControllerCategoriaImagen::class, 'destroy']);
});

// Rutas para Categorías de Videos
Route::prefix('categoria-video')->group(function () {
    Route::get('/', [ControllerCategoriaVideo::class, 'index']);
    Route::post('/', [ControllerCategoriaVideo::class, 'store']);
    Route::put('/{id}', [ControllerCategoriaVideo::class, 'update']);
    Route::delete('/{id}', [ControllerCategoriaVideo::class, 'destroy']);
});

// Rutas para Imágenes
Route::prefix('imagen')->group(function () {
    Route::get('/', [ControllerImagen::class, 'index']);
    Route::post('/', [ControllerImagen::class, 'store']);
    Route::put('/{id}', [ControllerImagen::class, 'update']);
    Route::delete('/{id}', [ControllerImagen::class, 'destroy']);
    Route::get('/{id}', [ControllerImagen::class, 'show']);
});

// Rutas para Videos
Route::prefix('video')->group(function () {
    Route::get('/', [ControllerVideo::class, 'index']);
    Route::post('/', [ControllerVideo::class, 'store']);
    Route::put('/{id}', [ControllerVideo::class, 'update']);
    Route::delete('/{id}', [ControllerVideo::class, 'destroy']);
    Route::get('/{id}', [ControllerVideo::class, 'show']);
});
//----------------Fin--------------------\Controllers\Multimedia-----------------------------------------






























/*
  ████████╗███████╗███████╗████████╗
  ╚══██╔══╝██╔════╝██╔════╝╚══██╔══╝
     ██║   █████╗  █████╗     ██║
     ██║   ██╔══╝  ██╔══╝     ██║
     ██║   ███████╗██║        ██║
     ╚═╝   ╚══════╝╚═╝        ╚═╝

           T  E  S  T
*/




Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando']);
});
Route::get('/carousel/test', [CarouselController::class, 'test']);


use App\Http\Controllers\ProductoTestController;

Route::get('test/nuevo', [ProductoTestController::class, 'testNuevo']);
Route::put('test/editar/{id}', [ProductoTestController::class, 'testEditar']);
Route::put('test/eliminar/{id}', [ProductoTestController::class, 'testEliminar']);
Route::get('test/buscar/{nombre}', [ProductoTestController::class, 'testBuscar']);

use App\Http\Controllers\api\CarouselDesignTestController;

Route::prefix('carousel-design-test')->group(function () {
    Route::get('/', [CarouselDesignTestController::class, 'index']);
    Route::post('/nuevo', [CarouselDesignTestController::class, 'testNuevo']);
    Route::put('/editar/{id}', [CarouselDesignTestController::class, 'editarDiseño']);
    Route::delete('/eliminar/{id}', [CarouselDesignTestController::class, 'eliminar']);
    Route::post('/crear-varios/{cantidad?}', [CarouselDesignTestController::class, 'crearVariosTest']);
    Route::get('/test-css/{id}', [CarouselDesignTestController::class, 'testGenerarCSS']);
});
