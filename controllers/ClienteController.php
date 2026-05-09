<?php

/**
 * ClienteController — CRUD de Clientes
 */
class ClienteController extends Controller {

    private ClienteModel $clienteModel;

    public function __construct() {
        Session::requerLogin();
        $this->clienteModel = new ClienteModel();
    }

    // ================================================
    // ACTION: index — Lista todos os clientes
    // URL: index.php?controller=cliente
    // ================================================
    public function index(): void {

        // Verifica se há busca
        $busca    = $this->get('busca', '');
        $clientes = [];

        if (!empty($busca)) {
            $clientes = $this->clienteModel->buscar($busca);
        } else {
            $clientes = $this->clienteModel->listarAtivos();
        }

        $this->view('clientes/index', [
            'tituloPagina' => 'Clientes',
            'clientes'     => $clientes,
            'busca'        => $busca,
        ]);
    }

    // ================================================
    // ACTION: form — Exibe formulário (novo ou editar)
    // URL: index.php?controller=cliente&action=form
    // URL: index.php?controller=cliente&action=form&id=1
    // ================================================
    public function form(): void {

        $id      = (int) $this->get('id', 0);
        $cliente = [];

        // Se tem ID, busca os dados para edição
        if ($id > 0) {
            $cliente = $this->clienteModel->buscarPorId($id);
            if (!$cliente) {
                $this->redirecionarComMensagem(
                    'cliente', 'index',
                    'erro', 'Cliente não encontrado!'
                );
                return;
            }
        }

        $this->view('clientes/form', [
            'tituloPagina' => $id > 0 ? 'Editar Cliente' : 'Novo Cliente',
            'cliente'      => $cliente,
        ]);
    }

    // ================================================
    // ACTION: salvar — Processa o formulário
    // URL: index.php?controller=cliente&action=salvar
    // ================================================
    public function salvar(): void {

        // Só aceita POST
        if (!$this->isPost()) {
            $this->redirecionar('cliente');
            return;
        }

        $id   = (int) $this->post('id');
        $dados = [
            'nome'            => $this->post('nome'),
            'telefone'        => $this->post('telefone'),
            'email'           => $this->post('email'),
            'data_nascimento' => $this->post('data_nascimento'),
            'observacoes'     => $this->post('observacoes'),
        ];

        // ---- VALIDAÇÕES ----
        $erros = $this->validar($dados);

        // Verifica telefone duplicado
        if ($this->clienteModel->telefoneExiste(
                $dados['telefone'], $id)) {
            $erros[] = 'Este telefone já está cadastrado!';
        }

        // Se tem erros, volta ao formulário
        if (!empty($erros)) {
            $this->view('clientes/form', [
                'tituloPagina' => $id > 0
                    ? 'Editar Cliente' : 'Novo Cliente',
                'cliente'      => array_merge(['id' => $id], $dados),
                'erros'        => $erros,
            ]);
            return;
        }

        // ---- SALVAR OU ATUALIZAR ----
        if ($id > 0) {
            $ok = $this->clienteModel->atualizar($id, $dados);
            $msg = $ok
                ? 'Cliente atualizado com sucesso!'
                : 'Erro ao atualizar cliente.';
        } else {
            $ok = $this->clienteModel->salvar($dados);
            $msg = $ok
                ? 'Cliente cadastrado com sucesso!'
                : 'Erro ao cadastrar cliente.';
        }

        $this->redirecionarComMensagem(
            'cliente', 'index',
            $ok ? 'sucesso' : 'erro',
            $msg
        );
    }

    // ================================================
    // ACTION: detalhes — Exibe detalhes + histórico
    // URL: index.php?controller=cliente&action=detalhes&id=1
    // ================================================
    public function detalhes(): void {

        $id      = (int) $this->get('id', 0);
        $cliente = $this->clienteModel->buscarPorId($id);

        if (!$cliente) {
            $this->redirecionarComMensagem(
                'cliente', 'index',
                'erro', 'Cliente não encontrado!'
            );
            return;
        }

        $historico = $this->clienteModel->buscarHistorico($id);

        $this->view('clientes/detalhes', [
            'tituloPagina' => 'Detalhes do Cliente',
            'cliente'      => $cliente,
            'historico'    => $historico,
        ]);
    }

    // ================================================
    // ACTION: inativar — Inativa um cliente
    // URL: index.php?controller=cliente&action=inativar&id=1
    // ================================================
    public function inativar(): void {

        // Apenas ADMIN pode inativar
        Session::requerAdmin();

        $id = (int) $this->get('id', 0);

        if ($id <= 0) {
            $this->redirecionarComMensagem(
                'cliente', 'index',
                'erro', 'ID inválido!'
            );
            return;
        }

        $ok = $this->clienteModel->inativar($id);

        $this->redirecionarComMensagem(
            'cliente', 'index',
            $ok ? 'sucesso' : 'erro',
            $ok ? 'Cliente inativado com sucesso!'
                : 'Erro ao inativar cliente.'
        );
    }

    // ================================================
    // VALIDAÇÕES PRIVADAS
    // ================================================
    private function validar(array $dados): array {
        $erros = [];

        if (empty($dados['nome'])) {
            $erros[] = 'Nome é obrigatório!';
        } elseif (strlen($dados['nome']) < 2) {
            $erros[] = 'Nome deve ter pelo menos 2 caracteres!';
        }

        if (empty($dados['telefone'])) {
            $erros[] = 'Telefone é obrigatório!';
        }

        if (!empty($dados['email'])
            && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'E-mail inválido!';
        }

        return $erros;
    }
}