<?php

$config = require 'config.php';

try {
    $dsn = sprintf(
        'firebird:dbname=%s:%s;charset=%s',
        $config['db']['host'],
        $config['db']['path'],
        $config['db']['charset']
    );

    $pdo = new PDO($dsn, $config['db']['username'], $config['db']['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexão com Firebird realizada com sucesso! 🚀";

} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
