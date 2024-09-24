<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Mi Sitio'; ?></title>
    <!-- Cargar estilos -->
    <link rel="stylesheet" href="/php5/public/css/styles.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="?view=home">Inicio</a></li>
                <li><a href="?view=equipo">Equipo</a></li>
            </ul>
        </nav>
    </header>
