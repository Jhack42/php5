<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
</head>
<body>

<h1>Página de Inicio</h1>
<p>Bienvenido a la página de inicio. Aquí puedes encontrar información básica sobre nuestra aplicación.</p>

<?php if (!empty($listaFacultades)): ?>
    <h2>Facultades</h2>
    <ul>
        <?php foreach ($listaFacultades as $facultad): ?>
            <li><?php echo htmlspecialchars($facultad['NOMBRE']); ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No hay facultades disponibles.</p>
<?php endif; ?>

</body>
</html>
