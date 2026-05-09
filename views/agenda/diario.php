<?php
/** @var string $data          */
/** @var string $diaAnterior   */
/** @var string $diaSeguinte   */
/** @var array  $agendamentos  */
/** @var array  $profissionais */
/** @var array  $horarios      */
/** @var string $nomeDiaSemana */
/** @var string $hoje          */
$data          = $data          ?? date('Y-m-d');
$diaAnterior   = $diaAnterior   ?? date('Y-m-d');
$diaSeguinte   = $diaSeguinte   ?? date('Y-m-d');
$agendamentos  = $agendamentos  ?? [];
$profissionais = $profissionais ?? [];
$horarios      = $horarios      ?? range(8, 20);
$nomeDiaSemana = $nomeDiaSemana ?? '';
$hoje          = $hoje          ?? date('Y-m-d');

// Conta totais do dia para o resumo
$totalAgendados  = 0;
$totalRealizados = 0;
$totalCancelados = 0;
foreach ($agendamentos as $profId => $horas) {
    foreach ($horas as $hora => $ag) {
        if ($ag['status'] === 'AGENDADO')  $totalAgendados++;
        if ($ag['status'] === 'REALIZADO') $totalRealizados++;
        if ($ag['status'] === 'CANCELADO') $totalCancelados++;
    }
}
$totalDia = $totalAgendados + $totalRealizados + $totalCancelados;

// Define colunas do grid:
// 80px para hora + 1fr para cada profissional
$colunas = '80px ' . implode(' ',
    array_fill(0, count($profissionais), '1fr'));

require_once VIEWS_PATH . '/layouts/header.php';
?>

<!-- Toolbar -->
<div class="agenda-toolbar">

    <!-- Navegação de datas -->
    <div class="agenda-toolbar-centro">
        <a href="<?= BASE_URL ?>
            /index.php?controller=agenda&action=diario
            &data=<?= $diaAnterior ?>"
           class="agenda-nav-btn" title="Dia anterior">
            ‹
        </a>

        <!-- Seletor de data -->
        <form method="GET"
              action="<?= BASE_URL ?>/index.php"
              style="display:flex; align-items:center;
                     gap:8px">
            <input type="hidden"
                   name="controller" value="agenda">
            <input type="hidden"
                   name="action" value="diario">
            <input
                type="date"
                name="data"
                value="<?= $data ?>"
                onchange="this.form.submit()"
                style="border:none; background:transparent;
                       font-size:18px; font-weight:700;
                       color:var(--cor-primaria);
                       cursor:pointer"
            >
        </form>

        <a href="<?= BASE_URL ?>
            /index.php?controller=agenda&action=diario
            &data=<?= $diaSeguinte ?>"
           class="agenda-nav-btn" title="Próximo dia">
            ›
        </a>
    </div>

    <!-- Info do dia -->
    <div style="text-align:center">
        <div style="font-size:14px; font-weight:600;
                    color:var(--cor-primaria)">
            <?= $nomeDiaSemana ?>
        </div>
        <?php if ($data === $hoje): ?>
        <div style="font-size:12px; background:#E8F5E9;
                    color:#2E7D32; padding:2px 8px;
                    border-radius:10px; margin-top:2px">
            ● Hoje
        </div>
        <?php endif; ?>
    </div>

    <!-- Alternar visão -->
    <div style="display:flex; gap:8px; flex-wrap:wrap">
        <a href="<?= BASE_URL ?>
            /index.php?controller=agenda&action=index"
           class="btn btn-secundario btn-sm">
            📅 Mensal
        </a>
        <a href="<?= BASE_URL ?>
            /index.php?controller=agenda&action=semanal"
           class="btn btn-secundario btn-sm">
            📆 Semanal
        </a>
        <span class="btn btn-primario btn-sm"
              style="width:auto; cursor:default">
            👥 Diário
        </span>
        <a href="<?= BASE_URL ?>
            /index.php?controller=agendamento&action=form"
           class="btn btn-sucesso btn-sm">
            + Novo
        </a>
    </div>
</div>

<!-- Resumo do dia -->
<div class="diario-resumo">
    <div class="resumo-item">
        📊 <strong><?= $totalDia ?></strong>
        agendamento(s) no dia
    </div>
    <?php if ($totalAgendados > 0): ?>
    <div class="resumo-item" style="color:#1565C0">
        🕐 <strong><?= $totalAgendados ?></strong> agendado(s)
    </div>
    <?php endif; ?>
    <?php if ($totalRealizados > 0): ?>
    <div class="resumo-item" style="color:#2E7D32">
        ✅ <strong><?= $totalRealizados ?></strong> realizado(s)
    </div>
    <?php endif; ?>
    <?php if ($totalCancelados > 0): ?>
    <div class="resumo-item" style="color:#C62828">
        ❌ <strong><?= $totalCancelados ?></strong> cancelado(s)
    </div>
    <?php endif; ?>
    <?php if ($totalDia === 0): ?>
    <div class="resumo-item"
         style="color:var(--cor-texto-claro)">
        Nenhum agendamento neste dia
    </div>
    <?php endif; ?>
</div>

<?php if (empty($profissionais)): ?>
<!-- Nenhum profissional cadastrado -->
<div class="tabela-container"
     style="padding:40px; text-align:center;
            color:var(--cor-texto-claro)">
    <div style="font-size:48px; margin-bottom:12px">👩</div>
    <p>Nenhum profissional cadastrado no sistema.</p>
    <a href="<?= BASE_URL ?>
        /index.php?controller=usuario&action=form"
       class="btn btn-primario"
       style="width:auto; margin-top:16px">
        + Cadastrar Profissional
    </a>
</div>
<?php else: ?>

<!-- Grade diária -->
<div class="diario-container">
<div class="diario-grade">

    <!-- Cabeçalho com profissionais -->
    <div class="diario-cabecalho"
         style="grid-template-columns: <?= $colunas ?>">

        <!-- Coluna vazia para os horários -->
        <div class="diario-cabecalho-hora">
            🕐 Horário
        </div>

        <!-- Uma coluna por profissional -->
        <?php foreach ($profissionais as $prof):
            // Conta agendamentos deste profissional no dia
            $totalProf = count(
                $agendamentos[$prof['id']] ?? []
            );
        ?>
        <div class="diario-cabecalho-prof">
            <span class="prof-icone">👩</span>
            <span class="prof-nome">
                <?= htmlspecialchars($prof['nome']) ?>
            </span>
            <span class="prof-total">
                <?= $totalProf > 0
                    ? $totalProf . ' agendamento(s)'
                    : 'Livre' ?>
            </span>
        </div>
        <?php endforeach; ?>

    </div>

    <!-- Linhas de horário -->
    <?php foreach ($horarios as $hora): ?>
    <div class="diario-linha"
         style="grid-template-columns: <?= $colunas ?>">

        <!-- Coluna de hora -->
        <div class="diario-hora">
            <?= sprintf('%02d:00', $hora) ?>
            <span>às</span>
            <?= sprintf('%02d:00', $hora + 1) ?>
        </div>

        <!-- Célula de cada profissional -->
        <?php foreach ($profissionais as $prof):
            $ag = $agendamentos[$prof['id']][$hora] ?? null;
        ?>
        <?php if ($ag): ?>
        <!-- HORÁRIO OCUPADO — mostra o agendamento -->
        <div class="diario-celula ocupado"
             onclick="abrirPopup(<?= $ag['id'] ?>)"
             title="<?= htmlspecialchars(
                $ag['cliente_nome'] . ' — ' .
                $ag['servico']) ?>">
            <div class="diario-evento
                 <?= strtolower($ag['status']) ?>">
                <div>
                    <div class="evento-hora-tag">
                        🕐 <?= date('H:i', strtotime(
                            $ag['data_hora'])) ?>
                    </div>
                    <div class="evento-cliente">
                        <?= htmlspecialchars(
                            $ag['cliente_nome']) ?>
                    </div>
                    <div class="evento-servico">
                        ✂️ <?= htmlspecialchars(
                            $ag['servico']) ?>
                    </div>
                </div>
                <span class="badge badge-<?= 
                    strtolower($ag['status']) ?>"
                      style="font-size:10px;
                             align-self:flex-end">
                    <?= $ag['status'] ?>
                </span>
            </div>
        </div>

        <?php else: ?>
        <!-- HORÁRIO LIVRE — clique para agendar -->
        <div class="diario-celula vazio"
             onclick="abrirFormulario(
                 '<?= $data ?>',
                 '<?= sprintf('%02d', $hora) ?>',
                 <?= $prof['id'] ?>)"
             title="Clique para agendar">
        </div>
        <?php endif; ?>

        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

</div>
</div>
<?php endif; ?>

<!-- Popup (reutilizado das outras views) -->
<div class="popup-overlay" id="popup-overlay"
     onclick="fecharPopup()">
    <div class="popup-box"
         onclick="event.stopPropagation()">
        <div class="popup-header">
            <h4 id="popup-titulo">Agendamento</h4>
            <button class="popup-fechar"
                    onclick="fecharPopup()">×</button>
        </div>
        <div class="popup-body" id="popup-body">
            <div class="popup-vazio">Carregando...</div>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

// ================================================
// Abre popup com dados do agendamento
// ================================================
function abrirPopup(id) {
    document.getElementById('popup-overlay')
        .classList.add('aberto');
    document.getElementById('popup-titulo')
        .textContent = '📅 Carregando...';
    document.getElementById('popup-body').innerHTML =
        '<div class="popup-vazio">Carregando...</div>';

    fetch(BASE_URL +
        '/index.php?controller=agendamento' +
        '&action=detalhesJson&id=' + id)
        .then(r => r.json())
        .then(ag => {
            document.getElementById('popup-titulo')
                .textContent =
                    '📅 ' + ag.data + ' às ' + ag.hora;

            const statusClasse = {
                'AGENDADO' : 'badge-agendado',
                'REALIZADO': 'badge-realizado',
                'CANCELADO': 'badge-cancelado'
            };

            document.getElementById('popup-body')
                .innerHTML = `
                <div class="popup-item">
                    <div class="popup-item-hora">
                        🕐 ${ag.hora}
                    </div>
                    <div class="popup-item-cliente">
                        👤 ${ag.cliente_nome}
                    </div>
                    <div style="font-size:13px; color:#777;
                                margin-bottom:4px">
                        📞 ${ag.cliente_telefone}
                    </div>
                    <div class="popup-item-servico">
                        ✂️ ${ag.servico}
                    </div>
                    <div style="margin-bottom:10px;
                                font-size:13px">
                        👩 ${ag.profissional_nome}
                    </div>
                    <span class="badge
                        ${statusClasse[ag.status] || ''}">
                        ${ag.status}
                    </span>
                    ${ag.observacoes ? `
                    <div style="margin-top:10px;
                                font-size:13px; color:#555;
                                padding:8px;
                                background:#f9f5f6;
                                border-radius:6px">
                        📝 ${ag.observacoes}
                    </div>` : ''}
                    <div style="display:flex; gap:8px;
                                margin-top:14px">
                        <a href="${BASE_URL}/index.php
                            ?controller=agendamento
                            &action=detalhes&id=${ag.id}"
                           class="btn btn-secundario btn-sm">
                            👁️ Detalhes
                        </a>
                        ${ag.status === 'AGENDADO' ? `
                        <a href="${BASE_URL}/index.php
                            ?controller=agendamento
                            &action=form&id=${ag.id}"
                           class="btn btn-aviso btn-sm">
                            ✏️ Editar
                        </a>
                        <a href="${BASE_URL}/index.php
                            ?controller=agendamento
                            &action=atualizarStatus
                            &id=${ag.id}&status=CANCELADO"
                           class="btn btn-perigo btn-sm"
                           onclick="return confirm(
                               'Cancelar este agendamento?')">
                            ❌ Cancelar
                        </a>` : ''}
                    </div>
                </div>`;
        })
        .catch(() => {
            document.getElementById('popup-body').innerHTML =
                '<div class="popup-vazio">Erro ao carregar.</div>';
        });
}

// ================================================
// Abre formulário de novo agendamento
// com data e profissional pré-preenchidos
// ================================================
function abrirFormulario(data, hora, profissionalId) {
    const dataHora = data + 'T' + hora + ':00';
    window.location.href = BASE_URL +
        '/index.php?controller=agendamento&action=form' +
        '&data_hora=' + dataHora +
        '&profissional_id=' + profissionalId;
}

// ================================================
// Fecha popup
// ================================================
function fecharPopup() {
    document.getElementById('popup-overlay')
        .classList.remove('aberto');
}

// Fecha com ESC
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') fecharPopup();
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>