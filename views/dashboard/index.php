<?php
// ================================================
// Variáveis esperadas pelo Controller
// Os valores padrão evitam erros se alguma
// variável não for passada pelo controller
// ================================================

/** @var int   $totalClientes    */
/** @var int   $agendamentosHoje */
/** @var int   $agendamentosMes  */
/** @var int   $atendimentosMes  */
/** @var float $faturamentoMes   */
/** @var array $proximosAgs      */
/** @var array $ultimosClientes  */
/** @var array $statusContagem   */

$totalClientes    = $totalClientes    ?? 0;
$agendamentosHoje = $agendamentosHoje ?? 0;
$agendamentosMes  = $agendamentosMes  ?? 0;
$atendimentosMes  = $atendimentosMes  ?? 0;
$faturamentoMes   = $faturamentoMes   ?? 0.0;
$proximosAgs      = $proximosAgs      ?? [];
$ultimosClientes  = $ultimosClientes  ?? [];
$statusContagem   = $statusContagem   ?? [
    'AGENDADO'  => 0,
    'REALIZADO' => 0,
    'CANCELADO' => 0
];
?>

<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<!-- ================================================
     CARDS DE RESUMO
================================================ -->
<div class="cards-grid">

    <div class="card">
        <div class="card-icone">👤</div>
        <div class="card-numero">
            <?= $totalClientes ?>
        </div>
        <div class="card-label">Clientes Ativos</div>
    </div>

    <div class="card" style="border-left-color: #2196F3">
        <div class="card-icone">📅</div>
        <div class="card-numero" style="color: #2196F3">
            <?= $agendamentosHoje ?>
        </div>
        <div class="card-label">Agendamentos Hoje</div>
    </div>

    <div class="card" style="border-left-color: #FF9800">
        <div class="card-icone">📊</div>
        <div class="card-numero" style="color: #FF9800">
            <?= $agendamentosMes ?>
        </div>
        <div class="card-label">Agendamentos no Mês</div>
    </div>

    <div class="card" style="border-left-color: #4CAF50">
        <div class="card-icone">💰</div>
        <div class="card-numero" style="color: #4CAF50; font-size: 22px">
            R$ <?= number_format($faturamentoMes, 2, ',', '.') ?>
        </div>
        <div class="card-label">Faturamento do Mês</div>
    </div>

</div>

<!-- ================================================
     SEGUNDA LINHA DE CARDS — STATUS
================================================ -->
<div class="cards-grid" style="margin-bottom: 28px">

    <div class="card" style="border-left-color: #1565C0">
        <div class="card-icone">🕐</div>
        <div class="card-numero" style="color: #1565C0">
            <?= $statusContagem['AGENDADO'] ?>
        </div>
        <div class="card-label">Aguardando Atendimento</div>
    </div>

    <div class="card" style="border-left-color: #2E7D32">
        <div class="card-icone">✅</div>
        <div class="card-numero" style="color: #2E7D32">
            <?= $statusContagem['REALIZADO'] ?>
        </div>
        <div class="card-label">Atendimentos Realizados</div>
    </div>

    <div class="card" style="border-left-color: #C62828">
        <div class="card-icone">❌</div>
        <div class="card-numero" style="color: #C62828">
            <?= $statusContagem['CANCELADO'] ?>
        </div>
        <div class="card-label">Cancelamentos</div>
    </div>

    <div class="card" style="border-left-color: #6A1B9A">
        <div class="card-icone">💆</div>
        <div class="card-numero" style="color: #6A1B9A">
            <?= $atendimentosMes ?>
        </div>
        <div class="card-label">Atendimentos no Mês</div>
    </div>

</div>

<!-- ================================================
     CONTEÚDO EM DUAS COLUNAS
================================================ -->
<div style="display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 28px">

    <!-- PRÓXIMOS AGENDAMENTOS -->
    <div class="tabela-container">
        <div class="tabela-header">
            <h3>📅 Próximos Agendamentos</h3>
            <a href="<?= BASE_URL ?>
                /index.php?controller=agendamento&action=index"
               class="btn btn-secundario btn-sm">
                Ver todos
            </a>
        </div>

        <?php if (empty($proximosAgs)): ?>
        <div style="padding: 24px; text-align:center;
                    color: var(--cor-texto-claro)">
            Nenhum agendamento nos próximos 7 dias.
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Cliente</th>
                    <th>Serviço</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proximosAgs as $ag): ?>
                <tr>
                    <td>
                        <strong>
                            <?= date('d/m', strtotime($ag['data_hora'])) ?>
                        </strong>
                        <br>
                        <small style="color: var(--cor-texto-claro)">
                            <?= date('H:i', strtotime($ag['data_hora'])) ?>
                        </small>
                    </td>
                    <td>
                        <?= htmlspecialchars($ag['cliente_nome']) ?>
                        <br>
                        <small style="color: var(--cor-texto-claro)">
                            <?= htmlspecialchars($ag['cliente_telefone']) ?>
                        </small>
                    </td>
                    <td>
                        <?= htmlspecialchars($ag['servico']) ?>
                        <br>
                        <small style="color: var(--cor-texto-claro)">
                            <?= htmlspecialchars($ag['profissional_nome']) ?>
                        </small>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- ÚLTIMOS CLIENTES -->
    <div class="tabela-container">
        <div class="tabela-header">
            <h3>👤 Últimos Clientes</h3>
            <a href="<?= BASE_URL ?>
                /index.php?controller=cliente&action=index"
               class="btn btn-secundario btn-sm">
                Ver todos
            </a>
        </div>

        <?php if (empty($ultimosClientes)): ?>
        <div style="padding: 24px; text-align:center;
                    color: var(--cor-texto-claro)">
            Nenhum cliente cadastrado ainda.
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Cadastro</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ultimosClientes as $cliente): ?>
                <tr>
                    <td>
                        <a href="<?= BASE_URL ?>
                            /index.php?controller=cliente
                            &action=detalhes
                            &id=<?= $cliente['id'] ?>">
                            <?= htmlspecialchars($cliente['nome']) ?>
                        </a>
                    </td>
                    <td>
                        <?= htmlspecialchars($cliente['telefone']) ?>
                    </td>
                    <td>
                        <?= date('d/m/Y',
                            strtotime($cliente['criado_em'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</div>

<!-- ================================================
     AÇÕES RÁPIDAS
================================================ -->
<div class="tabela-container" style="padding: 24px">
    <h3 style="color: var(--cor-primaria);
               margin-bottom: 16px">
        ⚡ Ações Rápidas
    </h3>
    <div style="display: flex; gap: 12px; flex-wrap: wrap">
        <a href="<?= BASE_URL ?>
            /index.php?controller=cliente&action=form"
           class="btn btn-primario" style="width: auto">
            👤 Novo Cliente
        </a>
        <a href="<?= BASE_URL ?>
            /index.php?controller=agendamento&action=form"
           class="btn btn-sucesso" style="width: auto">
            📅 Novo Agendamento
        </a>
        <a href="<?= BASE_URL ?>
            /index.php?controller=atendimento&action=form"
           class="btn btn-aviso" style="width: auto">
            💆 Registrar Atendimento
        </a>
        <a href="<?= BASE_URL ?>
            /index.php?controller=diagnostico&action=form"
           class="btn btn-secundario" style="width: auto">
            🧪 Novo Diagnóstico
        </a>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>