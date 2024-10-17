<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Conexión a Oracle</title>
</head>
<body>
    <h1>Prueba de Conexión a Base de Datos Oracle (PHP 5.6.3)</h1>

    <?php
    // Asegúrate de tener la extensión OCI8 instalada para PHP 5.6.3
    // Configuración de conexión a Oracle
    $db_username = "php5";
    $db_password = "php5";
    $db_connection_string = "(DESCRIPTION =
        (ADDRESS_LIST =
          (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
        )
        (CONNECT_DATA =
          (SERVICE_NAME = XEPDB1)
        )
      )";

    // Conectando a la base de datos Oracle
    $conn = oci_connect($db_username, $db_password, $db_connection_string);

    if (!$conn) {
        $e = oci_error();
        echo "<p>Error de conexión a la base de datos Oracle: " . htmlentities($e['message'], ENT_QUOTES) . "</p>";
    } else {
        echo "<p>Conexión exitosa a la base de datos Oracle.</p>";
        
        // Aquí puedes ejecutar consultas SQL si lo deseas

        // Cerrando la conexión
        oci_close($conn);
    }
    ?>
</body>
</html>
