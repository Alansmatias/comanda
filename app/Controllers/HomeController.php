<?php

namespace Controllers;

class HomeController
{
    public function index()
    {
        // Aqui você pode passar dados se quiser
        $titulo = "Página Inicial";

        // Inclui a view
        require_once __DIR__ . '/../Views/home.php';
    }
}
