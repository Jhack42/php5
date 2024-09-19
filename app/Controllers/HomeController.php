<?php

namespace App\Controllers;

class HomeController {

    // Página principal
    public function index() {
        require_once 'app/Views/home.php';
    }

    // Página "Acerca de"
    public function about() {
        require_once 'app/Views/about.php';
    }

    // Página de contacto
    public function contact() {
        require_once 'app/Views/contact.php';
    }
}
