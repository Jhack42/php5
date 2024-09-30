<?php
// Función para obtener los datos de la API
function obtenerDatosFacultades() {
    $url = 'http://localhost:3002/api/vista-facultades-eventos';
    
    $opciones = array(
        'http' => array(
            'method' => 'GET',
            'header' => 'Content-type: application/json'
        )
    );

    $contexto = stream_context_create($opciones);
    $respuesta = file_get_contents($url, false, $contexto);

    if ($respuesta === false) {
        // Manejar error si no se puede obtener la respuesta
        return array();
    }

    return json_decode($respuesta, true);
}

// Obtener los datos
$datosFacultades = obtenerDatosFacultades();
?>

<!-- Menú con Bulma CSS -->
<aside class="menu">
    <p class="menu-label">General</p>
    <ul class="menu-list">
        <li><a href="#">Inicio</a></li>
    </ul>

    <p class="menu-label">Acerca de</p>
    <ul class="menu-list">
        <li>
            <a class="is-active" href="#">Nuestro Equipo</a>
            <ul>
                <li><a href="#">Equipo</a></li>
                <li><a href="#">Historia</a></li>
            </ul>
        </li>
    </ul>

    <p class="menu-label">Servicios</p>
    <ul class="menu-list">
        <li><a href="#">Consultoría</a></li>
        <li><a href="#">Soporte</a></li>
    </ul>

    <p class="menu-label">Facultades</p>
    <ul class="menu-list">
        <?php if (!empty($datosFacultades)): ?>
            <?php foreach ($datosFacultades as $facultad): ?>
                <li><a href="#facultad-<?php echo htmlspecialchars($facultad['facultad_id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($facultad['nombre_facultad'], ENT_QUOTES, 'UTF-8'); ?>
                </a></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li><a>No hay facultades disponibles</a></li>
        <?php endif; ?>
    </ul>
</aside>
