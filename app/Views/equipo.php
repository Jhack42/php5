<?php
$pageTitle = 'Nuestro Equipo';
?>

<div id="content">
    <h1>Nuestro Equipo</h1>
    <p>Conoce a los integrantes de nuestro equipo.</p>

    <!-- Menú desplegable -->
    <div class="dropdown">
        <button class="dropdown-btn">Opciones del Equipo</button>
        <div class="dropdown-content">
            <a href="#">Integrantes</a>
            <a href="#">Proyectos</a>
            <a href="#">Contactos</a>
        </div>
    </div>
</div>

<!-- Estilos específicos para el menú desplegable -->
<style>
    /* Estilos generales */
    .dropdown {
        position: relative;
        display: inline-block;
        margin-top: 20px;
    }

    .dropdown-btn {
        background-color: #4CAF50;
        color: white;
        padding: 10px;
        font-size: 16px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.1);
        min-width: 160px;
        border-radius: 5px;
        z-index: 1;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    .show {
        display: block;
    }
</style>

<!-- Script para hacer que el menú sea dinámico -->
<script>
    // Seleccionamos el botón del menú y el contenido del menú desplegable
    document.addEventListener('DOMContentLoaded', function() {
        var dropdownBtn = document.querySelector('.dropdown-btn');
        var dropdownContent = document.querySelector('.dropdown-content');

        // Añadir evento al botón para mostrar/ocultar el menú
        dropdownBtn.addEventListener('click', function() {
            dropdownContent.classList.toggle('show');
        });

        // Cerrar el menú si se hace clic fuera de él
        window.addEventListener('click', function(event) {
            if (!event.target.matches('.dropdown-btn')) {
                if (dropdownContent.classList.contains('show')) {
                    dropdownContent.classList.remove('show');
                }
            }
        });
    });
</script>
