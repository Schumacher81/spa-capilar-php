<?php

/**
 * AgendamentoModel — Model de agendamentos
 */
class AgendamentoModel extends Model {

    protected string $tabela = 'agendamentos';

    // ================================================
    // LISTAR TODOS COM DADOS COMPLETOS
    // Usa a view do banco para evitar JOIN no PHP
    // ================================================
    public function listarTodos(): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM vw_agendamentos"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ================================================
    // LISTAR POR STATUS
    // ================================================
    public function listarPorStatus(string $status): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM vw_agendamentos
             WHERE status = :status"
        );
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }

    // ================================================
    // LISTAR POR PERÍODO
    // ================================================
    public function listarPorPeriodo(string $inicio,
                                     string $fim): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM vw_agendamentos
             WHERE DATE(data_hora) BETWEEN :inicio AND :fim
             ORDER BY data_hora ASC"
        );
        $stmt->execute([
            ':inicio' => $inicio,
            ':fim'    => $fim,
        ]);
        return $stmt->fetchAll();
    }

    // ================================================
    // LISTAR POR CLIENTE
    // ================================================
    public function listarPorCliente(int $clienteId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM vw_agendamentos
             WHERE cliente_id = :cliente_id"
        );
        $stmt->execute([':cliente_id' => $clienteId]);
        return $stmt->fetchAll();
    }

    // ================================================
    // BUSCAR POR ID COM DADOS COMPLETOS
    // ================================================
    public function buscarCompleto(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM vw_agendamentos WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // ================================================
    // VERIFICAR CONFLITO DE HORÁRIO
    // Impede dois agendamentos no mesmo horário
    // para o mesmo profissional
    // ================================================
    public function existeConflito(int $profissionalId,
                                   string $dataHora,
                                   int $excluirId = 0): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM agendamentos
             WHERE profissional_id = :profissional_id
             AND data_hora = :data_hora
             AND status   = 'AGENDADO'
             AND id      != :excluir_id"
        );
        $stmt->execute([
            ':profissional_id' => $profissionalId,
            ':data_hora'       => $dataHora,
            ':excluir_id'      => $excluirId,
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }

    // ================================================
    // SALVAR NOVO AGENDAMENTO
    // ================================================
    public function salvar(array $dados): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO agendamentos
                (cliente_id, profissional_id, data_hora,
                 servico, observacoes, status)
             VALUES
                (:cliente_id, :profissional_id, :data_hora,
                 :servico, :observacoes, 'AGENDADO')"
        );
        return $stmt->execute([
            ':cliente_id'      => $dados['cliente_id'],
            ':profissional_id' => $dados['profissional_id'],
            ':data_hora'       => $dados['data_hora'],
            ':servico'         => $dados['servico'],
            ':observacoes'     => $dados['observacoes'] ?: null,
        ]);
    }

    // ================================================
    // ATUALIZAR STATUS
    // ================================================
    public function atualizarStatus(int $id,
                                    string $status): bool {
        $stmt = $this->db->prepare(
            "UPDATE agendamentos 
             SET status = :status 
             WHERE id = :id"
        );
        return $stmt->execute([
            ':status' => $status,
            ':id'     => $id,
        ]);
    }

    // ================================================
    // ATUALIZAR AGENDAMENTO COMPLETO
    // ================================================
    public function atualizar(int $id, array $dados): bool {
        $stmt = $this->db->prepare(
            "UPDATE agendamentos SET
                cliente_id      = :cliente_id,
                profissional_id = :profissional_id,
                data_hora       = :data_hora,
                servico         = :servico,
                observacoes     = :observacoes
             WHERE id = :id"
        );
        return $stmt->execute([
            ':cliente_id'      => $dados['cliente_id'],
            ':profissional_id' => $dados['profissional_id'],
            ':data_hora'       => $dados['data_hora'],
            ':servico'         => $dados['servico'],
            ':observacoes'     => $dados['observacoes'] ?: null,
            ':id'              => $id,
        ]);
    }

    // ================================================
    // VERIFICAR SE JÁ TEM ATENDIMENTO VINCULADO
    // ================================================
    public function temAtendimento(int $id): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM atendimentos
             WHERE agendamento_id = :id"
        );
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }
}