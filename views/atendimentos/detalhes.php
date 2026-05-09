<?php
/** @var array $atendimento    */
/** @var bool  $temDiagnostico */
$atendimento    = $atendimento    ?? [];
$temDiagnostico = $temDiagnostico ?? false;

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="form-card" style="max-width:100%">

    <h3>💆 Detalhes do Atendimento</h3>

    <!-- Dados principais -->
    <div style="display:grid;
                grid-template-columns:repeat(3,1fr);
                gap:20px; margin-bottom:24px">
        <div>
            <div style="font-size:12px;
                        color:var(--cor-texto-claro);
                        text-transform:uppercase;
                        letter-spacing:.5px">
                Cliente
            </div>
            <div style="font-size:16px; font-weight:600;
                        margin-top:4px">
                <?= htmlspecialchars(
                    $atendimento['cliente_nome']) ?>
            </div>
        </div>

        <div>
            <div style="font-size:12px;
                        color:var(--cor-texto-claro);
                        text-transform:uppercase;
                        letter-spacing:.5px">
                Profissional
            </div>
            <div style="font-size:16px; font-weight:600;
                        margin-top:4px">
                <?= htmlspecialchars(
                    $atendimento['profissional_nome']) ?>
            </div>
        </div>

        <div>
            <div style="font-size:12px;
                        color:var(--cor-texto-claro);
                        text-transform:uppercase;
                        letter-spacing:.5px">
                Serviço
            </div>
            <div style="font-size:16px; font-weight:600;
                        margin-top:4px">
                <?= htmlspecialchars($atendimento['servico']) ?>
            </div>
        </div>

        <div>
            <div style="font-size:12px;
                        color:var(--cor-texto-claro);
                        text-transform:uppercase;
                        letter-spacing:.5px">
                Data
            </div>
            <div style="font-size:16px; font-weight:600;
                        margin-top:4px">
                <?= date('d/m/Y H:i', strtotime(
                    $atendimento['realizado_em'])) ?>
            </div>
        </div>

        <div>
            <div style="font-size:12px;
                        color:var(--cor-texto-claro);
                        text-transform:uppercase;
                        letter-spacing:.5px">
                Valor
            </div>
            <div style="font-size:20px; font-weight:700;
                        color:var(--cor-sucesso);
                        margin-top:4px">
                <?= $atendimento['valor']
                    ? 'R$ ' . number_format(
                        $atendimento['valor'], 2, ',', '.')
                    : '—' ?>
            </div>
        </div>
    </div>

    <!-- Produtos utilizados -->
    <?php if (!empty($atendimento['produtos_utilizados'])): ?>
    <div style="padding:16px; background:var(--cor-fundo);
                border-radius:var(--raio-borda);
                margin-bottom:16px">
        <strong>🧴 Produtos Utilizados:</strong><br>
        <span style="font-size:14px; margin-top:6px;
                     display:block">
            <?= nl2br(htmlspecialchars(
                $atendimento['produtos_utilizados'])) ?>
        </span>
    </div>
    <?php endif; ?>

    <!-- Observações -->
    <?php if (!empty($atendimento['observacoes'])): ?>
    <div style="padding:16px; background:var(--cor-fundo);
                border-radius:var(--raio-borda);
                margin-bottom:16px">
        <strong>📝 Observações:</strong><br>
        <span style="font-size:14px; margin-top:6px;
                     display:block">
            <?= nl2br(htmlspecialchars(
                $atendimento['observacoes'])) ?>
        </span>
    </div>
    <?php endif; ?>

    <!-- Ações -->
    <div class="form-acoes">
        <a href="<?= BASE_URL ?>
            /index.php?controller=atendimento
            &action=form&id=<?= $atendimento['id'] ?>"
           class="btn btn-aviso" style="width:auto">
            ✏️ Editar
        </a>

        <?php if (!$temDiagnostico): ?>
        <a href="<?= BASE_URL ?>
            /index.php?controller=diagnostico
            &action=form
            &atendimento_id=<?= $atendimento['id'] ?>"
           class="btn btn-primario" style="width:auto">
            🧪 Registrar Diagnóstico
        </a>
        <?php else: ?>
        <a href="<?= BASE_URL ?>
            /index.php?controller=diagnostico
            &action=porAtendimento
            &id=<?= $atendimento['id'] ?>"
           class="btn btn-secundario" style="width:auto">
            🧪 Ver Diagnóstico
        </a>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>
            /index.php?controller=atendimento"
           class="btn btn-secundario" style="width:auto">
            ← Voltar
        </a>
    </div>

</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>