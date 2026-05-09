<?php
/** @var array $agendamento   */
/** @var array $clientes      */
/** @var array $profissionais */
/** @var array $erros         */
/** @var int   $clienteId     */
$agendamento   = $agendamento   ?? [];
$clientes      = $clientes      ?? [];
$profissionais = $profissionais ?? [];
$erros         = $erros         ?? [];
$clienteId     = $clienteId     ?? 0;

$v = function(string $campo) use ($agendamento): string {
    return htmlspecialchars($agendamento[$campo] ?? '');
};

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="form-card">

    <h3>
        <?= !empty($agendamento['id'])
            ? '✏️ Editar Agendamento'
            : '📅 Novo Agendamento' ?>
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

    <form method="POST"
          action="<?= BASE_URL ?>
            /index.php?controller=agendamento&action=salvar">

        <input type="hidden" name="id" value="<?= $v('id') ?>">

        <!-- Cliente e Profissional -->
        <div class="form-linha">
            <div class="form-grupo">
                <label for="cliente_id">Cliente *</label>
                <select id="cliente_id"
                        name="cliente_id" required>
                    <option value="">
                        Selecione o cliente...
                    </option>
                    <?php foreach ($clientes as $c): ?>
                    <option value="<?= $c['id'] ?>"
                        <?= ((int)($agendamento['cliente_id']
                                ?? $clienteId))
                            === (int)$c['id']
                            ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nome']) ?> —
                        <?= htmlspecialchars($c['telefone']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-grupo">
                <label for="profissional_id">
                    Profissional *
                </label>
                <select id="profissional_id"
                        name="profissional_id" required>
                    <option value="">
                        Selecione o profissional...
                    </option>
                    <?php foreach ($profissionais as $p): ?>
                    <option value="<?= $p['id'] ?>"
                        <?= ((int)($agendamento['profissional_id'] ?? $profissionalId))
                            === (int)$p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nome']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Data/Hora e Serviço -->
        <div class="form-linha">
            <div class="form-grupo">
                <label for="data_hora">Data e Hora *</label>
                <input
                    type="datetime-local"
                    id="data_hora"
                    name="data_hora"
                    value="<?= !empty($agendamento['data_hora'])
                        ? date('Y-m-d\TH:i',
                            strtotime($agendamento['data_hora']))
                        : htmlspecialchars($dataHora ?? '') ?>"
                    min="<?= date('Y-m-d\TH:i') ?>"
                    required
                >
            </div>

            <div class="form-grupo">
                <label for="servico">Serviço *</label>
                <input
                    type="text"
                    id="servico"
                    name="servico"
                    value="<?= $v('servico') ?>"
                    placeholder="Ex: Hidratação Profunda"
                    list="servicos-lista"
                    required
                >
                <!-- Sugestões de serviços -->
                <datalist id="servicos-lista">
                    <option value="Hidratação Profunda">
                    <option value="Cronograma Capilar">
                    <option value="Corte e Escova">
                    <option value="Coloração">
                    <option value="Progressiva">
                    <option value="Botox Capilar">
                    <option value="Cauterização">
                    <option value="Luzes">
                    <option value="Mega Hair">
                </datalist>
            </div>
        </div>

        <!-- Observações -->
        <div class="form-grupo">
            <label for="observacoes">Observações</label>
            <textarea
                id="observacoes"
                name="observacoes"
                rows="3"
                placeholder="Informações adicionais..."
            ><?= $v('observacoes') ?></textarea>
        </div>

        <div class="form-acoes">
            <button type="submit"
                    class="btn btn-primario"
                    style="width:auto">
                💾 Salvar Agendamento
            </button>
            <a href="<?= BASE_URL ?>
                /index.php?controller=agendamento"
               class="btn btn-secundario">
                ← Cancelar
            </a>
        </div>

    </form>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>