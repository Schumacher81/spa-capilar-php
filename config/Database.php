<?php

/**
 * Classe Database — Conexão com o banco de dados
 * 
 * Padrão de projeto: SINGLETON
 * Garante que apenas UMA instância de conexão
 * seja criada durante toda a execução do sistema.
 * 
 * Por que Singleton?
 * Abrir e fechar conexões com banco é custoso.
 * O Singleton reutiliza a mesma conexão sempre.
 */
class Database {

    // ================================================
    // CONFIGURAÇÕES DO BANCO
    // Altere apenas estas constantes se necessário
    // ================================================
    private const HOST     = 'localhost';
    private const DBNAME   = 'spa_capilar';
    private const USER     = 'root';
    private const PASSWORD = 'Peteleco123';          // padrão XAMPP sem senha
    private const CHARSET  = 'utf8mb4';

    // ================================================
    // PROPRIEDADES DA CLASSE
    // private = só acessível dentro desta classe
    // static  = pertence à classe, não ao objeto
    // ================================================

    // Guarda a única instância da classe
    private static ?Database $instancia = null;

    // Guarda a conexão PDO
    private PDO $conexao;

    // ================================================
    // CONSTRUTOR PRIVADO
    // private = ninguém pode fazer "new Database()"
    // de fora da classe — isso é o coração do Singleton
    // ================================================
    private function __construct() {
        // Monta a string de conexão (DSN)
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            self::HOST,
            self::DBNAME,
            self::CHARSET
        );

        // Opções do PDO para melhor funcionamento
        $opcoes = [
            // Lança exceções em caso de erro (não silencia erros!)
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

            // Retorna resultados como arrays associativos por padrão
            // Ex: $row['nome'] em vez de $row[0]
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Desativa emulação de prepared statements
            // Mais seguro contra SQL Injection
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            // Cria a conexão PDO
            $this->conexao = new PDO($dsn, self::USER, self::PASSWORD, $opcoes);
        } catch (PDOException $e) {
            // Em produção, nunca mostre detalhes do erro!
            // Aqui mostramos para facilitar o desenvolvimento
            die('Erro de conexão com o banco: ' . $e->getMessage());
        }
    }

    // ================================================
    // MÉTODO ESTÁTICO getInstance()
    // É o único jeito de obter a instância da classe
    // Se não existe → cria uma nova
    // Se já existe  → retorna a mesma
    // ================================================
    public static function getInstance(): Database {
        if (self::$instancia === null) {
            self::$instancia = new Database();
        }
        return self::$instancia;
    }

    // ================================================
    // MÉTODO getConexao()
    // Retorna o objeto PDO para fazer queries
    // ================================================
    public function getConexao(): PDO {
        return $this->conexao;
    }

    // ================================================
    // Impede clonagem do objeto (reforça o Singleton)
    // ================================================
    private function __clone() {}
}