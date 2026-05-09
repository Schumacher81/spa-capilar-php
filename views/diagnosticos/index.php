<?php
/** @var array $diagnosticos */
$diagnosticos = $diagnosticos ?? [];

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="tabela-container">

    <div class="tabela-header">
        <h3>🧪 Diagnósticos Capilares</h3>
        <a href="<?= BASE_URL ?>
            /index.php?controller=diagnostico&action=form"
           class="btn btn-sucesso btn-sm">
            + Novo Diagnóstico
        </a>
    </div>

    <?php if (empty($diagnosticos)): ?>
    <div style="padding:40px; text-align:center;
                color:var(--cor-texto-claro)">
        Nenhum diagnóstico registrado ainda.
    </div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Serviço</th>
                <th>Tipo de Cabelo</th>
                <th>Porosidade</th>
                <th>Profissional</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($diagnosticos as $d): ?>
            <tr>
                <td style="color:var(--cor-texto-claro);
                           font-size:13px">
                    #<?= $d['id'] ?>
                </td>
                <td>
                    <?= date('d/m/Y',
                        strtotime($d['criado_em'])) ?>
                </td>
                <td>
                    <strong>
                        <?= htmlspecialchars(
                            $d['cliente_nome']) ?>
                    </strong>
                </td>
                <td>
                    <?= htmlspecialchars($d['servico']) ?>
                </td>
                <td>
                    <?= $d['tipo_cabelo']
                        ? htmlspecialchars($d['tipo_cabelo'])
                        : '<span style="color:var(--cor-texto-claro)">—</span>'
                    ?>
                </td>
                <td>
                    <?= $d['porosidade']
                        ? htmlspecialchars($d['porosidade'])
                        : '<span style="color:var(--cor-texto-claro)">—</span>'
                    ?>
                </td>
                <td>
                    <?= htmlspecialchars(
                        $d['profissional_nome']) ?>
                </td>
                <td>
                    <div style="display:flex; gap:5px">
                        <a href="<?= BASE_URL ?>
                            /index.php?controller=diagnostico
                            &action=detalhes
                            &id=<?= $d['id'] ?>"
                           class="btn btn-secundario btn-sm"
                           title="Ver detalhes">👁️</a>
                        <a href="<?= BASE_URL ?>
                            /index.php?controller=diagnostico
                            &action=form
                            &id=<?= $d['id'] ?>"
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
            <?= count($diagnosticos) ?>
        </strong> diagnóstico(s)
    </div>
    <?php endif; ?>

</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
