<?php

/**
 * DiagnosticoModel — Model de diagnósticos capilares
 */
class DiagnosticoModel extends Model {

    protected string $tabela = 'diagnosticos';

    // ================================================
    // LISTAR TODOS COM DADOS COMPLETOS
    // ================================================
    public function listarTodos(): array {
        $stmt = $this->db->prepare(
            "SELECT
                d.*,
                at.valor,
                at.realizado_em,
                ag.servico,
                ag.data_hora,
                c.id    AS cliente_id,
                c.nome  AS cliente_nome,
                u.nome  AS profissional_nome
             FROM diagnosticos d
             INNER JOIN atendimentos at
                ON at.id = d.atendimento_id
             INNER JOIN agendamentos ag
                ON ag.id = at.agendamento_id
             INNER JOIN clientes c
                ON c.id = ag.cliente_id
             INNER JOIN usuarios u
                ON u.id = ag.profissional_id
             ORDER BY d.criado_em DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ================================================
    // BUSCAR POR ID COM DADOS COMPLETOS
    // ================================================
    public function buscarCompleto(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT
                d.*,
                at.valor,
                at.produtos_utilizados,
                at.realizado_em,
                ag.servico,
                ag.data_hora,
                c.id    AS cliente_id,
                c.nome  AS cliente_nome,
                c.telefone AS cliente_telefone,
                u.nome  AS profissional_nome
             FROM diagnosticos d
             INNER JOIN atendimentos at
                ON at.id = d.atendimento_id
             INNER JOIN agendamentos ag
                ON ag.id = at.agendamento_id
             INNER JOIN clientes c
                ON c.id = ag.cliente_id
             INNER JOIN usuarios u
                ON u.id = ag.profissional_id
             WHERE d.id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // ================================================
    // BUSCAR POR ATENDIMENTO
    // ================================================
    public function buscarPorAtendimento(
            int $atendimentoId): array|false {
        $stmt = $this->db->prepare(
            "SELECT d.*,
                c.nome AS cliente_nome,
                ag.servico
             FROM diagnosticos d
             INNER JOIN atendimentos at
                ON at.id = d.atendimento_id
             INNER JOIN agendamentos ag
                ON ag.id = at.agendamento_id
             INNER JOIN clientes c
                ON c.id = ag.cliente_id
             WHERE d.atendimento_id = :atendimento_id"
        );
        $stmt->execute([':atendimento_id' => $atendimentoId]);
        return $stmt->fetch();
    }

    // ================================================
    // BUSCAR TODOS OS DIAGNÓSTICOS DE UM CLIENTE
    // Útil para ver a evolução capilar ao longo do tempo
    // ================================================
    public function buscarPorCliente(int $clienteId): array {
        $stmt = $this->db->prepare(
            "SELECT
                d.*,
                ag.servico,
                ag.data_hora,
                u.nome AS profissional_nome
             FROM diagnosticos d
             INNER JOIN atendimentos at
                ON at.id = d.atendimento_id
             INNER JOIN agendamentos ag
                ON ag.id = at.agendamento_id
             INNER JOIN usuarios u
                ON u.id = ag.profissional_id
             WHERE ag.cliente_id = :cliente_id
             ORDER BY d.criado_em DESC"
        );
        $stmt->execute([':cliente_id' => $clienteId]);
        return $stmt->fetchAll();
    }

    // ================================================
    // VERIFICAR SE JÁ EXISTE DIAGNÓSTICO
    // ================================================
    public function existePorAtendimento(
            int $atendimentoId): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM diagnosticos
             WHERE atendimento_id = :atendimento_id"
        );
        $stmt->execute([':atendimento_id' => $atendimentoId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    // ================================================
    // BUSCAR ATENDIMENTOS SEM DIAGNÓSTICO
    // ================================================
    public function buscarAtendimentosSemDiagnostico(): array {
        $stmt = $this->db->prepare(
            "SELECT
                at.id,
                at.realizado_em,
                ag.servico,
                c.nome AS cliente_nome,
                u.nome AS profissional_nome
             FROM atendimentos at
             INNER JOIN agendamentos ag
                ON ag.id = at.agendamento_id
             INNER JOIN clientes c
                ON c.id = ag.cliente_id
             INNER JOIN usuarios u
                ON u.id = ag.profissional_id
             LEFT JOIN diagnosticos d
                ON d.atendimento_id = at.id
             WHERE d.id IS NULL
             ORDER BY at.realizado_em DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ================================================
    // SALVAR DIAGNÓSTICO
    // ================================================
    public function salvar(array $dados): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO diagnosticos
                (atendimento_id, tipo_cabelo, porosidade,
                 oleosidade, historico_quimico,
                 problemas, tratamento_recomendado)
             VALUES
                (:atendimento_id, :tipo_cabelo, :porosidade,
                 :oleosidade, :historico_quimico,
                 :problemas, :tratamento_recomendado)"
        );
        return $stmt->execute([
            ':atendimento_id'       =>
                $dados['atendimento_id'],
            ':tipo_cabelo'          =>
                $dados['tipo_cabelo']           ?: null,
            ':porosidade'           =>
                $dados['porosidade']            ?: null,
            ':oleosidade'           =>
                $dados['oleosidade']            ?: null,
            ':historico_quimico'    =>
                $dados['historico_quimico']     ?: null,
            ':problemas'            =>
                $dados['problemas']             ?: null,
            ':tratamento_recomendado' =>
                $dados['tratamento_recomendado'] ?: null,
        ]);
    }

    // ================================================
    // ATUALIZAR DIAGNÓSTICO
    // ================================================
    public function atualizar(int $id, array $dados): bool {
        $stmt = $this->db->prepare(
            "UPDATE diagnosticos SET
                tipo_cabelo             = :tipo_cabelo,
                porosidade              = :porosidade,
                oleosidade              = :oleosidade,
                historico_quimico       = :historico_quimico,
                problemas               = :problemas,
                tratamento_recomendado  = :tratamento_recomendado
             WHERE id = :id"
        );
        return $stmt->execute([
            ':tipo_cabelo'            =>
                $dados['tipo_cabelo']            ?: null,
            ':porosidade'             =>
                $dados['porosidade']             ?: null,
            ':oleosidade'             =>
                $dados['oleosidade']             ?: null,
            ':historico_quimico'      =>
                $dados['historico_quimico']      ?: null,
            ':problemas'              =>
                $dados['problemas']              ?: null,
            ':tratamento_recomendado' =>
                $dados['tratamento_recomendado'] ?: null,
            ':id'                     => $id,
        ]);
    }
}