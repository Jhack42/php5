<?php
namespace App\Controllers;

use App\Database\Database;

class FacultadesController {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function index() {
        $conexion = $this->db->getConnection();
        $resultado = $conexion->query('SELECT * FROM facultades');

        if (!$resultado) {
            echo "Error al ejecutar la consulta: " . $conexion->error;
            return;
        }

        while ($fila = $resultado->fetch_assoc()) {
            echo "<p>Facultad: " . htmlentities($fila['nombre'], ENT_QUOTES, 'UTF-8') . "</p>";
        }

        $resultado->free();
        $this->db->close();
    }

    public function store() {
        // Implementar lógica para almacenar facultades
    }
}
