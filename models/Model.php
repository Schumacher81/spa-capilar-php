<?php


/**
 * Model Base — classe pai de todos os models
 * 
 * Contém a conexão com o banco e métodos
 * genéricos que todos os models usam.
 */
abstract class Model {

    // Conexão com o banco (PDO)
    protected PDO $db;

    // Nome da tabela (cada model define o seu)
    protected string $tabela = '';

    // ================================================
    // CONSTRUTOR
    // Obtém a conexão via Singleton
    // ================================================
    public function __construct() {
        $this->db = Database::getInstance()->getConexao();
    }

    // ================================================
    // BUSCAR TODOS OS REGISTROS
    // ================================================
    public function buscarTodos(): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->tabela} ORDER BY id DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ================================================
    // BUSCAR POR ID
    // ================================================
    public function buscarPorId(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->tabela} WHERE id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // ================================================
    // DELETAR POR ID
    // ================================================
    public function deletarPorId(int $id): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->tabela} WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    // ================================================
    // CONTAR REGISTROS
    // ================================================
    public function contar(): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->tabela}"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    // ================================================
    // ÚLTIMO ID INSERIDO
    // ================================================
    protected function ultimoId(): string {
        return $this->db->lastInsertId();
    }
}