<?php

/**
 * AtendimentoModel — Model de atendimentos
 */
class AtendimentoModel extends Model {

    protected string $tabela = 'atendimentos';

    // ================================================
    // LISTAR TODOS COM DADOS COMPLETOS
    // ================================================
    public function listarTodos(): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM vw_atendimentos"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ================================================
    // BUSCAR POR ID COM DADOS COMPLETOS
    // ================================================
    public function buscarCompleto(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM vw_atendimentos WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // ================================================
    // BUSCAR POR AGENDAMENTO
    // ================================================
    public function buscarPorAgendamento(
            int $agendamentoId): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM vw_atendimentos
             WHERE agendamento_id = :agendamento_id"
        );
        $stmt->execute([':agendamento_id' => $agendamentoId]);
        return $stmt->fetch();
    }

    // ================================================
    // VERIFICAR SE JÁ EXISTE ATENDIMENTO
    // ================================================
    public function existePorAgendamento(
            int $agendamentoId): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM atendimentos
             WHERE agendamento_id = :agendamento_id"
        );
        $stmt->execute([':agendamento_id' => $agendamentoId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    // ================================================
    // VERIFICAR SE JÁ TEM DIAGNÓSTICO
    // ================================================
    public function temDiagnostico(int $id): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM diagnosticos
             WHERE atendimento_id = :id"
        );
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    // ================================================
    // SALVAR ATENDIMENTO
    // Usa transação pois precisa atualizar dois registros:
    // 1. Insere o atendimento
    // 2. Atualiza o status do agendamento para REALIZADO
    // ================================================
    public function salvar(array $dados): bool {
        try {
            // Inicia transação — garante que as duas
            // operações acontecem juntas ou nenhuma acontece
            $this->db->beginTransaction();

            // 1. Insere o atendimento
            $stmt = $this->db->prepare(
                "INSERT INTO atendimentos
                    (agendamento_id, produtos_utilizados,
                     observacoes, valor)
                 VALUES
                    (:agendamento_id, :produtos_utilizados,
                     :observacoes, :valor)"
            );
            $stmt->execute([
                ':agendamento_id'     =>
                    $dados['agendamento_id'],
                ':produtos_utilizados' =>
                    $dados['produtos_utilizados'] ?: null,
                ':observacoes'         =>
                    $dados['observacoes'] ?: null,
                ':valor'               =>
                    $dados['valor'] ?: null,
            ]);

            // 2. Atualiza o status do agendamento
            $stmtAg = $this->db->prepare(
                "UPDATE agendamentos
                 SET status = 'REALIZADO'
                 WHERE id = :id"
            );
            $stmtAg->execute([
                ':id' => $dados['agendamento_id']
            ]);

            // Confirma a transação
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // Se algo der errado, desfaz tudo
            $this->db->rollBack();
            return false;
        }
    }

    // ================================================
    // ATUALIZAR ATENDIMENTO
    // ================================================
    public function atualizar(int $id, array $dados): bool {
        $stmt = $this->db->prepare(
            "UPDATE atendimentos SET
                produtos_utilizados = :produtos_utilizados,
                observacoes         = :observacoes,
                valor               = :valor
             WHERE id = :id"
        );
        return $stmt->execute([
            ':produtos_utilizados' =>
                $dados['produtos_utilizados'] ?: null,
            ':observacoes'         =>
                $dados['observacoes'] ?: null,
            ':valor'               =>
                $dados['valor'] ?: null,
            ':id'                  => $id,
        ]);
    }

    // ================================================
    // BUSCAR AGENDAMENTOS DISPONÍVEIS PARA ATENDIMENTO
    // (status AGENDADO e sem atendimento vinculado)
    // ================================================
    public function buscarAgendamentosDisponiveis(): array {
        $stmt = $this->db->prepare(
            "SELECT
                a.id,
                a.data_hora,
                a.servico,
                c.nome  AS cliente_nome,
                u.nome  AS profissional_nome
             FROM agendamentos a
             INNER JOIN clientes c ON c.id = a.cliente_id
             INNER JOIN usuarios u ON u.id = a.profissional_id
             LEFT JOIN atendimentos at
                ON at.agendamento_id = a.id
             WHERE a.status  = 'AGENDADO'
             AND at.id IS NULL
             ORDER BY a.data_hora ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}