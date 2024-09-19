<?php
require_once __DIR__ . '/../../config/database.php'; // Asegúrate de que la ruta sea correcta

class Facultades {

    private $conn;

    public function __construct() {
        // Obtener la conexión a la base de datos Oracle
        $this->conn = getConnection();
    }

    // Método para obtener todas las facultades
    public function getAllFacultades() {
        $query = "SELECT * FROM FACULTADES";
        
        // Preparar la consulta
        $stmt = oci_parse($this->conn, $query);
        oci_execute($stmt);

        $facultades = [];
        while ($row = oci_fetch_assoc($stmt)) {
            $facultades[] = $row;
        }
        oci_free_statement($stmt); // Liberar los recursos

        return $facultades;
    }

    // Método para agregar una nueva facultad
    public function addFacultad($nombre) {
        // Consulta para insertar
        $query = "INSERT INTO FACULTADES (FACULTAD_ID, NOMBRE) VALUES (FACULTADES_SEQ.NEXTVAL, :nombre)";

        // Preparar la consulta
        $stmt = oci_parse($this->conn, $query);
        
        // Vincular el parámetro
        oci_bind_by_name($stmt, ":nombre", $nombre);

        // Ejecutar la consulta
        $result = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
        oci_free_statement($stmt); // Liberar los recursos

        return $result;
    }

    // Método para cerrar la conexión
    public function __destruct() {
        oci_close($this->conn); // Cerrar la conexión cuando el objeto sea destruido
    }
}
