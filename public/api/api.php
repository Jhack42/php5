<?php
require_once __DIR__ . '/../../app/Models/Facultades.php';

// Función para devolver las facultades en formato JSON
function getFacultadesJson() {
    $facultades = new Facultades();
    $listaFacultades = $facultades->getAllFacultades();

    // Devolver los datos en formato JSON
    header('Content-Type: application/json');
    echo json_encode($listaFacultades);
}
