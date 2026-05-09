<?php
/** @var array $diagnostico */
/** @var array $evolucao    */
$diagnostico = $diagnostico ?? [];
$evolucao    = $evolucao    ?? [];

require_once VIEWS_PATH . '/layouts/header.php';
?>

<!-- Ficha do diagnóstico -->
<div class="form-card" style="max-width:100%;
                               margin-bottom:24px">

    <h3>🧪 Diagnóstico Capilar</h3>

    <!-- Identificação -->
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
            <div style="font-size:18px; font-weight:700;
                        color:var(--cor-primaria);
                        margin-top:4px">
                <?= htmlspecialchars(
                    $diagnostico['cliente_nome']) ?>
            </div>
            <div style="font-size:13px;
                        color:var(--cor-texto-claro)">
                <?= htmlspecialchars(
                    $diagnostico['cliente_telefone']) ?>
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
                    $diagnostico['profissional_nome']) ?>
            </div>
        </div>

        <div>
            <div style="font-size:12px;
                        color:var(--cor-texto-claro);
                        text-transform:uppercase;
                        letter-spacing:.5px">
                Data do Diagnóstico
            </div>
            <div style="font-size:16px; font-weight:600;
                        margin-top:4px">
                <?= date('d/m/Y',
                    strtotime($diagnostico['criado_em'])) ?>
            </div>
        </div>
    </div>

    <!-- Características capilares -->
    <div style="background:var(--cor-fundo);
                border-radius:var(--raio-borda);
                padding:20px; margin-bottom:20px">
        <div style="font-weight:700; color:var(--cor-primaria);
                    margin-bottom:16px; font-size:15px">
            💇 Características do Cabelo
        </div>
        <div style="display:grid;
                    grid-template-columns:repeat(3,1fr);
                    gap:16px">
            <div>
                <div style="font-size:12px;
                            color:var(--cor-texto-claro);
                            text-transform:uppercase">
                    Tipo
                </div>
                <div style="font-size:22px; margin-top:4px">
                    <?php
                    $icones = [
                        'Liso'     => '〰️',
                        'Ondulado' => '🌊',
                        'Cacheado' => '🌀',
                        'Crespo'   => '⚡'
                    ];
                    $tipo = $diagnostico['tipo_cabelo'] ?? '';
                    echo ($icones[$tipo] ?? '—') . ' ';
                    ?>
                    <span style="font-size:16px;
                                 font-weight:600">
                        <?= $tipo ?: '—' ?>
                    </span>
                </div>
            </div>

            <div>
                <div style="font-size:12px;
                            color:var(--cor-texto-claro);
                            text-transform:uppercase">
                    Porosidade
                </div>
                <div style="font-size:16px; font-weight:600;
                            margin-top:8px">
                    <?php
                    $porosidade = $diagnostico['porosidade'] ?? '';
                    $corPoros = [
                        'Baixa' => '#2196F3',
                        'Média' => '#FF9800',
                        'Alta'  => '#E53935'
                    ];
                    $cor = $corPoros[$porosidade] ?? '#777';
                    ?>
                    <span style="color:<?= $cor ?>">
                        <?= $porosidade ?: '—' ?>
                    </span>
                </div>
            </div>

            <div>
                <div style="font-size:12px;
                            color:var(--cor-texto-claro);
                            text-transform:uppercase">
                    Oleosidade
                </div>
                <div style="font-size:16px; font-weight:600;
                            margin-top:8px">
                    <?= $diagnostico['oleosidade'] ?: '—' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Histórico químico -->
    <?php if (!empty($diagnostico['historico_quimico'])): ?>
    <div style="margin-bottom:16px">
        <div style="font-weight:700; margin-bottom:8px">
            ⚗️ Histórico Químico
        </div>
        <p style="font-size:14px; line-height:1.7;
                  color:var(--cor-texto)">
            <?= nl2br(htmlspecialchars(
                $diagnostico['historico_quimico'])) ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- Problemas -->
    <?php if (!empty($diagnostico['problemas'])): ?>
    <div style="background:#FFEBEE; border-radius:var(--raio-borda);
                padding:16px; margin-bottom:16px;
                border-left:4px solid var(--cor-erro)">
        <div style="font-weight:700; margin-bottom:8px;
                    color:#C62828">
            ⚠️ Problemas Identificados
        </div>
        <p style="font-size:14px; line-height:1.7">
            <?= nl2br(htmlspecialchars(
                $diagnostico['problemas'])) ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- Tratamento recomendado -->
    <?php if (!empty($diagnostico['tratamento_recomendado'])): ?>
    <div style="background:#E8F5E9; border-radius:var(--raio-borda);
                padding:16px; margin-bottom:16px;
                border-left:4px solid var(--cor-sucesso)">
        <div style="font-weight:700; margin-bottom:8px;
                    color:#2E7D32">
            ✅ Tratamento Recomendado
        </div>
        <p style="font-size:14px; line-height:1.7">
            <?= nl2br(htmlspecialchars(
                $diagnostico['tratamento_recomendado'])) ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- Ações -->
    <div class="form-acoes">
        <a href="<?= BASE_URL ?>
            /index.php?controller=diagnostico
            &action=form&id=<?= $diagnostico['id'] ?>"
           class="btn btn-aviso" style="width:auto">
            ✏️ Editar Diagnóstico
        </a>
        <a href="<?= BASE_URL ?>
            /index.php?controller=cliente
            &action=detalhes
            &id=<?= $diagnostico['cliente_id'] ?>"
           class="btn btn-secundario" style="width:auto">
            👤 Ver Cliente
        </a>
        <a href="<?= BASE_URL ?>
            /index.php?controller=diagnostico"
           class="btn btn-secundario" style="width:auto">
            ← Voltar
        </a>
    </div>
</div>

<!-- Evolução capilar -->
<?php if (count($evolucao) > 1): ?>
<div class="tabela-container">
    <div class="tabela-header">
        <h3>📈 Evolução Capilar da Cliente</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Serviço</th>
                <th>Tipo</th>
                <th>Porosidade</th>
                <th>Oleosidade</th>
                <th>Profissional</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($evolucao as $ev): ?>
            <tr <?= $ev['id'] === $diagnostico['id']
                    ? 'style="background:rgba(139,94,131,.08)"'
                    : '' ?>>
                <td>
                    <?= date('d/m/Y',
                        strtotime($ev['criado_em'])) ?>
                    <?php if ($ev['id'] ===
                            $diagnostico['id']): ?>
                    <br>
                    <small style="color:var(--cor-primaria);
                                  font-weight:600">
                        ← atual
                    </small>
                    <?php endif; ?>
                </td>
                <td>
                    <?= htmlspecialchars($ev['servico']) ?>
                </td>
                <td>
                    <?= $ev['tipo_cabelo'] ?: '—' ?>
                </td>
                <td>
                    <?= $ev['porosidade'] ?: '—' ?>
                </td>
                <td>
                    <?= $ev['oleosidade'] ?: '—' ?>
                </td>
                <td>
                    <?= htmlspecialchars(
                        $ev['profissional_nome']) ?>
                </td>
                <td>
                    <?php if ($ev['id'] !==
                            $diagnostico['id']): ?>
                    <a href="<?= BASE_URL ?>
                        /index.php?controller=diagnostico
                        &action=detalhes
                        &id=<?= $ev['id'] ?>"
                       class="btn btn-secundario btn-sm">
                        👁️
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>