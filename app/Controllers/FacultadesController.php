<?php
require_once __DIR__ . '/../Models/Facultades.php';

class FacultadesController {

    public function index() {
        $facultades = new Facultades();
        $listaFacultades = $facultades->getAllFacultades();
        
        // Pasar los datos a la vista
        require_once __DIR__ . '/../Views/facultades.php';
    }

    public function store() {
        $facultades = new Facultades();
        
        // Obtener el nombre de la facultad desde un formulario POST
        $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null; // Reemplazamos ?? con isset()

        if ($nombre) {
            $facultades->addFacultad($nombre);
            header("Location: /facultades"); // Redirigir de vuelta a la lista de facultades
        } else {
            echo "Error: No se proporcionó el nombre de la facultad.";
        }
    }
}
