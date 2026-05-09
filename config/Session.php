<?php

/**
 * Classe Session — Gerencia a sessão do usuário
 * 
 * Centraliza todas as operações com $_SESSION
 * para evitar código repetido em todo o sistema
 */
class Session {

    // ================================================
    // Inicia a sessão se ainda não foi iniciada
    // ================================================
    public static function iniciar(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ================================================
    // Salva o usuário na sessão após o login
    // ================================================
    public static function logarUsuario(array $usuario): void {
        self::iniciar();
        $_SESSION['usuario_id']    = $usuario['id'];
        $_SESSION['usuario_nome']  = $usuario['nome'];
        $_SESSION['usuario_login'] = $usuario['login'];
        $_SESSION['usuario_perfil']= $usuario['perfil'];
        $_SESSION['logado']        = true;
    }

    // ================================================
    // Verifica se há usuário logado
    // ================================================
    public static function estaLogado(): bool {
        self::iniciar();
        return isset($_SESSION['logado']) && $_SESSION['logado'] === true;
    }

    // ================================================
    // Redireciona para login se não estiver logado
    // Use no início de cada página protegida
    // ================================================
    public static function requerLogin(): void {
        if (!self::estaLogado()) {
            self::redirecionar('/index.php');
        }
    }

    // ================================================
    // Verifica se o usuário tem determinado perfil
    // ================================================
    public static function temPerfil(string $perfil): bool {
        self::iniciar();
        return isset($_SESSION['usuario_perfil'])
            && $_SESSION['usuario_perfil'] === $perfil;
    }

    // ================================================
    // Redireciona se não for ADMIN
    // ================================================
    public static function requerAdmin(): void {
        self::requerLogin();
        if (!self::temPerfil('ADMIN')) {
            self::redirecionar('/index.php?controller=dashboard');
        }
    }
    // ================================================
    // Retorna dados do usuário logado
    // ================================================
    public static function getUsuario(): array {
        self::iniciar();
        return [
            'id'     => $_SESSION['usuario_id']     ?? null,
            'nome'   => $_SESSION['usuario_nome']   ?? null,
            'login'  => $_SESSION['usuario_login']  ?? null,
            'perfil' => $_SESSION['usuario_perfil'] ?? null,
        ];
    }

    // ================================================
    // Salva mensagem de feedback (sucesso ou erro)
    // Aparece uma vez e some (flash message)
    // ================================================
    public static function setFlash(string $tipo, string $mensagem): void {
        self::iniciar();
        $_SESSION['flash'] = [
            'tipo'     => $tipo,       // 'sucesso' ou 'erro'
            'mensagem' => $mensagem
        ];
    }

    // ================================================
    // Lê e apaga a mensagem flash
    // ================================================
    public static function getFlash(): ?array {
        self::iniciar();
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']); // apaga após ler
            return $flash;
        }
        return null;
    }

    // ================================================
    // Destrói a sessão (logout)
    // ================================================
    public static function destruir(): void {
        self::iniciar();
        session_unset();
        session_destroy();
    }

    // ================================================
    // MÉTODO PRIVADO — centraliza o redirecionamento
    // Usa BASE_URL se disponível, senão monta a URL
    // a partir das variáveis do servidor
    // ================================================
    private static function redirecionar(string $caminho): void {
        // Se a constante BASE_URL já foi definida, usa ela
        if (defined('BASE_URL')) {
            header('Location: ' . BASE_URL . $caminho);
        } else {
            // Monta a URL base dinamicamente como fallback
            $protocolo = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
            $host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $base      = $protocolo . '://' . $host . '/spa-capilar-php';
            header('Location: ' . $base . $caminho);
        }
        exit;
    }
}