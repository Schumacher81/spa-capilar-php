<?php



/**
 * Controller Base — classe pai de todos os controllers
 * 
 * Contém métodos úteis que todos os controllers usam.
 * Os controllers filhos herdam estes métodos com "extends".
 */
abstract class Controller {

    // ================================================
    // CARREGAR VIEW
    // Inclui o arquivo da view passando variáveis
    // ================================================
    protected function view(string $view, array $dados = []): void {
        // extract() transforma chaves do array em variáveis
        // ['cliente' => $obj] → $cliente = $obj
        // Assim a view pode usar $cliente diretamente
        extract($dados);

        // Monta o caminho completo da view
        $arquivo = VIEWS_PATH . '/' . $view . '.php';

        if (!file_exists($arquivo)) {
            die("View não encontrada: {$view}");
        }

        require_once $arquivo;
    }

    // ================================================
    // REDIRECIONAR
    // ================================================
    protected function redirecionar(string $controller,
                                    string $action = 'index'): void {
        header("Location: " . BASE_URL
            . "/index.php?controller={$controller}&action={$action}");
        exit;
    }

    // ================================================
    // REDIRECIONAR COM MENSAGEM FLASH
    // ================================================
    protected function redirecionarComMensagem(
            string $controller,
            string $action,
            string $tipo,
            string $mensagem): void {

        Session::setFlash($tipo, $mensagem);
        $this->redirecionar($controller, $action);
    }

    // ================================================
    // RETORNAR JSON (para requisições AJAX)
    // ================================================
    protected function json(array $dados, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ================================================
    // VERIFICAR SE É REQUISIÇÃO POST
    // ================================================
    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    // ================================================
    // PEGAR DADO DO POST COM SANITIZAÇÃO
    // ================================================
    protected function post(string $campo,
                            string $padrao = ''): string {
        return trim($_POST[$campo] ?? $padrao);
    }

    // ================================================
    // PEGAR DADO DO GET COM SANITIZAÇÃO
    // ================================================
    protected function get(string $campo,
                           mixed $padrao = null): mixed {
        return $_GET[$campo] ?? $padrao;
    }
}