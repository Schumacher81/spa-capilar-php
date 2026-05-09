<?php
/** @var array $usuario */
/** @var array $erros   */
$usuario = $usuario ?? [];
$erros   = $erros   ?? [];

$v = function(string $campo) use ($usuario): string {
    return htmlspecialchars($usuario[$campo] ?? '');
};

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="form-card">
    <h3>
        <?= !empty($usuario['id'])
            ? '✏️ Editar Usuário' : '⚙️ Novo Usuário' ?>
    </h3>

    <?php if (!empty($erros)): ?>
    <div class="alerta alerta-erro">
        <div>
            <strong>❌ Corrija os erros:</strong>
            <ul style="margin-top:6px; padding-left:18px">
                <?php foreach ($erros as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <form method="POST"
          action="<?= BASE_URL ?>
            /index.php?controller=usuario&action=salvar">

        <input type="hidden"
               name="id" value="<?= $v('id') ?>">

        <div class="form-linha">
            <div class="form-grupo">
                <label for="nome">Nome Completo *</label>
                <input type="text" id="nome" name="nome"
                       value="<?= $v('nome') ?>"
                       placeholder="Ex: Ana Paula"
                       required>
            </div>
            <div class="form-grupo">
                <label for="login">Login *</label>
                <input type="text" id="login" name="login"
                       value="<?= $v('login') ?>"
                       placeholder="Ex: ana.paula"
                       required>
            </div>
        </div>

        <div class="form-linha">
            <div class="form-grupo">
                <label for="senha">
                    Senha
                    <?= !empty($usuario['id'])
                        ? '(deixe em branco para manter)'
                        : '*' ?>
                </label>
                <input type="password"
                       id="senha" name="senha"
                       placeholder="Mínimo 6 caracteres"
                       <?= empty($usuario['id'])
                           ? 'required' : '' ?>>
            </div>
            <div class="form-grupo">
                <label for="perfil">Perfil *</label>
                <select id="perfil" name="perfil" required>
                    <option value="">Selecione...</option>
                    <option value="ADMIN"
                        <?= $v('perfil') === 'ADMIN'
                            ? 'selected' : '' ?>>
                        ADMIN
                    </option>
                    <option value="PROFISSIONAL"
                        <?= $v('perfil') === 'PROFISSIONAL'
                            ? 'selected' : '' ?>>
                        PROFISSIONAL
                    </option>
                </select>
            </div>
        </div>

        <div class="form-acoes">
            <button type="submit"
                    class="btn btn-primario"
                    style="width:auto">
                💾 Salvar Usuário
            </button>
            <a href="<?= BASE_URL ?>
                /index.php?controller=usuario"
               class="btn btn-secundario">
                ← Cancelar
            </a>
        </div>
    </form>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>