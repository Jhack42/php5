<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/balck', function () {
    return Inertia::render('Blank'); // Renderiza un componente vacío de Inertia llamado "Blank"
});
