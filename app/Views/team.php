<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facultades y Eventos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #E8CFA0;
            color: #333;
        }
        .team-navbar {
            background-color: #939598;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .team-menu-btn {
            background-color: #8B0202;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
        .team-search-bar {
            padding: 5px;
            width: 300px;
        }
        .team-dropdown-menu {
            background-color: #939598;
            position: absolute;
            display: none;
            z-index: 1;
        }
        .team-dropdown-menu.show {
            display: block;
        }
        .team-category-list {
            list-style-type: none;
            padding: 0;
        }
        .team-category-item a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
        }
        .team-category-item a:hover {
            background-color: #8B0202;
        }
        .team-container {
            padding: 20px;
        }
        .team-category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .team-category-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .team-category-header {
            background-color: #8B0202;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .team-category-header img {
            max-width: 100%;
            height: auto;
        }
        .team-event-info {
            padding: 15px;
        }
    </style>
</head>
<body>

<div class="team-navbar">
    <button class="team-menu-btn">FACULTADES</button>
    <input type="text" class="team-search-bar" placeholder="Buscar facultad o evento">
</div>

<div class="team-dropdown-menu">
    <ul class="team-category-list" id="facultades-menu"></ul>
</div>

<div class="team-container">
    <h1>Facultades y Eventos</h1>
    <div class="team-category-grid" id="facultades-grid"></div>
</div>

<?php
// Función para obtener los datos de la API
function obtenerDatosFacultades() {
    $url = 'http://localhost:3000/api/vista-facultades-eventos';
    $opciones = array(
        'http' => array(
            'method' => 'GET',
            'header' => 'Content-type: application/json'
        )
    );
    $contexto = stream_context_create($opciones);
    $respuesta = file_get_contents($url, false, $contexto);
    return json_decode($respuesta, true);
}

// Obtener los datos
$datosFacultades = obtenerDatosFacultades();
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.querySelector('.team-menu-btn');
    const dropdownMenu = document.querySelector('.team-dropdown-menu');
    const facultadesMenu = document.getElementById('facultades-menu');
    const facultadesGrid = document.getElementById('facultades-grid');

    // Datos obtenidos de PHP
    const datosFacultades = <?php echo json_encode($datosFacultades); ?>;

    function mostrarFacultades(datos) {
        facultadesMenu.innerHTML = '';
        facultadesGrid.innerHTML = '';

        datos.forEach(facultad => {
            const menuItem = document.createElement('li');
            menuItem.className = 'team-category-item';
            menuItem.innerHTML = `<a href="#facultad-${facultad.facultad_id}">${facultad.nombre_facultad}</a>`;
            facultadesMenu.appendChild(menuItem);

            const card = document.createElement('div');
            card.className = 'team-category-card';
            card.id = `facultad-${facultad.facultad_id}`;
            card.innerHTML = `
                <div class="team-category-header">
                    <img src="${facultad.imagen_url}" alt="${facultad.nombre_facultad}">
                    <h2>${facultad.nombre_facultad}</h2>
                </div>
                <div class="team-event-info">
                    <h3>Próximo Evento</h3>
                    <p><strong>Código:</strong> ${facultad.cod_acti}</p>
                    <p><strong>Descripción:</strong> ${facultad.descripcion}</p>
                    <p><strong>Fecha:</strong> ${new Date(facultad.fecha_inicio).toLocaleDateString()} - ${new Date(facultad.fecha_fin).toLocaleDateString()}</p>
                    <p><strong>Estado:</strong> ${facultad.estado}</p>
                    <p><strong>Periodo:</strong> ${facultad.periodo}</p>
                </div>
            `;
            facultadesGrid.appendChild(card);
        });
    }

    // Mostrar datos obtenidos de la API
    mostrarFacultades(datosFacultades);

    menuBtn.addEventListener('click', function () {
        dropdownMenu.classList.toggle('show');
    });

    window.addEventListener('click', function(event) {
        if (!menuBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove('show');
        }
    });
});
</script>

</body>
</html>
