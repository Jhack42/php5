<?php
require_once 'database.php';

class Model {
    protected $conn;

    public function __construct() {
        // Obtener la conexión a Oracle
        $this->conn = getConnection();
    }

    // Método para ejecutar consultas SELECT
    public function select($query) {
        $stmt = oci_parse($this->conn, $query);
        oci_execute($stmt);
        
        $rows = [];
        while ($row = oci_fetch_assoc($stmt)) {
            $rows[] = $row;
        }
        oci_free_statement($stmt);
        
        return $rows;
    }

    // Método para ejecutar INSERT, UPDATE, DELETE
    public function execute($query) {
        $stmt = oci_parse($this->conn, $query);
        $result = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
        oci_free_statement($stmt);
        return $result;
    }

    public function __destruct() {
        // Cerrar la conexión a la base de datos
        oci_close($this->conn);
    }
}
