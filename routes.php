<?php

$uri = $_SERVER['REQUEST_URI'];

switch ($uri) {
    case '/':
        require_once __DIR__ . '/app/Controllers/HomeController.php';
        (new \Controllers\HomeController())->index();
        break;
    default:
        echo "Página não encontrada";
}