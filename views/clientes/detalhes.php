<?php
/** @var array $cliente   */
/** @var array $historico */
$cliente   = $cliente   ?? [];
$historico = $historico ?? [];

require_once VIEWS_PATH . '/layouts/header.php';
?>

<!-- Dados do cliente -->
<div class="form-card" style="max-width:100%;
                               margin-bottom: 24px">
    <h3>👤 Dados do Cliente</h3>

    <div style="display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px">

        <div>
            <div style="font-size:12px;
                        color:var(--cor-texto-claro);
                        text-transform:uppercase;
                        letter-spacing:.5px">
                Nome
            </div>
            <div style="font-size:16px; font-weight:600;
                        margin-top:4px">
                <?= htmlspecialchars($cliente['nome']) ?>
            </div>
        </div>

        <div>
            <div style="font-size:12px;
                        color:var(--cor-texto-claro);
                        text-transform:uppercase;
                        letter-spacing:.5px">
                Telefone
            </div>
            <div style="font-size:16px; font-weight:600;
                        margin-top:4px">
                <?= htmlspecialchars($cliente['telefone']) ?>
            </div>
        </div>

        <div>
            <div style="font-size:12px;
                        color:var(--cor-texto-claro);
                        text-transform:uppercase;
                        letter-spacing:.5px">
                E-mail
            </div>
            <div style="font-size:16px; font-weight:600;
                        margin-top:4px">
                <?= $cliente['email']
                    ? htmlspecialchars($cliente['email'])
                    : '—' ?>
            </div>
        </div>

        <div>
            <div style="font-size:12px;
                        color:var(--cor-texto-claro);
                        text-transform:uppercase;
                        letter-spacing:.5px">
                Nascimento
            </div>
            <div style="font-size:16px; font-weight:600;
                        margin-top:4px">
                <?= $cliente['data_nascimento']
                    ? date('d/m/Y',
                        strtotime($cliente['data_nascimento']))
                    : '—' ?>
            </div>
        </div>

        <div>
            <div style="font-size:12px;
                        color:var(--cor-texto-claro);
                        text-transform:uppercase;
                        letter-spacing:.5px">
                Cadastro
            </div>
            <div style="font-size:16px; font-weight:600;
                        margin-top:4px">
                <?= date('d/m/Y',
                    strtotime($cliente['criado_em'])) ?>
            </div>
        </div>

    </div>

    <?php if (!empty($cliente['observacoes'])): ?>
    <div style="margin-top: 20px; padding-top: 16px;
                border-top: 1px solid var(--cor-borda)">
        <div style="font-size:12px;
                    color:var(--cor-texto-claro);
                    text-transform:uppercase;
                    letter-spacing:.5px;
                    margin-bottom:6px">
            Observações
        </div>
        <p style="font-size:14px; line-height:1.6">
            <?= nl2br(htmlspecialchars($cliente['observacoes'])) ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- Botões de ação -->
    <div class="form-acoes">
        <a href="<?= BASE_URL ?>
            /index.php?controller=cliente
            &action=form&id=<?= $cliente['id'] ?>"
           class="btn btn-aviso" style="width:auto">
            ✏️ Editar
        </a>
        <a href="<?= BASE_URL ?>
            /index.php?controller=agendamento
            &action=form&cliente_id=<?= $cliente['id'] ?>"
           class="btn btn-sucesso" style="width:auto">
            📅 Novo Agendamento
        </a>
        <a href="<?= BASE_URL ?>/index.php?controller=cliente"
           class="btn btn-secundario" style="width:auto">
            ← Voltar
        </a>
    </div>
</div>

<!-- Histórico de atendimentos -->
<div class="tabela-container">
    <div class="tabela-header">
        <h3>📋 Histórico de Atendimentos</h3>
    </div>

    <?php if (empty($historico)): ?>
    <div style="padding: 32px; text-align:center;
                color: var(--cor-texto-claro)">
        Nenhum atendimento registrado para este cliente.
    </div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Serviço</th>
                <th>Profissional</th>
                <th>Status</th>
                <th>Valor</th>
                <th>Diagnóstico</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($historico as $h): ?>
            <tr>
                <td>
                    <?= date('d/m/Y H:i',
                        strtotime($h['data_hora'])) ?>
                </td>
                <td>
                    <?= htmlspecialchars($h['servico']) ?>
                </td>
                <td>
                    <?= htmlspecialchars($h['profissional_nome']) ?>
                </td>
                <td>
                    <span class="badge badge-<?= 
                        strtolower($h['status']) ?>">
                        <?= $h['status'] ?>
                    </span>
                </td>
                <td>
                    <?= $h['valor']
                        ? 'R$ ' . number_format(
                            $h['valor'], 2, ',', '.')
                        : '—' ?>
                </td>
                <td>
                    <?php if ($h['diagnostico_id']): ?>
                    <a href="<?= BASE_URL ?>
                        /index.php?controller=diagnostico
                        &action=detalhes
                        &id=<?= $h['diagnostico_id'] ?>"
                       class="btn btn-secundario btn-sm">
                        🧪 Ver
                    </a>
                    <?php else: ?>
                    <span style="color:var(--cor-texto-claro)">
                        —
                    </span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>