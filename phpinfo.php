<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Prueba de conexión a Oracle</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .mensaje {
            padding: 10px;
            margin: 15px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .exito {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>
    <?php
    // Output buffering para capturar errores sin romper el diseño HTML
    ob_start();

    try {
        // Datos de conexión
        $usuario = "php5";
        $contrasena = "tu_contraseña"; // Reemplaza con tu contraseña real
        $conexion_string = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=XEPDB1)))";

        // Intentar establecer la conexión con manejo de errores
        $conexion = oci_connect($usuario, $contrasena, $conexion_string);

        if (!$conexion) {
            $e = oci_error();
            throw new Exception("No se pudo establecer la conexión a Oracle: " . htmlentities($e['message'], ENT_QUOTES, 'UTF-8'));
        }

        echo "<div class='mensaje exito'>¡Conexión exitosa a Oracle!</div>";

        // Realizar una consulta de prueba
        $stid = oci_parse($conexion, 'SELECT * FROM v$version');
        if (!$stid) {
            $e = oci_error($conexion);
            throw new Exception("Error al preparar la consulta: " . htmlentities($e['message'], ENT_QUOTES, 'UTF-8'));
        }

        // Ejecutar la consulta
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            throw new Exception("Error al ejecutar la consulta: " . htmlentities($e['message'], ENT_QUOTES, 'UTF-8'));
        }

        // Mostrar resultados
        while (($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
            echo "<p>Versión de Oracle: " . htmlentities($row['BANNER'], ENT_QUOTES, 'UTF-8') . "</p>";
        }

        // Liberar el statement
        oci_free_statement($stid);

        // Cerrar la conexión
        oci_close($conexion);

    } catch (Exception $e) {
        // Mostrar error personalizado
        echo "<div class='mensaje error'>Error: " . $e->getMessage() . "</div>";
    }

    // Output buffering termina y muestra contenido
    ob_end_flush();
    ?>
</body>
</html>
