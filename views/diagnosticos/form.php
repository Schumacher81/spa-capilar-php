<?php
/** @var array      $diagnostico             */
/** @var array      $atendimentosDisponiveis */
/** @var array|null $atendimentoSelecionado  */
/** @var array      $erros                   */
/** @var int        $atendimentoId           */
$diagnostico             = $diagnostico             ?? [];
$atendimentosDisponiveis = $atendimentosDisponiveis ?? [];
$atendimentoSelecionado  = $atendimentoSelecionado  ?? null;
$erros                   = $erros                   ?? [];
$atendimentoId           = $atendimentoId           ?? 0;

$v = function(string $campo) use ($diagnostico): string {
    return htmlspecialchars($diagnostico[$campo] ?? '');
};

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="form-card" style="max-width: 800px">

    <h3>
        <?= !empty($diagnostico['id'])
            ? '✏️ Editar Diagnóstico'
            : '🧪 Novo Diagnóstico Capilar' ?>
    </h3>

    <?php if (!empty($erros)): ?>
    <div class="alerta alerta-erro">
        <div>
            <strong>❌ Corrija os erros:</strong>
            <ul style="margin-top:6px; padding-left:18px">
                <?php foreach ($erros as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <!-- Info do atendimento selecionado -->
    <?php if ($atendimentoSelecionado): ?>
    <div class="alerta alerta-aviso">
        💆 Atendimento:
        <strong>
            <?= htmlspecialchars(
                $atendimentoSelecionado['cliente_nome']) ?>
        </strong>
        — <?= htmlspecialchars(
            $atendimentoSelecionado['servico']) ?>
        — <?= date('d/m/Y', strtotime(
            $atendimentoSelecionado['realizado_em'])) ?>
    </div>
    <?php endif; ?>

    <form method="POST"
          action="<?= BASE_URL ?>
            /index.php?controller=diagnostico&action=salvar">

        <input type="hidden" name="id" value="<?= $v('id') ?>">

        <!-- Seleção do atendimento -->
        <?php if (empty($diagnostico['id'])): ?>
        <div class="form-grupo">
            <label for="atendimento_id">Atendimento *</label>
            <select id="atendimento_id"
                    name="atendimento_id" required>
                <option value="">
                    Selecione o atendimento...
                </option>
                <?php foreach (
                    $atendimentosDisponiveis as $at): ?>
                <option value="<?= $at['id'] ?>"
                    <?= (int)$at['id'] === $atendimentoId
                        ? 'selected' : '' ?>>
                    <?= date('d/m/Y',
                        strtotime($at['realizado_em'])) ?> —
                    <?= htmlspecialchars($at['cliente_nome']) ?>
                    — <?= htmlspecialchars($at['servico']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php if (empty($atendimentosDisponiveis)): ?>
            <small style="color:var(--cor-aviso)">
                ⚠️ Nenhum atendimento disponível para
                diagnóstico.
            </small>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <input type="hidden"
               name="atendimento_id"
               value="<?= $v('atendimento_id') ?>">
        <div class="form-grupo">
            <label>Atendimento</label>
            <div style="padding:10px 14px;
                        background:var(--cor-fundo);
                        border-radius:var(--raio-borda);
                        border:1px solid var(--cor-borda)">
                <?= htmlspecialchars(
                    $diagnostico['cliente_nome'] ?? '') ?>
                — <?= htmlspecialchars(
                    $diagnostico['servico'] ?? '') ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- ========================================
             SEÇÃO 1 — CARACTERÍSTICAS DO CABELO
        ======================================== -->
        <div style="margin:24px 0 16px;
                    padding-bottom:8px;
                    border-bottom:2px solid var(--cor-primaria);
                    color:var(--cor-primaria);
                    font-weight:700; font-size:15px">
            💇 Características do Cabelo
        </div>

        <div class="form-linha-3">

            <div class="form-grupo">
                <label for="tipo_cabelo">Tipo de Cabelo</label>
                <select id="tipo_cabelo" name="tipo_cabelo">
                    <option value="">Selecione...</option>
                    <?php
                    $tipos = ['Liso','Ondulado',
                              'Cacheado','Crespo'];
                    foreach ($tipos as $t): ?>
                    <option value="<?= $t ?>"
                        <?= $v('tipo_cabelo') === $t
                            ? 'selected' : '' ?>>
                        <?= $t ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-grupo">
                <label for="porosidade">Porosidade</label>
                <select id="porosidade" name="porosidade">
                    <option value="">Selecione...</option>
                    <?php
                    $porosidades = ['Baixa','Média','Alta'];
                    foreach ($porosidades as $p): ?>
                    <option value="<?= $p ?>"
                        <?= $v('porosidade') === $p
                            ? 'selected' : '' ?>>
                        <?= $p ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-grupo">
                <label for="oleosidade">Oleosidade</label>
                <select id="oleosidade" name="oleosidade">
                    <option value="">Selecione...</option>
                    <?php
                    $oleosidades = ['Seco','Normal','Oleoso'];
                    foreach ($oleosidades as $o): ?>
                    <option value="<?= $o ?>"
                        <?= $v('oleosidade') === $o
                            ? 'selected' : '' ?>>
                        <?= $o ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

        <!-- ========================================
             SEÇÃO 2 — HISTÓRICO E PROBLEMAS
        ======================================== -->
        <div style="margin:24px 0 16px;
                    padding-bottom:8px;
                    border-bottom:2px solid var(--cor-primaria);
                    color:var(--cor-primaria);
                    font-weight:700; font-size:15px">
            📋 Histórico e Problemas
        </div>

        <div class="form-grupo">
            <label for="historico_quimico">
                Histórico Químico
            </label>
            <textarea
                id="historico_quimico"
                name="historico_quimico"
                rows="3"
                placeholder="Ex: Coloração há 3 meses,
progressiva há 1 ano..."
            ><?= $v('historico_quimico') ?></textarea>
        </div>

        <div class="form-grupo">
            <label for="problemas">
                Problemas Identificados
            </label>
            <textarea
                id="problemas"
                name="problemas"
                rows="3"
                placeholder="Ex: Ressecamento, frizz,
quebra nas pontas..."
            ><?= $v('problemas') ?></textarea>
        </div>

        <!-- ========================================
             SEÇÃO 3 — TRATAMENTO RECOMENDADO
        ======================================== -->
        <div style="margin:24px 0 16px;
                    padding-bottom:8px;
                    border-bottom:2px solid var(--cor-primaria);
                    color:var(--cor-primaria);
                    font-weight:700; font-size:15px">
            💊 Tratamento Recomendado
        </div>

        <div class="form-grupo">
            <label for="tratamento_recomendado">
                Tratamento Recomendado
            </label>
            <textarea
                id="tratamento_recomendado"
                name="tratamento_recomendado"
                rows="4"
                placeholder="Ex: Cronograma capilar mensal
com foco em hidratação e nutrição.
Evitar calor excessivo..."
            ><?= $v('tratamento_recomendado') ?></textarea>
        </div>

        <div class="form-acoes">
            <button type="submit"
                    class="btn btn-primario"
                    style="width:auto">
                💾 Salvar Diagnóstico
            </button>
            <a href="<?= BASE_URL ?>
                /index.php?controller=diagnostico"
               class="btn btn-secundario">
                ← Cancelar
            </a>
        </div>

    </form>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>