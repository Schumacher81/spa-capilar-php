<?php

/**
 * DiagnosticoController — CRUD de Diagnósticos Capilares
 */
class DiagnosticoController extends Controller {

    private DiagnosticoModel  $diagnosticoModel;
    private AtendimentoModel  $atendimentoModel;

    public function __construct() {
        Session::requerLogin();
        $this->diagnosticoModel = new DiagnosticoModel();
        $this->atendimentoModel = new AtendimentoModel();
    }

    // ================================================
    // ACTION: index — Lista diagnósticos
    // ================================================
    public function index(): void {

        $diagnosticos = $this->diagnosticoModel->listarTodos();

        $this->view('diagnosticos/index', [
            'tituloPagina' => 'Diagnósticos Capilares',
            'diagnosticos' => $diagnosticos,
        ]);
    }

    // ================================================
    // ACTION: form — Formulário novo/editar
    // ================================================
    public function form(): void {

        $id            = (int) $this->get('id', 0);
        $atendimentoId = (int) $this->get('atendimento_id', 0);
        $diagnostico   = [];

        // Modo edição
        if ($id > 0) {
            $diagnostico = $this->diagnosticoModel
                ->buscarCompleto($id);
            if (!$diagnostico) {
                $this->redirecionarComMensagem(
                    'diagnostico', 'index',
                    'erro', 'Diagnóstico não encontrado!'
                );
                return;
            }
        }

        // Atendimentos disponíveis para o dropdown
        $atendimentosDisponiveis = $this->diagnosticoModel
            ->buscarAtendimentosSemDiagnostico();

        // Atendimento pré-selecionado (vindo dos detalhes)
        $atendimentoSelecionado = null;
        if ($atendimentoId > 0) {
            $atendimentoSelecionado = $this->atendimentoModel
                ->buscarCompleto($atendimentoId);
        }

        $this->view('diagnosticos/form', [
            'tituloPagina'           => $id > 0
                ? 'Editar Diagnóstico'
                : 'Novo Diagnóstico',
            'diagnostico'            => $diagnostico,
            'atendimentosDisponiveis'=> $atendimentosDisponiveis,
            'atendimentoSelecionado' => $atendimentoSelecionado,
            'atendimentoId'          => $atendimentoId,
            'erros'                  => [],
        ]);
    }

    // ================================================
    // ACTION: salvar — Processa o formulário
    // ================================================
    public function salvar(): void {

        if (!$this->isPost()) {
            $this->redirecionar('diagnostico');
            return;
        }

        $id    = (int) $this->post('id');
        $dados = [
            'atendimento_id'         =>
                (int) $this->post('atendimento_id'),
            'tipo_cabelo'            =>
                $this->post('tipo_cabelo'),
            'porosidade'             =>
                $this->post('porosidade'),
            'oleosidade'             =>
                $this->post('oleosidade'),
            'historico_quimico'      =>
                $this->post('historico_quimico'),
            'problemas'              =>
                $this->post('problemas'),
            'tratamento_recomendado' =>
                $this->post('tratamento_recomendado'),
        ];

        // Validações
        $erros = $this->validar($dados, $id);

        if (!empty($erros)) {
            $atendimentosDisponiveis = $this->diagnosticoModel
                ->buscarAtendimentosSemDiagnostico();

            $this->view('diagnosticos/form', [
                'tituloPagina'           => $id > 0
                    ? 'Editar Diagnóstico'
                    : 'Novo Diagnóstico',
                'diagnostico'            =>
                    array_merge(['id' => $id], $dados),
                'atendimentosDisponiveis'=>
                    $atendimentosDisponiveis,
                'atendimentoSelecionado' => null,
                'atendimentoId'          => 0,
                'erros'                  => $erros,
            ]);
            return;
        }

        // Salva ou atualiza
        if ($id > 0) {
            $ok  = $this->diagnosticoModel
                ->atualizar($id, $dados);
            $msg = $ok
                ? 'Diagnóstico atualizado com sucesso!'
                : 'Erro ao atualizar diagnóstico.';
        } else {
            $ok  = $this->diagnosticoModel->salvar($dados);
            $msg = $ok
                ? 'Diagnóstico registrado com sucesso!'
                : 'Erro ao registrar diagnóstico.';
        }

        $this->redirecionarComMensagem(
            'diagnostico', 'index',
            $ok ? 'sucesso' : 'erro', $msg
        );
    }

    // ================================================
    // ACTION: detalhes — Exibe detalhes completos
    // ================================================
    public function detalhes(): void {

        $id          = (int) $this->get('id', 0);
        $diagnostico = $this->diagnosticoModel
            ->buscarCompleto($id);

        if (!$diagnostico) {
            $this->redirecionarComMensagem(
                'diagnostico', 'index',
                'erro', 'Diagnóstico não encontrado!'
            );
            return;
        }

        // Busca evolução do cliente (todos os diagnósticos)
        $evolucao = $this->diagnosticoModel
            ->buscarPorCliente($diagnostico['cliente_id']);

        $this->view('diagnosticos/detalhes', [
            'tituloPagina' => 'Diagnóstico Capilar',
            'diagnostico'  => $diagnostico,
            'evolucao'     => $evolucao,
        ]);
    }

    // ================================================
    // ACTION: porAtendimento
    // Busca diagnóstico pelo ID do atendimento
    // ================================================
    public function porAtendimento(): void {

        $atendimentoId = (int) $this->get('id', 0);
        $diagnostico   = $this->diagnosticoModel
            ->buscarPorAtendimento($atendimentoId);

        if (!$diagnostico) {
            $this->redirecionarComMensagem(
                'diagnostico', 'index',
                'erro',
                'Nenhum diagnóstico para este atendimento!'
            );
            return;
        }

        $this->redirecionar(
            'diagnostico',
            'detalhes&id=' . $diagnostico['id']
        );
    }

    // ================================================
    // VALIDAÇÕES
    // ================================================
    private function validar(array $dados, int $id): array {
        $erros = [];

        if (empty($dados['atendimento_id'])) {
            $erros[] = 'Atendimento é obrigatório!';
        } elseif ($id === 0) {
            if ($this->diagnosticoModel->existePorAtendimento(
                    $dados['atendimento_id'])) {
                $erros[] = 'Já existe diagnóstico '
                         . 'para este atendimento!';
            }
        }

        return $erros;
    }
}