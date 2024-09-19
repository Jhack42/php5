<?php

// Cargar las variables de entorno manualmente desde el archivo .env
function loadEnv($path)
{
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Ignorar comentarios
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Quitar comillas si están presentes
        $value = str_replace(array('"', "'"), '', $value);

        // Definir la variable de entorno
        putenv("$name=$value");
    }

    return true;
}

// Cargar las variables de entorno
loadEnv(__DIR__ . '/../.env');

// Prueba de conexión
$conn = getConnection();
if ($conn) {
    echo "Conexión exitosa a Oracle";
    oci_close($conn);
} else {
    echo "Error en la conexión a Oracle";
}

// Función para conectarse a la base de datos Oracle usando oci_connect
function getConnection() {
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $service_name = getenv('DB_SERVICE_NAME');
    $username = getenv('DB_USERNAME');
    $password = getenv('DB_PASSWORD');

    // Construir la cadena de conexión TNS
    $tns = "(DESCRIPTION =
        (ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))
        (CONNECT_DATA =
          (SERVER = DEDICATED)
          (SERVICE_NAME = $service_name)
        )
    )";

    // Conectar a Oracle
    $conn = oci_connect($username, $password, $tns);

    if (!$conn) {
        $e = oci_error();
        echo "Error al conectarse a la base de datos Oracle: " . $e['message'];
        exit;
    }

    return $conn;
}
?>
