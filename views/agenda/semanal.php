<?php
/** @var string $segunda        */
/** @var string $domingo        */
/** @var array  $diasSemana     */
/** @var string $semanaAnterior */
/** @var string $semanaSeguinte */
/** @var array  $agendamentos   */
/** @var array  $profissionais  */
/** @var int    $profissionalId */
/** @var array  $horarios       */
$segunda        = $segunda        ?? date('Y-m-d');
$domingo        = $domingo        ?? date('Y-m-d');
$diasSemana     = $diasSemana     ?? [];
$semanaAnterior = $semanaAnterior ?? '';
$semanaSeguinte = $semanaSeguinte ?? '';
$agendamentos   = $agendamentos   ?? [];
$profissionais  = $profissionais  ?? [];
$profissionalId = $profissionalId ?? 0;
$horarios       = $horarios       ?? range(8, 20);

$hoje       = date('Y-m-d');
$nomesDias  = ['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'];

require_once VIEWS_PATH . '/layouts/header.php';
?>

<!-- Toolbar -->
<div class="agenda-toolbar">

    <div class="agenda-toolbar-centro">
        <a href="<?= BASE_URL ?>
            /index.php?controller=agenda&action=semanal
            &data=<?= $semanaAnterior ?>
            &profissional=<?= $profissionalId ?>"
           class="agenda-nav-btn">‹</a>
        <span>
            📆 <?= date('d/m', strtotime($segunda)) ?> —
               <?= date('d/m/Y', strtotime($domingo)) ?>
        </span>
        <a href="<?= BASE_URL ?>
            /index.php?controller=agenda&action=semanal
            &data=<?= $semanaSeguinte ?>
            &profissional=<?= $profissionalId ?>"
           class="agenda-nav-btn">›</a>
    </div>

    <!-- Filtro -->
    <form method="GET"
          action="<?= BASE_URL ?>/index.php"
          style="display:flex; gap:8px; align-items:center">
        <input type="hidden" name="controller" value="agenda">
        <input type="hidden" name="action" value="semanal">
        <input type="hidden" name="data" value="<?= $segunda ?>">
        <select name="profissional"
                onchange="this.form.submit()"
                style="padding:8px 12px;
                       border:1px solid var(--cor-borda);
                       border-radius:var(--raio-borda);
                       font-size:13px">
            <option value="0">Todos</option>
            <?php foreach ($profissionais as $p): ?>
            <option value="<?= $p['id'] ?>"
                <?= $profissionalId === (int)$p['id']
                    ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['nome']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- Alternar visão -->
    <div style="display:flex; gap:8px">
        <a href="<?= BASE_URL ?>
            /index.php?controller=agenda
            &profissional=<?= $profissionalId ?>"
           class="btn btn-secundario btn-sm">
            📅 Mensal
        </a>
        <span class="btn btn-primario btn-sm"
              style="width:auto; cursor:default">
            📆 Semanal
        </span>
        <a href="<?= BASE_URL ?>
            /index.php?controller=agendamento&action=form"
           class="btn btn-sucesso btn-sm">
            + Novo
        </a>
    </div>
</div>

<!-- Grade semanal -->
<div class="semana-grid">

    <!-- Cabeçalho -->
    <div class="semana-cabecalho">
        <div class="semana-cabecalho-hora"></div>
        <?php foreach ($diasSemana as $i => $data): ?>
        <div class="semana-cabecalho-dia
             <?= $data === $hoje ? 'hoje-col' : '' ?>">
            <div class="nome-dia">
                <?= $nomesDias[$i] ?>
            </div>
            <div class="numero-dia">
                <?= date('d', strtotime($data)) ?>
            </div>
            <div style="font-size:11px;
                        color:var(--cor-texto-claro)">
                <?= date('M', strtotime($data)) ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Linhas de horário -->
    <?php foreach ($horarios as $hora): ?>
    <div class="semana-linha">

        <!-- Coluna de hora -->
        <div class="semana-hora">
            <?= sprintf('%02d:00', $hora) ?>
        </div>

        <!-- Colunas dos dias -->
        <?php foreach ($diasSemana as $data): ?>
        <?php
        $ags = $agendamentos[$data][$hora] ?? [];
        $isHoje = ($data === $hoje);
        ?>
        <div class="semana-celula
             <?= $isHoje ? 'hoje-col' : '' ?>"
             onclick="abrirNovoAgendamento(
                '<?= $data ?>',
                '<?= sprintf('%02d', $hora) ?>')"
             title="Clique para novo agendamento">

            <?php foreach ($ags as $ag):
                $classe = 'evento-' . strtolower($ag['status']);
            ?>
            <div class="evento-semanal <?= $classe ?>"
                 onclick="event.stopPropagation();
                          abrirPopup(<?= $ag['id'] ?>)"
                 title="<?= htmlspecialchars(
                    $ag['cliente_nome'] . ' — ' .
                    $ag['servico']) ?>">
                <strong>
                    <?= date('H:i',
                        strtotime($ag['data_hora'])) ?>
                </strong><br>
                <?= htmlspecialchars(
                    mb_strimwidth($ag['cliente_nome'],
                    0, 15, '…')) ?><br>
                <span style="opacity:.8">
                    <?= htmlspecialchars(
                        mb_strimwidth($ag['servico'],
                        0, 15, '…')) ?>
                </span>
            </div>
            <?php endforeach; ?>

        </div>
        <?php endforeach; ?>

    </div>
    <?php endforeach; ?>

</div>

<!-- Popup (mesmo da visão mensal) -->
<div class="popup-overlay" id="popup-overlay"
     onclick="fecharPopup()">
    <div class="popup-box" onclick="event.stopPropagation()">
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

function abrirPopup(id) {
    document.getElementById('popup-overlay')
        .classList.add('aberto');
    document.getElementById('popup-body').innerHTML =
        '<div class="popup-vazio">Carregando...</div>';

    fetch(BASE_URL +
        '/index.php?controller=agendamento' +
        '&action=detalhesJson&id=' + id)
        .then(r => r.json())
        .then(ag => {
            document.getElementById('popup-titulo')
                .textContent = '📅 ' + ag.data + ' — ' + ag.hora;

            const statusClasse = {
                'AGENDADO' : 'badge-agendado',
                'REALIZADO': 'badge-realizado',
                'CANCELADO': 'badge-cancelado'
            };

            document.getElementById('popup-body').innerHTML = `
                <div class="popup-item">
                    <div class="popup-item-hora">🕐 ${ag.hora}</div>
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
                    <div style="margin-bottom:10px">
                        👩 ${ag.profissional_nome}
                    </div>
                    <span class="badge
                        ${statusClasse[ag.status] || ''}">
                        ${ag.status}
                    </span>
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
                        </a>` : ''}
                    </div>
                </div>`;
        });
}

function abrirNovoAgendamento(data, hora) {
    window.location.href = BASE_URL +
        '/index.php?controller=agendamento&action=form' +
        '&data_hora=' + data + 'T' + hora + ':00';
}

function fecharPopup() {
    document.getElementById('popup-overlay')
        .classList.remove('aberto');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') fecharPopup();
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>