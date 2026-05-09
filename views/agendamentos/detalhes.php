<?php
/** @var array $agendamento    */
/** @var bool  $temAtendimento */
$agendamento    = $agendamento    ?? [];
$temAtendimento = $temAtendimento ?? false;

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="form-card" style="max-width:100%">

    <h3>📅 Detalhes do Agendamento</h3>

    <!-- Dados principais -->
    <div style="display:grid;
                grid-template-columns: repeat(3,1fr);
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
                <?= htmlspecialchars($agendamento['cliente_nome']) ?>
            </div>
            <div style="font-size:13px;
                        color:var(--cor-texto-claro)">
                <?= htmlspecialchars(
                    $agendamento['cliente_telefone']) ?>
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
                    $agendamento['profissional_nome']) ?>
            </div>
        </div>

        <div>
            <div style="font-size:12px;
                        color:var(--cor-texto-claro);
                        text-transform:uppercase;
                        letter-spacing:.5px">
                Data e Hora
            </div>
            <div style="font-size:16px; font-weight:600;
                        margin-top:4px">
                <?= date('d/m/Y',
                    strtotime($agendamento['data_hora'])) ?>
            </div>
            <div style="font-size:13px;
                        color:var(--cor-texto-claro)">
                <?= date('H:i',
                    strtotime($agendamento['data_hora'])) ?>
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
                <?= htmlspecialchars($agendamento['servico']) ?>
            </div>
        </div>

        <div>
            <div style="font-size:12px;
                        color:var(--cor-texto-claro);
                        text-transform:uppercase;
                        letter-spacing:.5px">
                Status
            </div>
            <div style="margin-top:4px">
                <span class="badge badge-<?= 
                    strtolower($agendamento['status']) ?>">
                    <?= $agendamento['status'] ?>
                </span>
            </div>
        </div>
    </div>

    <?php if (!empty($agendamento['observacoes'])): ?>
    <div style="padding:16px; background:var(--cor-fundo);
                border-radius:var(--raio-borda);
                margin-bottom:20px">
        <strong>Observações:</strong><br>
        <?= nl2br(htmlspecialchars(
            $agendamento['observacoes'])) ?>
    </div>
    <?php endif; ?>

    <!-- Ações -->
    <div class="form-acoes">

        <?php if ($agendamento['status'] === 'AGENDADO'): ?>

            <!-- Editar -->
            <a href="<?= BASE_URL ?>
                /index.php?controller=agendamento
                &action=form&id=<?= $agendamento['id'] ?>"
               class="btn btn-aviso" style="width:auto">
                ✏️ Editar
            </a>

            <?php if (!$temAtendimento): ?>
            <!-- Registrar Atendimento -->
            <a href="<?= BASE_URL ?>
                /index.php?controller=atendimento
                &action=form
                &agendamento_id=<?= $agendamento['id'] ?>"
               class="btn btn-sucesso" style="width:auto">
                💆 Registrar Atendimento
            </a>
            <?php endif; ?>

            <!-- Cancelar -->
            <a href="<?= BASE_URL ?>
                /index.php?controller=agendamento
                &action=atualizarStatus
                &id=<?= $agendamento['id'] ?>
                &status=CANCELADO"
               class="btn btn-perigo btn-excluir"
               style="width:auto">
                ❌ Cancelar Agendamento
            </a>

        <?php endif; ?>

        <a href="<?= BASE_URL ?>
            /index.php?controller=agendamento"
           class="btn btn-secundario" style="width:auto">
            ← Voltar
        </a>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>