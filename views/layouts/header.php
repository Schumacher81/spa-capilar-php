<?php
// Garante que config foi carregado
if (!defined('BASE_URL')) {
    require_once dirname(__DIR__, 2) . '/config/config.php';
}

// Garante que a sessão está iniciada
Session::iniciar();

// Redireciona para login se não estiver logado
if (!Session::estaLogado()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// Dados do usuário logado
$usuarioLogado = Session::getUsuario();

// Flash message
$flash = Session::getFlash();

// Controller atual para destacar item do menu
$controllerAtual = $_GET['controller'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>
        <?= isset($tituloPagina)
            ? htmlspecialchars($tituloPagina) . ' — '
            : '' ?>
        <?= SISTEMA_NOME ?>
    </title>
    <link rel="stylesheet"
          href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>

<div class="layout-wrapper">

    <!-- ==========================================
         MENU LATERAL
    ========================================== -->
    <aside class="sidebar" id="sidebar">

        <!-- Logo -->
        <div class="sidebar-logo">
            <h2>✂️ <?= SISTEMA_NOME ?></h2>
            <span>Sistema de Gestão</span>
        </div>

        <!-- Navegação -->
        <nav class="sidebar-menu">

            <div class="menu-secao">Principal</div>

            <!-- Dashboard -->
            <a href="<?= BASE_URL ?>
                /index.php?controller=dashboard&action=index"
               class="menu-item
               <?= $controllerAtual === 'dashboard'
                   ? 'ativo' : '' ?>">
                <span class="icone">🏡</span>
                <span>Dashboard</span>
            </a>

            <!-- Agenda -->
            <!--<a href="<?= BASE_URL ?>
                /index.php?controller=agenda&action=index"
               class="menu-item
               <?= $controllerAtual === 'agenda'
                   ? 'ativo' : '' ?>">
                <span class="icone">📆</span>
                <span>Agenda</span>
            </a>-->

            <a href="<?= BASE_URL ?>
                /index.php?controller=agenda
                &action=diario&data=<?= date('Y-m-d') ?>"
                class="menu-item
                <?= ($controllerAtual === 'agenda'
                    && ($_GET['action'] ?? '') === 'diario')
                    ? 'ativo' : '' ?>">
                <span class="icone">👥</span>
                <span>Agenda Diária</span>
            </a>

            <div class="menu-secao">Gestão</div>

            <!-- Clientes → lista todos -->
            <a href="<?= BASE_URL ?>
                /index.php?controller=cliente&action=index"
               class="menu-item
               <?= $controllerAtual === 'cliente'
                   ? 'ativo' : '' ?>">
                <span class="icone">👤</span>
                <span>Clientes</span>
            </a>

            <!-- Agendamentos → lista todos -->
            <a href="<?= BASE_URL ?>
                /index.php?controller=agendamento&action=index"
               class="menu-item
               <?= $controllerAtual === 'agendamento'
                   ? 'ativo' : '' ?>">
                <span class="icone">📅</span>
                <span>Agendamentos</span>
            </a>

            <!-- Atendimentos → lista todos -->
            <a href="<?= BASE_URL ?>
                /index.php?controller=atendimento&action=index"
               class="menu-item
               <?= $controllerAtual === 'atendimento'
                   ? 'ativo' : '' ?>">
                <span class="icone">💆</span>
                <span>Atendimentos</span>
            </a>

            <!-- Diagnósticos → lista todos -->
            <a href="<?= BASE_URL ?>
                /index.php?controller=diagnostico&action=index"
               class="menu-item
               <?= $controllerAtual === 'diagnostico'
                   ? 'ativo' : '' ?>">
                <span class="icone">🧪</span>
                <span>Diagnósticos</span>
            </a>

            <!-- Usuários (só ADMIN) -->
            <?php if ($usuarioLogado['perfil'] === 'ADMIN'): ?>
            <div class="menu-secao">Administração</div>

            <a href="<?= BASE_URL ?>
                /index.php?controller=usuario&action=index"
               class="menu-item
               <?= $controllerAtual === 'usuario'
                   ? 'ativo' : '' ?>">
                <span class="icone">⚙️</span>
                <span>Usuários</span>
            </a>
            <?php endif; ?>

        </nav>

        <!-- Rodapé com dados do usuário -->
        <div class="sidebar-rodape">
            <div class="usuario-nome">
                👤 <?= htmlspecialchars(
                    $usuarioLogado['nome']) ?>
            </div>
            <div class="usuario-perfil">
                <?= $usuarioLogado['perfil'] ?>
            </div>
        </div>

    </aside>

    <!-- ==========================================
         CONTEÚDO PRINCIPAL
    ========================================== -->
    <div class="main-content">

        <!-- Barra superior -->
        <header class="topbar">
            <div style="display:flex;
                        align-items:center; gap:12px">

                <!-- Botão menu mobile -->
                <button id="btn-menu"
                        style="background:none; border:none;
                               font-size:22px; cursor:pointer;
                               color:var(--cor-primaria);
                               display:none"
                        title="Menu">
                    ☰
                </button>

                <div class="topbar-titulo">
                    <?= isset($tituloPagina)
                        ? htmlspecialchars($tituloPagina)
                        : 'Dashboard' ?>
                </div>
            </div>

            <div class="topbar-acoes">
                <span style="font-size:13px;
                             color:var(--cor-texto-claro)">
                    📅 <?= date('d/m/Y') ?>
                </span>
                <a href="<?= BASE_URL ?>
                    /index.php?controller=auth&action=logout"
                   class="btn btn-secundario btn-sm"
                   onclick="return confirm(
                       'Deseja sair do sistema?')">
                    🚪 Sair
                </a>
            </div>
        </header>

        <!-- Área principal -->
        <main class="conteudo">

            <!-- Flash message -->
            <?php if ($flash): ?>
            <div class="alerta alerta-<?= $flash['tipo'] ?>">
                <?= $flash['tipo'] === 'sucesso'
                    ? '✅' : '❌' ?>
                <?= htmlspecialchars($flash['mensagem']) ?>
            </div>
            <?php endif; ?>

            <!-- O conteúdo de cada view começa aqui -->