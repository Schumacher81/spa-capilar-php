<?php
/** @var array  $agendamentos */
/** @var string $filtroStatus */
/** @var string $filtroDe     */
/** @var string $filtroAte    */
$agendamentos = $agendamentos ?? [];
$filtroStatus = $filtroStatus ?? '';
$filtroDe     = $filtroDe     ?? '';
$filtroAte    = $filtroAte    ?? '';

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="tabela-container">

    <div class="tabela-header">
        <h3>📅 Agendamentos</h3>
        <a href="<?= BASE_URL ?>
            /index.php?controller=agendamento&action=form"
           class="btn btn-sucesso btn-sm">
            + Novo Agendamento
        </a>
    </div>

    <!-- Filtros -->
    <div style="padding: 16px 24px;
                border-bottom: 1px solid var(--cor-borda);
                background: var(--cor-fundo)">
        <form method="GET"
              action="<?= BASE_URL ?>/index.php"
              style="display:flex; gap:10px; flex-wrap:wrap;
                     align-items:flex-end">
            <input type="hidden"
                   name="controller" value="agendamento">

            <!-- Filtro por status -->
            <div>
                <label style="font-size:12px; font-weight:600;
                              display:block; margin-bottom:4px">
                    Status
                </label>
                <select name="status"
                        style="padding:8px 12px;
                               border:1px solid var(--cor-borda);
                               border-radius:var(--raio-borda);
                               font-size:14px">
                    <option value="">Todos</option>
                    <option value="AGENDADO"
                        <?= $filtroStatus==='AGENDADO'
                            ?'selected':'' ?>>
                        Agendado
                    </option>
                    <option value="REALIZADO"
                        <?= $filtroStatus==='REALIZADO'
                            ?'selected':'' ?>>
                        Realizado
                    </option>
                    <option value="CANCELADO"
                        <?= $filtroStatus==='CANCELADO'
                            ?'selected':'' ?>>
                        Cancelado
                    </option>
                </select>
            </div>

            <!-- Filtro por período -->
            <div>
                <label style="font-size:12px; font-weight:600;
                              display:block; margin-bottom:4px">
                    De
                </label>
                <input type="date" name="de"
                       value="<?= $filtroDe ?>"
                       style="padding:8px 12px;
                              border:1px solid var(--cor-borda);
                              border-radius:var(--raio-borda);
                              font-size:14px">
            </div>
            <div>
                <label style="font-size:12px; font-weight:600;
                              display:block; margin-bottom:4px">
                    Até
                </label>
                <input type="date" name="ate"
                       value="<?= $filtroAte ?>"
                       style="padding:8px 12px;
                              border:1px solid var(--cor-borda);
                              border-radius:var(--raio-borda);
                              font-size:14px">
            </div>

            <button type="submit"
                    class="btn btn-primario btn-sm"
                    style="width:auto">
                🔍 Filtrar
            </button>
            <a href="<?= BASE_URL ?>
                /index.php?controller=agendamento"
               class="btn btn-secundario btn-sm">
                Limpar
            </a>
        </form>
    </div>

    <!-- Tabela -->
    <?php if (empty($agendamentos)): ?>
    <div style="padding:40px; text-align:center;
                color:var(--cor-texto-claro)">
        Nenhum agendamento encontrado.
    </div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Data/Hora</th>
                <th>Cliente</th>
                <th>Serviço</th>
                <th>Profissional</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($agendamentos as $ag): ?>
            <tr>
                <td style="color:var(--cor-texto-claro);
                           font-size:13px">
                    #<?= $ag['id'] ?>
                </td>
                <td>
                    <strong>
                        <?= date('d/m/Y',
                            strtotime($ag['data_hora'])) ?>
                    </strong>
                    <br>
                    <small style="color:var(--cor-texto-claro)">
                        <?= date('H:i',
                            strtotime($ag['data_hora'])) ?>
                    </small>
                </td>
                <td>
                    <?= htmlspecialchars($ag['cliente_nome']) ?>
                    <br>
                    <small style="color:var(--cor-texto-claro)">
                        <?= htmlspecialchars(
                            $ag['cliente_telefone']) ?>
                    </small>
                </td>
                <td>
                    <?= htmlspecialchars($ag['servico']) ?>
                </td>
                <td>
                    <?= htmlspecialchars(
                        $ag['profissional_nome']) ?>
                </td>
                <td>
                    <span class="badge badge-<?= 
                        strtolower($ag['status']) ?>">
                        <?= $ag['status'] ?>
                    </span>
                </td>
                <td>
                    <div style="display:flex; gap:5px">
                        <!-- Ver detalhes -->
                        <a href="<?= BASE_URL ?>
                            /index.php?controller=agendamento
                            &action=detalhes
                            &id=<?= $ag['id'] ?>"
                           class="btn btn-secundario btn-sm"
                           title="Detalhes">👁️</a>

                        <!-- Editar (só AGENDADO) -->
                        <?php if ($ag['status'] === 'AGENDADO'): ?>
                        <a href="<?= BASE_URL ?>
                            /index.php?controller=agendamento
                            &action=form
                            &id=<?= $ag['id'] ?>"
                           class="btn btn-aviso btn-sm"
                           title="Editar">✏️</a>

                        <!-- Cancelar -->
                        <a href="<?= BASE_URL ?>
                            /index.php?controller=agendamento
                            &action=atualizarStatus
                            &id=<?= $ag['id'] ?>
                            &status=CANCELADO"
                           class="btn btn-perigo btn-sm
                                  btn-excluir"
                           title="Cancelar">❌</a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="padding:12px 24px;
                border-top:1px solid var(--cor-borda);
                font-size:13px;
                color:var(--cor-texto-claro)">
        Total: <strong>
            <?= count($agendamentos) ?>
        </strong> agendamento(s)
    </div>
    <?php endif; ?>

</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>