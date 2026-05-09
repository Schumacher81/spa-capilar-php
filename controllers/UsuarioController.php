<?php

class UsuarioController extends Controller {

    private UsuarioModel $usuarioModel;

    public function __construct() {
        Session::requerLogin();
        Session::requerAdmin(); // só ADMIN acessa
        $this->usuarioModel = new UsuarioModel();
    }

    // ================================================
    // ACTION: index — Lista usuários
    // ================================================
    public function index(): void {
        $usuarios = $this->usuarioModel->listarAtivos();

        $this->view('usuarios/index', [
            'tituloPagina' => 'Usuários do Sistema',
            'usuarios'     => $usuarios,
        ]);
    }

    // ================================================
    // ACTION: form — Formulário novo/editar
    // ================================================
    public function form(): void {
        $id      = (int) $this->get('id', 0);
        $usuario = [];

        if ($id > 0) {
            $usuario = $this->usuarioModel->buscarPorId($id);
            if (!$usuario) {
                $this->redirecionarComMensagem(
                    'usuario', 'index',
                    'erro', 'Usuário não encontrado!'
                );
                return;
            }
        }

        $this->view('usuarios/form', [
            'tituloPagina' => $id > 0
                ? 'Editar Usuário' : 'Novo Usuário',
            'usuario'      => $usuario,
            'erros'        => [],
        ]);
    }

    // ================================================
    // ACTION: salvar
    // ================================================
    public function salvar(): void {

        if (!$this->isPost()) {
            $this->redirecionar('usuario');
            return;
        }

        $id    = (int) $this->post('id');
        $dados = [
            'nome'   => $this->post('nome'),
            'login'  => $this->post('login'),
            'senha'  => $this->post('senha'),
            'perfil' => $this->post('perfil'),
        ];

        $erros = [];
        if (empty($dados['nome']))  $erros[] = 'Nome é obrigatório!';
        if (empty($dados['login'])) $erros[] = 'Login é obrigatório!';
        if ($id === 0 && empty($dados['senha'])) {
            $erros[] = 'Senha é obrigatória!';
        }

        if ($this->usuarioModel->loginExiste(
                $dados['login'], $id)) {
            $erros[] = 'Este login já está em uso!';
        }

        if (!empty($erros)) {
            $this->view('usuarios/form', [
                'tituloPagina' => $id > 0
                    ? 'Editar Usuário' : 'Novo Usuário',
                'usuario'      =>
                    array_merge(['id' => $id], $dados),
                'erros'        => $erros,
            ]);
            return;
        }

        if ($id > 0) {
            $ok  = $this->usuarioModel->atualizar($id, $dados);
            $msg = $ok
                ? 'Usuário atualizado!'
                : 'Erro ao atualizar.';
        } else {
            $ok  = $this->usuarioModel->salvar($dados);
            $msg = $ok
                ? 'Usuário criado com sucesso!'
                : 'Erro ao criar usuário.';
        }

        $this->redirecionarComMensagem(
            'usuario', 'index',
            $ok ? 'sucesso' : 'erro', $msg
        );
    }

    // ================================================
    // ACTION: inativar
    // ================================================
    public function inativar(): void {
        $id = (int) $this->get('id', 0);

        // Não pode inativar a si mesmo
        $usuarioLogado = Session::getUsuario();
        if ($id === (int)$usuarioLogado['id']) {
            $this->redirecionarComMensagem(
                'usuario', 'index',
                'erro', 'Você não pode inativar seu próprio usuário!'
            );
            return;
        }

        $ok = $this->usuarioModel->inativar($id);
        $this->redirecionarComMensagem(
            'usuario', 'index',
            $ok ? 'sucesso' : 'erro',
            $ok ? 'Usuário inativado!' : 'Erro ao inativar.'
        );
    }
}