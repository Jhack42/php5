<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías</title>
    <link rel="stylesheet" href="/php5/public/css/team.css">
</head>
<body>

<!-- Barra superior con menú y búsqueda -->
<div class="team-navbar">
    <div class="team-navbar-left">
        <button class="team-menu-btn">CATEGORÍAS</button>
    </div>
    <div class="team-navbar-center">
        <input type="text" class="team-search-bar" placeholder="Buscar">
    </div>
</div>

<!-- Menú de categorías -->
<div class="team-dropdown-menu">
    <ul class="team-category-list">
        <li class="team-category-item">
            <a href="#">Dispositivos Móviles</a>
            <div class="team-sub-menu">
                <ul>
                    <li>Teléfonos Móviles</li>
                    <li>Tablets</li>
                    <li>E-Readers</li>
                </ul>
            </div>
        </li>
        <li class="team-category-item">
            <a href="#">Tecnología Vestible</a>
            <div class="team-sub-menu">
                <ul>
                    <li>Relojes Inteligentes</li>
                    <li>Pulseras de Actividad</li>
                    <li>Relojes Deportivos</li>
                </ul>
            </div>
        </li>
        <!-- Añadir más categorías aquí -->
    </ul>
</div>

<!-- Sección de categorías -->
<div class="team-container">
    <h1>Categorías</h1>
    <div class="team-category-grid">
        <div class="team-category-card">
            <div class="team-category-header">
                <img src="https://i.postimg.cc/pLDr8ktb/faua-logo-principal.png" alt="Facultad de Arquitectura, Urbanismo y Artes">
                <h2>Facultad de Arquitectura, Urbanismo y Artes</h2>
            </div>
            <ul>
                <li><a href="#">Teléfonos Móviles</a></li>
                <li><a href="#">Tablets</a></li>
                <li><a href="#">E-Readers</a></li>
            </ul>
        </div>
        <div class="team-category-card">
            <div class="team-category-header">
                <img src="https://i.postimg.cc/MTV4rNVP/componentes-informaticos.png" alt="Componentes Informáticos">
                <h2>Componentes Informáticos</h2>
            </div>
            <ul>
                <li><a href="#">Tarjetas Gráficas</a></li>
                <li><a href="#">Procesadores</a></li>
                <li><a href="#">Discos Duros SSD</a></li>
            </ul>
        </div>
        <div class="team-category-card">
            <div class="team-category-header">
                <img src="https://i.postimg.cc/kMztkX8z/aplicaciones-software.png" alt="Aplicaciones y Software">
                <h2>Aplicaciones y Software</h2>
            </div>
            <ul>
                <li><a href="#">Aplicaciones De Mensajería</a></li>
                <li><a href="#">Aplicaciones De Salud</a></li>
                <li><a href="#">Frameworks MV</a></li>
            </ul>
        </div>
        <div class="team-category-card">
            <div class="team-category-header">
                <img src="https://i.postimg.cc/7PmcvzQj/hogar-jardin.png" alt="Hogar y Jardín">
                <h2>Hogar y Jardín</h2>
            </div>
            <ul>
                <li><a href="#">Freidoras De Aire</a></li>
                <li><a href="#">Aspiradoras</a></li>
                <li><a href="#">Robots Aspiradores</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- JavaScript para el menú desplegable -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.querySelector('.menu-btn');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    // Menú desplegable
    if (menuBtn && dropdownMenu) {
        menuBtn.addEventListener('click', function () {
            dropdownMenu.classList.toggle('show');
        });
    }

    // Ocultar el menú al hacer clic fuera de él
    window.addEventListener('click', function(event) {
        if (!menuBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove('show');
        }
    });
});
</script>

</body>
</html>
