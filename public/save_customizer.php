<?php
// save_customizer.php
session_start();

// Guardar los valores del customizer en la sesión (en PHP 5)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['skin'] = isset($_POST['skin']) ? $_POST['skin'] : 'light';
    $_SESSION['navbar'] = isset($_POST['navbar']) ? $_POST['navbar'] : 'floating';
    $_SESSION['footer'] = isset($_POST['footer']) ? $_POST['footer'] : 'static';
}

// Redirigir de vuelta a la página principal
header('Location: index.php');
exit();
