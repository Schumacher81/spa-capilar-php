<?php



/**
 * ROTEADOR PRINCIPAL
 * 
 * Ponto de entrada único do sistema.
 * Todas as URLs passam por aqui.
 * 
 * Como funciona:
 * URL: index.php?controller=cliente&action=index
 *   → instancia ClienteController
 *   → chama o método index()
 */

// Carrega as configurações globais
require_once 'config/config.php';

// Inicia a sessão
Session::iniciar();

// ================================================
// LEITURA DOS PARÂMETROS DA URL
// $_GET['controller'] → qual controller usar
// $_GET['action']     → qual método chamar
// Se não informado, usa os valores padrão
// ================================================
$controller = $_GET['controller'] ?? 'auth';
$action     = $_GET['action']     ?? 'login';

// Exceção: controller auth sem action → action login
if ($controller === 'auth' && $action === 'index') {
    $action = 'login';
}

// ================================================
// SEGURANÇA — sanitiza os parâmetros
// Evita que alguém passe "../../../etc/passwd"
// como controller para acessar arquivos do servidor
// ================================================
$controller = preg_replace('/[^a-zA-Z0-9_]/', '', $controller);
$action     = preg_replace('/[^a-zA-Z0-9_]/', '', $action);


// ================================================
// MONTA O NOME DA CLASSE DO CONTROLLER
// "cliente" → "ClienteController"
// "auth"    → "AuthController"
// ================================================
$nomeClasse = ucfirst(strtolower($controller)) . 'Controller';

// ================================================
// CAMINHO DO ARQUIVO DO CONTROLLER
// ================================================
$arquivoController = CONTROLLERS_PATH . '/' . $nomeClasse . '.php';

// ← ADICIONE ESTA LINHA TEMPORARIAMENTE
error_log("Controller: $nomeClasse | Action: $action | Arquivo: $arquivoController");

// ================================================
// VERIFICA SE O CONTROLLER EXISTE
// ================================================
if (!file_exists($arquivoController)) {
    // Controller não encontrado → redireciona para login
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// Carrega o arquivo do controller
require_once $arquivoController;

// ================================================
// VERIFICA SE A CLASSE EXISTE
// ================================================
if (!class_exists($nomeClasse)) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// ================================================
// INSTANCIA O CONTROLLER
// ================================================
$objeto = new $nomeClasse();

// ================================================
// VERIFICA SE O MÉTODO (ACTION) EXISTE
// ================================================
if (!method_exists($objeto, $action)) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// ================================================
// EXECUTA O MÉTODO
// É aqui que o sistema realmente roda!
// ================================================
$objeto->$action();