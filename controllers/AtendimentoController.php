<?php

/**
 * AtendimentoController — Registro de Atendimentos
 */
class AtendimentoController extends Controller {

    private AtendimentoModel $atendimentoModel;
    private AgendamentoModel $agendamentoModel;

    public function __construct() {
        Session::requerLogin();
        $this->atendimentoModel = new AtendimentoModel();
        $this->agendamentoModel = new AgendamentoModel();
    }

    // ================================================
    // ACTION: index — Lista atendimentos
    // ================================================
    public function index(): void {

        $atendimentos = $this->atendimentoModel->listarTodos();

        $this->view('atendimentos/index', [
            'tituloPagina' => 'Atendimentos',
            'atendimentos' => $atendimentos,
        ]);
    }

    // ================================================
    // ACTION: form — Formulário de registro
    // ================================================
    public function form(): void {

        $id            = (int) $this->get('id', 0);
        $agendamentoId = (int) $this->get('agendamento_id', 0);
        $atendimento   = [];

        // Modo edição
        if ($id > 0) {
            $atendimento = $this->atendimentoModel
                ->buscarCompleto($id);
            if (!$atendimento) {
                $this->redirecionarComMensagem(
                    'atendimento', 'index',
                    'erro', 'Atendimento não encontrado!'
                );
                return;
            }
        }

        // Busca agendamentos disponíveis para o dropdown
        $agendamentosDisponiveis = $this->atendimentoModel
            ->buscarAgendamentosDisponiveis();

        // Se veio de um agendamento específico,
        // busca os dados dele para pré-selecionar
        $agendamentoSelecionado = null;
        if ($agendamentoId > 0) {
            $agendamentoSelecionado = $this->agendamentoModel
                ->buscarCompleto($agendamentoId);
        }

        $this->view('atendimentos/form', [
            'tituloPagina'           => $id > 0
                ? 'Editar Atendimento'
                : 'Registrar Atendimento',
            'atendimento'            => $atendimento,
            'agendamentosDisponiveis'=> $agendamentosDisponiveis,
            'agendamentoSelecionado' => $agendamentoSelecionado,
            'agendamentoId'          => $agendamentoId,
        ]);
    }

    // ================================================
    // ACTION: salvar — Processa o formulário
    // ================================================
    public function salvar(): void {

        if (!$this->isPost()) {
            $this->redirecionar('atendimento');
            return;
        }

        $id    = (int) $this->post('id');
        $dados = [
            'agendamento_id'       =>
                (int) $this->post('agendamento_id'),
            'produtos_utilizados'  =>
                $this->post('produtos_utilizados'),
            'observacoes'          =>
                $this->post('observacoes'),
            'valor'                =>
                $this->post('valor'),
        ];

        // Validações
        $erros = $this->validar($dados, $id);

        if (!empty($erros)) {
            $agendamentosDisponiveis = $this->atendimentoModel
                ->buscarAgendamentosDisponiveis();

            $this->view('atendimentos/form', [
                'tituloPagina'           => $id > 0
                    ? 'Editar Atendimento'
                    : 'Registrar Atendimento',
                'atendimento'            =>
                    array_merge(['id' => $id], $dados),
                'agendamentosDisponiveis'=>
                    $agendamentosDisponiveis,
                'agendamentoSelecionado' => null,
                'agendamentoId'          => 0,
                'erros'                  => $erros,
            ]);
            return;
        }

        // Salva ou atualiza
        if ($id > 0) {
            $ok  = $this->atendimentoModel
                ->atualizar($id, $dados);
            $msg = $ok
                ? 'Atendimento atualizado com sucesso!'
                : 'Erro ao atualizar atendimento.';
        } else {
            $ok  = $this->atendimentoModel->salvar($dados);
            $msg = $ok
                ? 'Atendimento registrado com sucesso!'
                : 'Erro ao registrar atendimento.';
        }

        $this->redirecionarComMensagem(
            'atendimento', 'index',
            $ok ? 'sucesso' : 'erro', $msg
        );
    }

    // ================================================
    // ACTION: detalhes
    // ================================================
    public function detalhes(): void {

        $id          = (int) $this->get('id', 0);
        $atendimento = $this->atendimentoModel
            ->buscarCompleto($id);

        if (!$atendimento) {
            $this->redirecionarComMensagem(
                'atendimento', 'index',
                'erro', 'Atendimento não encontrado!'
            );
            return;
        }

        $temDiagnostico = $this->atendimentoModel
            ->temDiagnostico($id);

        $this->view('atendimentos/detalhes', [
            'tituloPagina'  => 'Detalhes do Atendimento',
            'atendimento'   => $atendimento,
            'temDiagnostico'=> $temDiagnostico,
        ]);
    }

    // ================================================
    // VALIDAÇÕES
    // ================================================
    private function validar(array $dados, int $id): array {
        $erros = [];

        if (empty($dados['agendamento_id'])) {
            $erros[] = 'Agendamento é obrigatório!';
        } elseif ($id === 0) {
            // Novo atendimento — verifica duplicata
            if ($this->atendimentoModel->existePorAgendamento(
                    $dados['agendamento_id'])) {
                $erros[] = 'Já existe atendimento '
                         . 'para este agendamento!';
            }
        }

        if (!empty($dados['valor'])
                && !is_numeric($dados['valor'])) {
            $erros[] = 'Valor inválido!';
        }

        if (!empty($dados['valor'])
                && (float)$dados['valor'] < 0) {
            $erros[] = 'Valor não pode ser negativo!';
        }

        return $erros;
    }
}