<?php
// Pega flash message se existir
$flash = Session::getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= SISTEMA_NOME ?></title>
    <link rel="stylesheet"
          href="<?= BASE_URL ?>/public/css/style.css">
    <style>
        /* Estilos extras só para a página de login */
        .login-icone {
            font-size: 48px;
            margin-bottom: 8px;
        }

        .login-rodape {
            text-align: center;
            margin-top: 20px;
            color: var(--cor-texto-claro);
            font-size: 12px;
        }

        .input-senha-wrapper {
            position: relative;
        }

        .btn-ver-senha {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: var(--cor-texto-claro);
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-box">

        <!-- Logo -->
        <div class="login-logo">
            <div class="login-icone">✂️</div>
            <h1><?= SISTEMA_NOME ?></h1>
            <p>Sistema de Gestão Capilar</p>
        </div>

        <!-- Flash message -->
        <?php if ($flash): ?>
        <div class="alerta alerta-<?= $flash['tipo'] ?>">
            <?= $flash['tipo'] === 'sucesso' ? '✅' : '❌' ?>
            <?= htmlspecialchars($flash['mensagem']) ?>
        </div>
        <?php endif; ?>

        <!-- Formulário de login -->
        <!-- action aponta para o roteador com controller e action -->
        <form method="POST"
              action="<?= BASE_URL ?>
                /index.php?controller=auth&action=login">

            <div class="form-grupo">
                <label for="login">👤 Login</label>
                <input
                    type="text"
                    id="login"
                    name="login"
                    placeholder="Digite seu login"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="form-grupo">
                <label for="senha">🔒 Senha</label>
                <div class="input-senha-wrapper">
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite sua senha"
                        autocomplete="current-password"
                        required
                    >
                    <!-- Botão para mostrar/ocultar senha -->
                    <button type="button"
                            class="btn-ver-senha"
                            onclick="alternarSenha()">
                        👁️
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primario">
                🚀 Entrar no Sistema
            </button>

        </form>

        <div class="login-rodape">
            <?= SISTEMA_NOME ?> v<?= SISTEMA_VERSAO ?> •
            Todos os direitos reservados
        </div>

    </div>
</div>

<script>
    // Alterna visibilidade da senha
    function alternarSenha() {
        const campo = document.getElementById('senha');
        campo.type = campo.type === 'password' ? 'text' : 'password';
    }

    // Auto-fecha alertas após 4 segundos
    const alertas = document.querySelectorAll('.alerta');
    alertas.forEach(function(alerta) {
        setTimeout(function() {
            alerta.style.opacity = '0';
            alerta.style.transition = 'opacity 0.5s';
            setTimeout(function() { alerta.remove(); }, 500);
        }, 4000);
    });
</script>

</body>
</html>