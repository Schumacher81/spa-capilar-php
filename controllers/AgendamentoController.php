<?php

/**
 * AgendamentoController — CRUD de Agendamentos
 */
class AgendamentoController extends Controller {

    private AgendamentoModel $agendamentoModel;
    private ClienteModel     $clienteModel;
    private UsuarioModel     $usuarioModel;

    public function __construct() {
        Session::requerLogin();
        $this->agendamentoModel = new AgendamentoModel();
        $this->clienteModel     = new ClienteModel();
        $this->usuarioModel     = new UsuarioModel();
    }

    // ================================================
    // ACTION: index — Lista agendamentos
    // ================================================
    public function index(): void {

        // Filtros disponíveis
        $filtroStatus = $this->get('status', '');
        $filtroDe     = $this->get('de', '');
        $filtroAte    = $this->get('ate', '');

        // Aplica filtros
        if (!empty($filtroDe) && !empty($filtroAte)) {
            $agendamentos = $this->agendamentoModel
                ->listarPorPeriodo($filtroDe, $filtroAte);
        } elseif (!empty($filtroStatus)) {
            $agendamentos = $this->agendamentoModel
                ->listarPorStatus($filtroStatus);
        } else {
            $agendamentos = $this->agendamentoModel
                ->listarTodos();
        }

        $this->view('agendamentos/index', [
            'tituloPagina'  => 'Agendamentos',
            'agendamentos'  => $agendamentos,
            'filtroStatus'  => $filtroStatus,
            'filtroDe'      => $filtroDe,
            'filtroAte'     => $filtroAte,
        ]);
    }

    // ================================================
    // ACTION: form — Formulário novo/editar
    // ================================================
    public function form(): void {

        $id          = (int) $this->get('id', 0);
        $clienteId   = (int) $this->get('cliente_id', 0);
        $profissionalId = (int) $this->get('profissional_id', 0);
        $dataHora       = $this->get('data_hora', '');
        $agendamento = [];

        if ($id > 0) {
            $agendamento = $this->agendamentoModel
                ->buscarCompleto($id);
            if (!$agendamento) {
                $this->redirecionarComMensagem(
                    'agendamento', 'index',
                    'erro', 'Agendamento não encontrado!'
                );
                return;
            }
        }

        // Listas para os dropdowns
        $clientes      = $this->clienteModel->listarAtivos();
        $profissionais = $this->usuarioModel->listarProfissionais();

        $this->view('agendamentos/form', [
            'tituloPagina'  => $id > 0
                ? 'Editar Agendamento' : 'Novo Agendamento',
            'agendamento'   => $agendamento,
            'clientes'      => $clientes,
            'profissionais' => $profissionais,
            'clienteId'     => $clienteId,
            'profissionalId'=> $profissionalId,
            'dataHora'      => $dataHora,
            'erros'         => [],
        ]);
    }

    // ================================================
    // ACTION: salvar — Processa o formulário
    // ================================================
    public function salvar(): void {

        if (!$this->isPost()) {
            $this->redirecionar('agendamento');
            return;
        }

        $id    = (int) $this->post('id');
        $dados = [
            'cliente_id'      => (int) $this->post('cliente_id'),
            'profissional_id' => (int) $this->post('profissional_id'),
            'data_hora'       => $this->post('data_hora'),
            'servico'         => $this->post('servico'),
            'observacoes'     => $this->post('observacoes'),
        ];

        // Validações
        $erros = $this->validar($dados);

        // Verifica conflito de horário
        if (empty($erros)) {
            if ($this->agendamentoModel->existeConflito(
                    $dados['profissional_id'],
                    $dados['data_hora'],
                    $id)) {
                $erros[] = 'Este profissional já possui '
                         . 'agendamento neste horário!';
            }
        }

        if (!empty($erros)) {
            $clientes      = $this->clienteModel->listarAtivos();
            $profissionais = $this->usuarioModel
                ->listarProfissionais();

            $this->view('agendamentos/form', [
                'tituloPagina'  => $id > 0
                    ? 'Editar Agendamento' : 'Novo Agendamento',
                'agendamento'   => array_merge(
                    ['id' => $id], $dados
                ),
                'clientes'      => $clientes,
                'profissionais' => $profissionais,
                'clienteId'     => 0,
                'erros'         => $erros,
            ]);
            return;
        }

        // Salva ou atualiza
        if ($id > 0) {
            $ok  = $this->agendamentoModel->atualizar($id, $dados);
            $msg = $ok
                ? 'Agendamento atualizado com sucesso!'
                : 'Erro ao atualizar agendamento.';
        } else {
            $ok  = $this->agendamentoModel->salvar($dados);
            $msg = $ok
                ? 'Agendamento criado com sucesso!'
                : 'Erro ao criar agendamento.';
        }

        $this->redirecionarComMensagem(
            'agendamento', 'index',
            $ok ? 'sucesso' : 'erro', $msg
        );
    }

    // ================================================
    // ACTION: detalhes — Exibe detalhes
    // ================================================
    public function detalhes(): void {

        $id          = (int) $this->get('id', 0);
        $agendamento = $this->agendamentoModel
            ->buscarCompleto($id);

        if (!$agendamento) {
            $this->redirecionarComMensagem(
                'agendamento', 'index',
                'erro', 'Agendamento não encontrado!'
            );
            return;
        }

        $temAtendimento = $this->agendamentoModel
            ->temAtendimento($id);

        $this->view('agendamentos/detalhes', [
            'tituloPagina'  => 'Detalhes do Agendamento',
            'agendamento'   => $agendamento,
            'temAtendimento'=> $temAtendimento,
        ]);
    }

    // ================================================
    // ACTION: atualizarStatus
    // ================================================
    public function atualizarStatus(): void {

        $id     = (int) $this->get('id', 0);
        $status = $this->get('status', '');

        $statusValidos = ['AGENDADO', 'REALIZADO', 'CANCELADO'];

        if (!in_array($status, $statusValidos) || $id <= 0) {
            $this->redirecionarComMensagem(
                'agendamento', 'index',
                'erro', 'Operação inválida!'
            );
            return;
        }

        // Busca agendamento atual
        $agendamento = $this->agendamentoModel
            ->buscarCompleto($id);

        // Regra: não pode reabrir cancelado
        if ($agendamento['status'] === 'CANCELADO'
                && $status === 'AGENDADO') {
            $this->redirecionarComMensagem(
                'agendamento', 'index',
                'erro',
                'Não é possível reabrir um agendamento cancelado!'
            );
            return;
        }

        $ok = $this->agendamentoModel
            ->atualizarStatus($id, $status);

        $this->redirecionarComMensagem(
            'agendamento', 'index',
            $ok ? 'sucesso' : 'erro',
            $ok ? 'Status atualizado com sucesso!'
                : 'Erro ao atualizar status.'
        );
    }

    // ================================================
    // VALIDAÇÕES
    // ================================================
    private function validar(array $dados): array {
        $erros = [];

        if (empty($dados['cliente_id'])) {
            $erros[] = 'Cliente é obrigatório!';
        }
        if (empty($dados['profissional_id'])) {
            $erros[] = 'Profissional é obrigatório!';
        }
        if (empty($dados['data_hora'])) {
            $erros[] = 'Data e hora são obrigatórios!';
        } elseif (strtotime($dados['data_hora']) < time()) {
            $erros[] = 'A data do agendamento deve ser no futuro!';
        }
        if (empty($dados['servico'])) {
            $erros[] = 'Serviço é obrigatório!';
        }

        return $erros;
    }
    // ================================================
    // ACTION: detalhesJson — retorna JSON para o popup
    // ================================================
    public function detalhesJson(): void {

        $id          = (int) $this->get('id', 0);
        $agendamento = $this->agendamentoModel
            ->buscarCompleto($id);

        if (!$agendamento) {
            $this->json(['erro' => 'Não encontrado'], 404);
            return;
        }

        $this->json([
            'id'               => $agendamento['id'],
            'data'             => date('d/m/Y', strtotime(
                                    $agendamento['data_hora'])),
            'hora'             => date('H:i', strtotime(
                                    $agendamento['data_hora'])),
            'servico'          => $agendamento['servico'],
            'status'           => $agendamento['status'],
            'observacoes'      => $agendamento['observacoes'],
            'cliente_nome'     => $agendamento['cliente_nome'],
            'cliente_telefone' => $agendamento['cliente_telefone'],
            'profissional_nome'=> $agendamento['profissional_nome'],
        ]); 
    }
}