<?php

// ===================================================
// COPIE ESTE ARQUIVO PARA config/config.php
// e preencha com suas configurações locais
// ===================================================

define('BASE_URL', 'http://localhost/spa-capilar-php');
define('SISTEMA_NOME', 'Spa Capilar');
define('SISTEMA_VERSAO', '1.0.0');

define('ROOT_PATH',        dirname(__DIR__));
define('CONFIG_PATH',      ROOT_PATH . '/config');
define('MODELS_PATH',      ROOT_PATH . '/models');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('VIEWS_PATH',       ROOT_PATH . '/views');

// ===================================================
// BANCO DE DADOS — preencha com seus dados
// ===================================================
// Em Database.php, altere as constantes:
// HOST     = 'localhost'
// DBNAME   = 'spa_capilar'
// USER     = 'seu_usuario'
// PASSWORD = 'sua_senha'

spl_autoload_register(function (string $classe): void {
    $pastas = [
        CONFIG_PATH      . '/',
        MODELS_PATH      . '/',
        CONTROLLERS_PATH . '/',
    ];
    foreach ($pastas as $pasta) {
        $arquivo = $pasta . $classe . '.php';
        if (file_exists($arquivo)) {
            require_once $arquivo;
            return;
        }
    }
});

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');