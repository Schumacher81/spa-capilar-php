<?php

/**
 * AuthController — Controla autenticação
 * 
 * Responsável pelo login e logout do sistema.
 * Herda de Controller (classe base)
 */
class AuthController extends Controller {

    // Instância do model de usuário
    private UsuarioModel $usuarioModel;

    // ================================================
    // CONSTRUTOR
    // Instancia o model ao criar o controller
    // ================================================
    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
    }

    // ================================================
    // ACTION: login
    // GET  → exibe o formulário de login
    // POST → processa o login
    //
    // URL: index.php?controller=auth&action=login
    //      ou simplesmente index.php (padrão)
    // ================================================
    public function login(): void {

        // Se já está logado, vai direto pro dashboard
        if (Session::estaLogado()) {
            $this->redirecionar('dashboard');
            return;
        }

        // Se é POST, processa o formulário
        if ($this->isPost()) {
            $this->processarLogin();
            return;
        }

        // Se é GET, exibe o formulário
        $this->view('auth/login');
    }

    // ================================================
    // PROCESSAR LOGIN (método privado)
    // Chamado quando o formulário é enviado
    // ================================================
    private function processarLogin(): void {

        // Pega os dados do formulário
        $login = $this->post('login');
        $senha = $this->post('senha');

        // Validação básica
        if (empty($login) || empty($senha)) {
            Session::setFlash('erro',
                'Preencha o login e a senha!'
            );
            $this->view('auth/login');
            return;
        }

        // Tenta autenticar no banco
        $usuario = $this->usuarioModel->autenticar($login, $senha);

        if (!$usuario) {
            // Autenticação falhou
            Session::setFlash('erro',
                'Login ou senha incorretos!'
            );
            $this->view('auth/login');
            return;
        }

        // Autenticação bem sucedida!
        // Salva os dados na sessão
        Session::logarUsuario($usuario);

        // Redireciona para o dashboard
        Session::setFlash('sucesso',
            'Bem-vindo(a), ' . $usuario['nome'] . '!'
        );
        $this->redirecionar('dashboard');
    }

    // ================================================
    // ACTION: logout
    // Destroi a sessão e volta para o login
    //
    // URL: index.php?controller=auth&action=logout
    // ================================================
    public function logout(): void {
        Session::destruir();
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}