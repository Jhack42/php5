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

Route::get('/omesa', function () {
    return Inertia::render('Omesa'); // Renderiza un componente vacío de Inertia llamado "Blank"
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


//-- ---------------------------------omesa----------------------------------------------------

/*use App\Http\Controllers\Omesa\CategoriaController;

Route::get('SmtrCategoria', [CategoriaController::class, 'index']);
Route::post('SmtrCategoria', [CategoriaController::class, 'store']);
Route::put('SmtrCategoria/{id}', [CategoriaController::class, 'update']);
Route::delete('SmtrCategoria/{id}', [CategoriaController::class, 'destroy']);
*/
/*
use App\Http\Controllers\Omesa\SmtrCategoriaController;

// Agrupa todas las rutas bajo el prefijo 'omesa'
Route::prefix('omesa')->group(function () {

    // Ruta para obtener todas las categorías (GET)
    // Esta ruta llama al método 'index' del SmtrCategoriaController, que generalmente se usa para mostrar una lista de todas las categorías
    Route::get('categorias', [SmtrCategoriaController::class, 'index']);

    // Ruta para crear una nueva categoría (POST)
    // Esta ruta llama al método 'store' del SmtrCategoriaController, que generalmente se usa para almacenar una nueva categoría
    Route::post('categorias', [SmtrCategoriaController::class, 'store']);

    // Ruta para obtener una categoría específica por ID (GET)
    // Esta ruta llama al método 'show' del SmtrCategoriaController, que generalmente se usa para mostrar una categoría específica según su ID
    Route::get('categorias/{id}', [SmtrCategoriaController::class, 'show']);

    // Ruta para actualizar una categoría específica por ID (PUT/PATCH)
    // Esta ruta llama al método 'update' del SmtrCategoriaController, que generalmente se usa para actualizar los datos de una categoría específica
    Route::put('categorias/{id}', [SmtrCategoriaController::class, 'update']);

    // Ruta para eliminar una categoría específica por ID (DELETE)
    // Esta ruta llama al método 'destroy' del SmtrCategoriaController, que generalmente se usa para eliminar una categoría específica
    Route::delete('categorias/{id}', [SmtrCategoriaController::class, 'destroy']);
});

*/

/*
use App\Http\Controllers\Omesa\CategoriaController;

Route::prefix('omesa')->group(function() {
    Route::get('categorias', [CategoriaController::class, 'index']);
    Route::get('categorias/{id}', [CategoriaController::class, 'show']);
    Route::post('categorias', [CategoriaController::class, 'store']);
    Route::put('categorias/{id}', [CategoriaController::class, 'update']);
    Route::delete('categorias/{id}', [CategoriaController::class, 'destroy']);
});
*/
use App\Http\Controllers\Omesa\SmtrCategoriaController;

Route::get('/api/SmtrCategoria', [SmtrCategoriaController::class, 'index']);
Route::post('/api/SmtrCategoria', [SmtrCategoriaController::class, 'store']);
Route::put('/api/SmtrCategoria/{id}', [SmtrCategoriaController::class, 'update']);
Route::delete('/api/SmtrCategoria/{id}', [SmtrCategoriaController::class, 'destroy']);



