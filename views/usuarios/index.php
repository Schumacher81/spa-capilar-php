<?php
/** @var array $usuarios */
$usuarios = $usuarios ?? [];
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="tabela-container">
    <div class="tabela-header">
        <h3>⚙️ Usuários do Sistema</h3>
        <a href="<?= BASE_URL ?>
            /index.php?controller=usuario&action=form"
           class="btn btn-sucesso btn-sm">
            + Novo Usuário
        </a>
    </div>

    <?php if (empty($usuarios)): ?>
    <div style="padding:40px; text-align:center;
                color:var(--cor-texto-claro)">
        Nenhum usuário cadastrado.
    </div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Login</th>
                <th>Perfil</th>
                <th>Cadastro</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
            <tr>
                <td style="color:var(--cor-texto-claro);
                           font-size:13px">
                    #<?= $u['id'] ?>
                </td>
                <td><strong>
                    <?= htmlspecialchars($u['nome']) ?>
                </strong></td>
                <td><?= htmlspecialchars($u['login']) ?></td>
                <td>
                    <span class="badge badge-<?= 
                        strtolower($u['perfil']) ?>">
                        <?= $u['perfil'] ?>
                    </span>
                </td>
                <td>
                    <?= date('d/m/Y',
                        strtotime($u['criado_em'])) ?>
                </td>
                <td>
                    <div style="display:flex; gap:6px">
                        <a href="<?= BASE_URL ?>
                            /index.php?controller=usuario
                            &action=form&id=<?= $u['id'] ?>"
                           class="btn btn-aviso btn-sm">
                            ✏️
                        </a>
                        <a href="<?= BASE_URL ?>
                            /index.php?controller=usuario
                            &action=inativar
                            &id=<?= $u['id'] ?>"
                           class="btn btn-perigo btn-sm
                                  btn-excluir">
                            🗑️
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div style="padding:12px 24px;
                border-top:1px solid var(--cor-borda);
                font-size:13px;
                color:var(--cor-texto-claro)">
        Total: <strong><?= count($usuarios) ?></strong>
        usuário(s)
    </div>
    <?php endif; ?>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>