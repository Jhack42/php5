<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Prueba de conexión a MySQL</title>
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
        $usuario = "root"; // Reemplaza con tu usuario de MySQL
        $contrasena = "";  // Reemplaza con tu contraseña real
        $base_datos = "php5"; // Reemplaza con el nombre de tu base de datos
        $host = "127.0.0.1"; // Localhost (o la IP de tu servidor)
        $puerto = 3306; // Puerto predeterminado de MySQL

        // Intentar establecer la conexión con manejo de errores
        $conexion = new mysqli($host, $usuario, $contrasena, $base_datos, $puerto);

        if ($conexion->connect_error) {
            throw new Exception("No se pudo establecer la conexión a MySQL: " . $conexion->connect_error);
        }

        echo "<div class='mensaje exito'>¡Conexión exitosa a MySQL!</div>";

        // Realizar una consulta de prueba
        $resultado = $conexion->query('SELECT VERSION()');

        if (!$resultado) {
            throw new Exception("Error al ejecutar la consulta: " . $conexion->error);
        }

        // Mostrar resultados
        $fila = $resultado->fetch_assoc();
        echo "<p>Versión de MySQL: " . htmlentities($fila['VERSION()'], ENT_QUOTES, 'UTF-8') . "</p>";

        // Liberar el resultado
        $resultado->free();

        // Cerrar la conexión
        $conexion->close();

    } catch (Exception $e) {
        // Mostrar error personalizado
        echo "<div class='mensaje error'>Error: " . $e->getMessage() . "</div>";
    }

    // Output buffering termina y muestra contenido
    ob_end_flush();
    ?>
</body>
</html>
