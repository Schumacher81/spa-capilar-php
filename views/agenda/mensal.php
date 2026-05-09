<?php
/** @var int    $mes            */
/** @var int    $ano            */
/** @var int    $mesAnterior    */
/** @var int    $anoAnterior    */
/** @var int    $mesSeguinte    */
/** @var int    $anoSeguinte    */
/** @var array  $agendamentos   */
/** @var array  $profissionais  */
/** @var int    $profissionalId */
/** @var string $nomeMes        */
$mes            = $mes            ?? (int)date('n');
$ano            = $ano            ?? (int)date('Y');
$mesAnterior    = $mesAnterior    ?? 0;
$anoAnterior    = $anoAnterior    ?? 0;
$mesSeguinte    = $mesSeguinte    ?? 0;
$anoSeguinte    = $anoSeguinte    ?? 0;
$agendamentos   = $agendamentos   ?? [];
$profissionais  = $profissionais  ?? [];
$profissionalId = $profissionalId ?? 0;
$nomeMes        = $nomeMes        ?? '';

// Dados do calendário
$primeiroDia    = mktime(0, 0, 0, $mes, 1, $ano);
$totalDias      = (int)date('t', $primeiroDia);
$diaSemanaInicio= (int)date('N', $primeiroDia); // 1=seg, 7=dom
$hoje           = date('Y-m-d');

// Nomes dos dias da semana
$nomesDias = ['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'];

require_once VIEWS_PATH . '/layouts/header.php';
?>

<!-- Toolbar -->
<div class="agenda-toolbar">

    <!-- Navegação -->
    <div class="agenda-toolbar-centro">
        <a href="<?= BASE_URL ?>
            /index.php?controller=agenda
            &mes=<?= $mesAnterior ?>
            &ano=<?= $anoAnterior ?>
            &profissional=<?= $profissionalId ?>"
           class="agenda-nav-btn" title="Mês anterior">
            ‹
        </a>
        <span>📅 <?= $nomeMes ?> <?= $ano ?></span>
        <a href="<?= BASE_URL ?>
            /index.php?controller=agenda
            &mes=<?= $mesSeguinte ?>
            &ano=<?= $anoSeguinte ?>
            &profissional=<?= $profissionalId ?>"
           class="agenda-nav-btn" title="Próximo mês">
            ›
        </a>
    </div>

    <!-- Filtro por profissional -->
    <form method="GET"
          action="<?= BASE_URL ?>/index.php"
          style="display:flex; gap:8px; align-items:center">
        <input type="hidden" name="controller" value="agenda">
        <input type="hidden" name="mes" value="<?= $mes ?>">
        <input type="hidden" name="ano" value="<?= $ano ?>">
        <select name="profissional"
                onchange="this.form.submit()"
                style="padding:8px 12px;
                       border:1px solid var(--cor-borda);
                       border-radius:var(--raio-borda);
                       font-size:13px">
            <option value="0">Todos os profissionais</option>
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
        <span class="btn btn-primario btn-sm"
              style="width:auto; cursor:default">
            📅 Mensal
        </span>
        <a href="<?= BASE_URL ?>
            /index.php?controller=agenda&action=semanal
            &profissional=<?= $profissionalId ?>"
           class="btn btn-secundario btn-sm">
            📆 Semanal
        </a>
        <a href="<?= BASE_URL ?>
            /index.php?controller=agendamento&action=form"
           class="btn btn-sucesso btn-sm">
            + Novo
        </a>
    </div>
</div>

<!-- Grade do calendário -->
<div class="calendario-grade">

    <!-- Cabeçalho com dias da semana -->
    <div class="calendario-cabecalho">
        <?php foreach ($nomesDias as $nomeDia): ?>
        <div><?= $nomeDia ?></div>
        <?php endforeach; ?>
    </div>

    <!-- Corpo do calendário -->
    <div class="calendario-corpo">

        <?php
        // Dias em branco antes do primeiro dia do mês
        for ($i = 1; $i < $diaSemanaInicio; $i++):
        ?>
        <div class="calendario-dia outro-mes"></div>
        <?php endfor; ?>

        <?php
        // Dias do mês
        for ($dia = 1; $dia <= $totalDias; $dia++):
            $dataStr   = sprintf('%04d-%02d-%02d',
                $ano, $mes, $dia);
            $isHoje    = ($dataStr === $hoje);
            $ags       = $agendamentos[$dataStr] ?? [];
            $maxExibir = 3; // máximo de eventos visíveis
        ?>
        <div class="calendario-dia <?= $isHoje ? 'hoje' : '' ?>"
             onclick="abrirDia('<?= $dataStr ?>')"
             title="Clique para ver ou adicionar agendamentos">

            <div class="dia-numero"><?= $dia ?></div>

            <?php
            $count = 0;
            foreach ($ags as $ag):
                if ($count >= $maxExibir) break;
                $count++;
                $classe = 'evento-' . strtolower($ag['status']);
            ?>
            <div class="evento-mensal <?= $classe ?>"
                 onclick="event.stopPropagation();
                          abrirPopup(<?= $ag['id'] ?>)"
                 title="<?= htmlspecialchars(
                    $ag['cliente_nome'] . ' — ' .
                    $ag['servico']) ?>">
                <?= date('H:i', strtotime($ag['data_hora'])) ?>
                <?= htmlspecialchars(
                    mb_strimwidth($ag['cliente_nome'],
                    0, 12, '…')) ?>
            </div>
            <?php endforeach; ?>

            <?php if (count($ags) > $maxExibir): ?>
            <div class="mais-eventos"
                 onclick="event.stopPropagation();
                          abrirDia('<?= $dataStr ?>')">
                +<?= count($ags) - $maxExibir ?> mais
            </div>
            <?php endif; ?>

        </div>
        <?php endfor; ?>

        <?php
        // Dias em branco após o último dia do mês
        $diaSemanaFim = (int)date('N',
            mktime(0, 0, 0, $mes, $totalDias, $ano));
        for ($i = $diaSemanaFim; $i < 7; $i++):
        ?>
        <div class="calendario-dia outro-mes"></div>
        <?php endfor; ?>

    </div>
</div>

<!-- Legenda -->
<div style="display:flex; gap:16px; margin-top:16px;
            flex-wrap:wrap">
    <div style="display:flex; align-items:center; gap:6px;
                font-size:13px">
        <span style="width:16px; height:16px;
                     background:#BBDEFB;
                     border-left:3px solid #1565C0;
                     border-radius:3px;
                     display:inline-block"></span>
        Agendado
    </div>
    <div style="display:flex; align-items:center; gap:6px;
                font-size:13px">
        <span style="width:16px; height:16px;
                     background:#C8E6C9;
                     border-left:3px solid #2E7D32;
                     border-radius:3px;
                     display:inline-block"></span>
        Realizado
    </div>
    <div style="display:flex; align-items:center; gap:6px;
                font-size:13px">
        <span style="width:16px; height:16px;
                     background:#FFCDD2;
                     border-left:3px solid #C62828;
                     border-radius:3px;
                     display:inline-block"></span>
        Cancelado
    </div>
</div>

<!-- ====================================================
     POPUP DE AGENDAMENTO
==================================================== -->
<div class="popup-overlay" id="popup-overlay"
     onclick="fecharPopup()">
    <div class="popup-box" onclick="event.stopPropagation()">
        <div class="popup-header">
            <h4 id="popup-titulo">Agendamentos</h4>
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
// Abre popup com resumo do agendamento
// ================================================
function abrirPopup(id) {
    fetch(BASE_URL +
        '/index.php?controller=agendamento' +
        '&action=detalhesJson&id=' + id)
        .then(r => r.json())
        .then(ag => {
            document.getElementById('popup-titulo').textContent =
                '📅 ' + ag.data + ' — ' + ag.hora;

            const statusClasse = {
                'AGENDADO' : 'badge-agendado',
                'REALIZADO': 'badge-realizado',
                'CANCELADO': 'badge-cancelado'
            };

            document.getElementById('popup-body').innerHTML = `
                <div class="popup-item">
                    <div class="popup-item-hora">
                        🕐 ${ag.hora}
                    </div>
                    <div class="popup-item-cliente">
                        👤 ${ag.cliente_nome}
                    </div>
                    <div style="font-size:13px;
                                color:#777; margin-bottom:4px">
                        📞 ${ag.cliente_telefone}
                    </div>
                    <div class="popup-item-servico">
                        ✂️ ${ag.servico}
                    </div>
                    <div style="margin-bottom:10px">
                        👩 ${ag.profissional_nome}
                    </div>
                    <span class="badge ${statusClasse[ag.status] || ''}">
                        ${ag.status}
                    </span>
                    ${ag.observacoes ? `
                    <div style="margin-top:10px; font-size:13px;
                                color:#555; padding:8px;
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
                        </a>` : ''}
                    </div>
                </div>
            `;

            document.getElementById('popup-overlay')
                .classList.add('aberto');
        })
        .catch(() => {
            document.getElementById('popup-body').innerHTML =
                '<div class="popup-vazio">Erro ao carregar.</div>';
        });
}

// ================================================
// Abre dia — popup com todos os agendamentos do dia
// ================================================
function abrirDia(data) {
    document.getElementById('popup-titulo').textContent =
        '📅 Carregando...';
    document.getElementById('popup-body').innerHTML =
        '<div class="popup-vazio">Carregando...</div>';
    document.getElementById('popup-overlay')
        .classList.add('aberto');

    fetch(BASE_URL +
        '/index.php?controller=agenda&action=dia&data=' + data)
        .then(r => r.json())
        .then(resp => {
            document.getElementById('popup-titulo').textContent =
                '📅 ' + resp.data;

            if (resp.agendamentos.length === 0) {
                document.getElementById('popup-body').innerHTML =
                    `<div class="popup-vazio">
                        Nenhum agendamento neste dia.
                        <br><br>
                        <a href="${BASE_URL}/index.php
                            ?controller=agendamento&action=form
                            &data_hora=${data}T08:00"
                           class="btn btn-sucesso"
                           style="width:auto">
                            + Novo Agendamento
                        </a>
                    </div>`;
                return;
            }

            const statusClasse = {
                'AGENDADO' : 'badge-agendado',
                'REALIZADO': 'badge-realizado',
                'CANCELADO': 'badge-cancelado'
            };

            let html = '';
            resp.agendamentos.forEach(ag => {
                const hora = ag.data_hora.substring(11, 16);
                html += `
                <div class="popup-item">
                    <div style="display:flex;
                                justify-content:space-between;
                                align-items:start">
                        <div>
                            <div class="popup-item-hora">
                                🕐 ${hora}
                            </div>
                            <div class="popup-item-cliente">
                                ${ag.cliente_nome}
                            </div>
                            <div class="popup-item-servico">
                                ✂️ ${ag.servico}
                            </div>
                            <div style="font-size:12px;color:#777">
                                👩 ${ag.profissional_nome}
                            </div>
                        </div>
                        <span class="badge
                            ${statusClasse[ag.status] || ''}">
                            ${ag.status}
                        </span>
                    </div>
                    <div style="display:flex; gap:6px;
                                margin-top:10px">
                        <a href="${BASE_URL}/index.php
                            ?controller=agendamento
                            &action=detalhes&id=${ag.id}"
                           class="btn btn-secundario btn-sm">
                            👁️
                        </a>
                        ${ag.status === 'AGENDADO' ? `
                        <a href="${BASE_URL}/index.php
                            ?controller=agendamento
                            &action=form&id=${ag.id}"
                           class="btn btn-aviso btn-sm">
                            ✏️
                        </a>` : ''}
                    </div>
                </div>`;
            });

            // Botão para novo agendamento no dia
            html += `
            <a href="${BASE_URL}/index.php
                ?controller=agendamento&action=form
                &data_hora=${data}T08:00"
               class="btn btn-sucesso"
               style="width:100%; margin-top:4px;
                      justify-content:center">
                + Novo Agendamento Neste Dia
            </a>`;

            document.getElementById('popup-body')
                .innerHTML = html;
        });
}

// ================================================
// Fecha o popup
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