<?php
/** @var array      $atendimento             */
/** @var array      $agendamentosDisponiveis */
/** @var array|null $agendamentoSelecionado  */
/** @var array      $erros                   */
/** @var int        $agendamentoId           */
$atendimento             = $atendimento             ?? [];
$agendamentosDisponiveis = $agendamentosDisponiveis ?? [];
$agendamentoSelecionado  = $agendamentoSelecionado  ?? null;
$erros                   = $erros                   ?? [];
$agendamentoId           = $agendamentoId           ?? 0;

$v = function(string $campo) use ($atendimento): string {
    return htmlspecialchars($atendimento[$campo] ?? '');
};

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="form-card">

    <h3>
        <?= !empty($atendimento['id'])
            ? '✏️ Editar Atendimento'
            : '💆 Registrar Atendimento' ?>
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

    <!-- Info do agendamento selecionado -->
    <?php if ($agendamentoSelecionado): ?>
    <div class="alerta alerta-aviso" style="margin-bottom:20px">
        📅 Agendamento selecionado:
        <strong>
            <?= htmlspecialchars(
                $agendamentoSelecionado['cliente_nome']) ?>
        </strong>
        — <?= htmlspecialchars(
            $agendamentoSelecionado['servico']) ?>
        — <?= date('d/m/Y H:i', strtotime(
            $agendamentoSelecionado['data_hora'])) ?>
    </div>
    <?php endif; ?>

    <form method="POST"
          action="<?= BASE_URL ?>
            /index.php?controller=atendimento&action=salvar">

        <input type="hidden" name="id" value="<?= $v('id') ?>">

        <!-- Agendamento -->
        <?php if (empty($atendimento['id'])): ?>
        <div class="form-grupo">
            <label for="agendamento_id">
                Agendamento *
            </label>
            <select id="agendamento_id"
                    name="agendamento_id" required>
                <option value="">
                    Selecione o agendamento...
                </option>
                <?php foreach (
                    $agendamentosDisponiveis as $ag): ?>
                <option value="<?= $ag['id'] ?>"
                    <?= (int)$ag['id'] === $agendamentoId
                        ? 'selected' : '' ?>>
                    <?= date('d/m/Y H:i',
                        strtotime($ag['data_hora'])) ?> —
                    <?= htmlspecialchars($ag['cliente_nome']) ?> —
                    <?= htmlspecialchars($ag['servico']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php if (empty($agendamentosDisponiveis)): ?>
            <small style="color:var(--cor-aviso)">
                ⚠️ Nenhum agendamento disponível para
                atendimento no momento.
            </small>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <!-- Modo edição — mostra o agendamento fixo -->
        <input type="hidden"
               name="agendamento_id"
               value="<?= $v('agendamento_id') ?>">
        <div class="form-grupo">
            <label>Agendamento</label>
            <div style="padding:10px 14px;
                        background:var(--cor-fundo);
                        border-radius:var(--raio-borda);
                        border:1px solid var(--cor-borda);
                        font-size:14px">
                <?= htmlspecialchars(
                    $atendimento['cliente_nome']
                    ?? '') ?> —
                <?= htmlspecialchars(
                    $atendimento['servico'] ?? '') ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Produtos e Valor -->
        <div class="form-linha">
            <div class="form-grupo">
                <label for="produtos_utilizados">
                    Produtos Utilizados
                </label>
                <textarea
                    id="produtos_utilizados"
                    name="produtos_utilizados"
                    rows="3"
                    placeholder="Ex: Máscara Lola, Óleo de Argan..."
                ><?= $v('produtos_utilizados') ?></textarea>
            </div>

            <div class="form-grupo">
                <label for="valor">
                    Valor Cobrado (R$)
                </label>
                <input
                    type="number"
                    id="valor"
                    name="valor"
                    value="<?= $v('valor') ?>"
                    placeholder="0.00"
                    step="0.01"
                    min="0"
                >
            </div>
        </div>

        <!-- Observações -->
        <div class="form-grupo">
            <label for="observacoes">Observações</label>
            <textarea
                id="observacoes"
                name="observacoes"
                rows="3"
                placeholder="Anotações sobre o atendimento..."
            ><?= $v('observacoes') ?></textarea>
        </div>

        <div class="form-acoes">
            <button type="submit"
                    class="btn btn-primario"
                    style="width:auto">
                💾 Salvar Atendimento
            </button>
            <a href="<?= BASE_URL ?>
                /index.php?controller=atendimento"
               class="btn btn-secundario">
                ← Cancelar
            </a>
        </div>

    </form>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>