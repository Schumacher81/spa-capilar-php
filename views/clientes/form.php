<?php
/** @var array $cliente */
/** @var array $erros   */
$cliente = $cliente ?? [];
$erros   = $erros   ?? [];

// Atalho para preencher campos no modo edição
$v = function(string $campo) use ($cliente): string {
    return htmlspecialchars($cliente[$campo] ?? '');
};

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="form-card">

    <h3>
        <?= !empty($cliente['id'])
            ? '✏️ Editar Cliente' : '👤 Novo Cliente' ?>
    </h3>

    <!-- Exibe erros de validação -->
    <?php if (!empty($erros)): ?>
    <div class="alerta alerta-erro">
        <div>
            <strong>❌ Corrija os erros abaixo:</strong>
            <ul style="margin-top: 6px; padding-left: 18px">
                <?php foreach ($erros as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <form method="POST"
          action="<?= BASE_URL ?>
            /index.php?controller=cliente&action=salvar">

        <!-- ID oculto para edição -->
        <input type="hidden" name="id"
               value="<?= $v('id') ?>">

        <!-- Nome e Telefone -->
        <div class="form-linha">
            <div class="form-grupo">
                <label for="nome">Nome Completo *</label>
                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?= $v('nome') ?>"
                    placeholder="Ex: Maria Silva"
                    required
                >
            </div>
            <div class="form-grupo">
                <label for="telefone">Telefone/WhatsApp *</label>
                <input
                    type="text"
                    id="telefone"
                    name="telefone"
                    value="<?= $v('telefone') ?>"
                    placeholder="(51) 99999-9999"
                    required
                >
            </div>
        </div>

        <!-- Email e Data de Nascimento -->
        <div class="form-linha">
            <div class="form-grupo">
                <label for="email">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= $v('email') ?>"
                    placeholder="maria@email.com"
                >
            </div>
            <div class="form-grupo">
                <label for="data_nascimento">
                    Data de Nascimento
                </label>
                <input
                    type="date"
                    id="data_nascimento"
                    name="data_nascimento"
                    value="<?= $v('data_nascimento') ?>"
                >
            </div>
        </div>

        <!-- Observações -->
        <div class="form-grupo">
            <label for="observacoes">Observações</label>
            <textarea
                id="observacoes"
                name="observacoes"
                placeholder="Alergias, preferências, histórico capilar..."
                rows="4"
            ><?= $v('observacoes') ?></textarea>
        </div>

        <!-- Botões -->
        <div class="form-acoes">
            <button type="submit" class="btn btn-primario"
                    style="width:auto">
                💾 Salvar Cliente
            </button>
            <a href="<?= BASE_URL ?>
                /index.php?controller=cliente"
               class="btn btn-secundario">
                ← Cancelar
            </a>
        </div>

    </form>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>