<?php
// update_cache.php

// Caminho absoluto mais confiável
require_once __DIR__ . '/app/Controllers/SyspdvCache.php';
$config = require __DIR__ . '/config/config.php';

$cache = new SyspdvCache($config['db']);
$cache->atualizarProdutosCache();
$cache->atualizarFuncionariosCache();
echo "Caches atualizados com sucesso!\n";