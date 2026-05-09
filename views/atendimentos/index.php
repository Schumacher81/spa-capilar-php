<?php
/** @var array $atendimentos */
$atendimentos = $atendimentos ?? [];

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="tabela-container">

    <div class="tabela-header">
        <h3>💆 Atendimentos Registrados</h3>
        <a href="<?= BASE_URL ?>
            /index.php?controller=atendimento&action=form"
           class="btn btn-sucesso btn-sm">
            + Registrar Atendimento
        </a>
    </div>

    <?php if (empty($atendimentos)): ?>
    <div style="padding:40px; text-align:center;
                color:var(--cor-texto-claro)">
        Nenhum atendimento registrado ainda.
    </div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Serviço</th>
                <th>Profissional</th>
                <th>Valor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($atendimentos as $at): ?>
            <tr>
                <td style="color:var(--cor-texto-claro);
                           font-size:13px">
                    #<?= $at['id'] ?>
                </td>
                <td>
                    <?= date('d/m/Y',
                        strtotime($at['realizado_em'])) ?>
                    <br>
                    <small style="color:var(--cor-texto-claro)">
                        <?= date('H:i',
                            strtotime($at['realizado_em'])) ?>
                    </small>
                </td>
                <td>
                    <strong>
                        <?= htmlspecialchars(
                            $at['cliente_nome']) ?>
                    </strong>
                </td>
                <td>
                    <?= htmlspecialchars($at['servico']) ?>
                </td>
                <td>
                    <?= htmlspecialchars(
                        $at['profissional_nome']) ?>
                </td>
                <td>
                    <?= $at['valor']
                        ? '<strong>R$ ' . number_format(
                            $at['valor'], 2, ',', '.')
                          . '</strong>'
                        : '<span style="color:var(--cor-texto-claro)">—</span>'
                    ?>
                </td>
                <td>
                    <div style="display:flex; gap:5px">
                        <a href="<?= BASE_URL ?>
                            /index.php?controller=atendimento
                            &action=detalhes
                            &id=<?= $at['id'] ?>"
                           class="btn btn-secundario btn-sm"
                           title="Detalhes">👁️</a>
                        <a href="<?= BASE_URL ?>
                            /index.php?controller=atendimento
                            &action=form
                            &id=<?= $at['id'] ?>"
                           class="btn btn-aviso btn-sm"
                           title="Editar">✏️</a>
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
        Total: <strong>
            <?= count($atendimentos) ?>
        </strong> atendimento(s)
    </div>
    <?php endif; ?>

</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>