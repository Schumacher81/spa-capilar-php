<?php
/** @var array  $clientes */
/** @var string $busca    */
$clientes = $clientes ?? [];
$busca    = $busca    ?? '';

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="tabela-container">

    <!-- Cabeçalho com busca e botão novo -->
    <div class="tabela-header">
        <h3>👤 Clientes Cadastrados</h3>
        <a href="<?= BASE_URL ?>
            /index.php?controller=cliente&action=form"
           class="btn btn-sucesso btn-sm">
            + Novo Cliente
        </a>
    </div>

    <!-- Barra de busca -->
    <div style="padding: 16px 24px;
                border-bottom: 1px solid var(--cor-borda)">
        <form method="GET"
              action="<?= BASE_URL ?>/index.php"
              style="display:flex; gap: 10px">
            <input type="hidden" name="controller" value="cliente">
            <input
                type="text"
                name="busca"
                value="<?= htmlspecialchars($busca) ?>"
                placeholder="🔍 Buscar por nome, telefone ou e-mail..."
                style="flex:1; padding: 8px 14px;
                       border: 1px solid var(--cor-borda);
                       border-radius: var(--raio-borda);
                       font-size: 14px"
            >
            <button type="submit" class="btn btn-primario btn-sm"
                    style="width:auto">
                Buscar
            </button>
            <?php if (!empty($busca)): ?>
            <a href="<?= BASE_URL ?>/index.php?controller=cliente"
               class="btn btn-secundario btn-sm">
                Limpar
            </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Tabela de clientes -->
    <?php if (empty($clientes)): ?>
    <div style="padding: 40px; text-align: center;
                color: var(--cor-texto-claro)">
        <?php if (!empty($busca)): ?>
            Nenhum cliente encontrado para
            "<strong><?= htmlspecialchars($busca) ?></strong>".
        <?php else: ?>
            Nenhum cliente cadastrado ainda.
            <br><br>
            <a href="<?= BASE_URL ?>
                /index.php?controller=cliente&action=form"
               class="btn btn-primario" style="width:auto">
                + Cadastrar primeiro cliente
            </a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Telefone</th>
                <th>E-mail</th>
                <th>Cadastro</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td style="color: var(--cor-texto-claro);
                           font-size: 13px">
                    #<?= $cliente['id'] ?>
                </td>
                <td>
                    <strong>
                        <?= htmlspecialchars($cliente['nome']) ?>
                    </strong>
                </td>
                <td><?= htmlspecialchars($cliente['telefone']) ?></td>
                <td>
                    <?= $cliente['email']
                        ? htmlspecialchars($cliente['email'])
                        : '<span style="color:var(--cor-texto-claro)">—</span>'
                    ?>
                </td>
                <td>
                    <?= date('d/m/Y',
                        strtotime($cliente['criado_em'])) ?>
                </td>
                <td>
                    <div style="display:flex; gap:6px">
                        <!-- Detalhes -->
                        <a href="<?= BASE_URL ?>
                            /index.php?controller=cliente
                            &action=detalhes
                            &id=<?= $cliente['id'] ?>"
                           class="btn btn-secundario btn-sm"
                           title="Ver detalhes">
                            👁️
                        </a>
                        <!-- Editar -->
                        <a href="<?= BASE_URL ?>
                            /index.php?controller=cliente
                            &action=form
                            &id=<?= $cliente['id'] ?>"
                           class="btn btn-aviso btn-sm"
                           title="Editar">
                            ✏️
                        </a>
                        <!-- Inativar (só ADMIN) -->
                        <?php if (Session::temPerfil('ADMIN')): ?>
                        <a href="<?= BASE_URL ?>
                            /index.php?controller=cliente
                            &action=inativar
                            &id=<?= $cliente['id'] ?>"
                           class="btn btn-perigo btn-sm btn-excluir"
                           title="Inativar">
                            🗑️
                        </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Rodapé com total -->
    <div style="padding: 12px 24px;
                border-top: 1px solid var(--cor-borda);
                font-size: 13px;
                color: var(--cor-texto-claro)">
        Total: <strong><?= count($clientes) ?></strong> cliente(s)
    </div>
    <?php endif; ?>

</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>