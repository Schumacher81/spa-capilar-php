<?php

/**
 * ClienteModel — Model de clientes
 */
class ClienteModel extends Model {

    protected string $tabela = 'clientes';

    // ================================================
    // LISTAR TODOS OS CLIENTES ATIVOS
    // ================================================
    public function listarAtivos(): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM clientes 
             WHERE ativo = 1 
             ORDER BY nome ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ================================================
    // BUSCAR POR NOME OU TELEFONE
    // ================================================
    public function buscar(string $texto): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM clientes 
             WHERE ativo = 1
             AND (
                nome     LIKE :busca OR
                telefone LIKE :busca OR
                email    LIKE :busca
             )
             ORDER BY nome ASC"
        );
        $stmt->execute([':busca' => '%' . $texto . '%']);
        return $stmt->fetchAll();
    }

    // ================================================
    // VERIFICAR SE TELEFONE JÁ EXISTE
    // ================================================
    public function telefoneExiste(string $telefone,
                                   int $excluirId = 0): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM clientes 
             WHERE telefone = :telefone 
             AND id != :id
             AND ativo = 1"
        );
        $stmt->execute([
            ':telefone' => $telefone,
            ':id'       => $excluirId
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }

    // ================================================
    // SALVAR NOVO CLIENTE
    // ================================================
    public function salvar(array $dados): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO clientes 
                (nome, telefone, email, 
                 data_nascimento, observacoes)
             VALUES 
                (:nome, :telefone, :email,
                 :data_nascimento, :observacoes)"
        );
        return $stmt->execute([
            ':nome'            => $dados['nome'],
            ':telefone'        => $dados['telefone'],
            ':email'           => $dados['email']           ?: null,
            ':data_nascimento' => $dados['data_nascimento'] ?: null,
            ':observacoes'     => $dados['observacoes']     ?: null,
        ]);
    }

    // ================================================
    // ATUALIZAR CLIENTE
    // ================================================
    public function atualizar(int $id, array $dados): bool {
        $stmt = $this->db->prepare(
            "UPDATE clientes SET
                nome             = :nome,
                telefone         = :telefone,
                email            = :email,
                data_nascimento  = :data_nascimento,
                observacoes      = :observacoes
             WHERE id = :id"
        );
        return $stmt->execute([
            ':nome'            => $dados['nome'],
            ':telefone'        => $dados['telefone'],
            ':email'           => $dados['email']           ?: null,
            ':data_nascimento' => $dados['data_nascimento'] ?: null,
            ':observacoes'     => $dados['observacoes']     ?: null,
            ':id'              => $id,
        ]);
    }

    // ================================================
    // INATIVAR CLIENTE
    // ================================================
    public function inativar(int $id): bool {
        $stmt = $this->db->prepare(
            "UPDATE clientes SET ativo = 0 WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    // ================================================
    // BUSCAR HISTÓRICO COMPLETO DO CLIENTE
    // ================================================
    public function buscarHistorico(int $clienteId): array {
        $stmt = $this->db->prepare(
            "SELECT 
                a.id            AS agendamento_id,
                a.data_hora,
                a.servico,
                a.status,
                u.nome          AS profissional_nome,
                at.id           AS atendimento_id,
                at.valor,
                at.produtos_utilizados,
                d.id            AS diagnostico_id,
                d.tipo_cabelo,
                d.porosidade
             FROM agendamentos a
             INNER JOIN usuarios u ON u.id = a.profissional_id
             LEFT JOIN atendimentos at ON at.agendamento_id = a.id
             LEFT JOIN diagnosticos d  ON d.atendimento_id  = at.id
             WHERE a.cliente_id = :cliente_id
             ORDER BY a.data_hora DESC"
        );
        $stmt->execute([':cliente_id' => $clienteId]);
        return $stmt->fetchAll();
    }
}