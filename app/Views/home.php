<?php
require_once __DIR__ . '/layout/head.php';
require_once __DIR__ . '/layout/menu.php';
?>

<div class="container">
    <h1><?= $titulo ?? 'Bem-vindo!'?></h1>
    <p>Essa é a página inicial do sistema!</p>
</div>

<?php
require_once __DIR__ . '/layout/rodape.php';
?>
