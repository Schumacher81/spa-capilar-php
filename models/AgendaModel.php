<?php

/**
 * AgendaModel — Consultas específicas para o calendário
 */
class AgendaModel extends Model {

    protected string $tabela = 'agendamentos';

    // ================================================
    // BUSCAR AGENDAMENTOS DO MÊS
    // Retorna todos os agendamentos de um mês/ano
    // ================================================
    public function buscarPorMes(int $mes,
                                  int $ano,
                                  int $profissionalId = 0): array {
        $sql = "SELECT
                    a.id,
                    a.data_hora,
                    a.servico,
                    a.status,
                    a.observacoes,
                    c.id    AS cliente_id,
                    c.nome  AS cliente_nome,
                    c.telefone AS cliente_telefone,
                    u.id    AS profissional_id,
                    u.nome  AS profissional_nome
                FROM agendamentos a
                INNER JOIN clientes c ON c.id = a.cliente_id
                INNER JOIN usuarios u ON u.id = a.profissional_id
                WHERE MONTH(a.data_hora) = :mes
                AND   YEAR(a.data_hora)  = :ano";

        // Filtra por profissional se informado
        if ($profissionalId > 0) {
            $sql .= " AND a.profissional_id = :profissional_id";
        }

        $sql .= " ORDER BY a.data_hora ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':mes', $mes, PDO::PARAM_INT);
        $stmt->bindValue(':ano', $ano, PDO::PARAM_INT);

        if ($profissionalId > 0) {
            $stmt->bindValue(':profissional_id',
                $profissionalId, PDO::PARAM_INT);
        }

        $stmt->execute();
        $resultado = $stmt->fetchAll();

        // Organiza por dia para facilitar a renderização
        // ['2024-06-15' => [agendamento1, agendamento2], ...]
        $porDia = [];
        foreach ($resultado as $ag) {
            $dia = date('Y-m-d', strtotime($ag['data_hora']));
            $porDia[$dia][] = $ag;
        }

        return $porDia;
    }

    // ================================================
    // BUSCAR AGENDAMENTOS DA SEMANA
    // ================================================
    public function buscarPorSemana(string $dataInicio,
                                    string $dataFim,
                                    int $profissionalId = 0): array {
        $sql = "SELECT
                    a.id,
                    a.data_hora,
                    a.servico,
                    a.status,
                    a.observacoes,
                    c.id    AS cliente_id,
                    c.nome  AS cliente_nome,
                    c.telefone AS cliente_telefone,
                    u.id    AS profissional_id,
                    u.nome  AS profissional_nome
                FROM agendamentos a
                INNER JOIN clientes c ON c.id = a.cliente_id
                INNER JOIN usuarios u ON u.id = a.profissional_id
                WHERE DATE(a.data_hora) BETWEEN :inicio AND :fim";

        if ($profissionalId > 0) {
            $sql .= " AND a.profissional_id = :profissional_id";
        }

        $sql .= " ORDER BY a.data_hora ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':inicio', $dataInicio);
        $stmt->bindValue(':fim',    $dataFim);

        if ($profissionalId > 0) {
            $stmt->bindValue(':profissional_id',
                $profissionalId, PDO::PARAM_INT);
        }

        $stmt->execute();
        $resultado = $stmt->fetchAll();

        // Organiza por dia E hora
        $porDia = [];
        foreach ($resultado as $ag) {
            $dia  = date('Y-m-d', strtotime($ag['data_hora']));
            $hora = (int) date('H', strtotime($ag['data_hora']));
            $porDia[$dia][$hora][] = $ag;
        }

        return $porDia;
    }

    // ================================================
    // BUSCAR AGENDAMENTOS DE UM DIA ESPECÍFICO
    // ================================================
    public function buscarPorDia(string $data): array {
        $stmt = $this->db->prepare(
            "SELECT
                a.id,
                a.data_hora,
                a.servico,
                a.status,
                a.observacoes,
                c.nome  AS cliente_nome,
                c.telefone AS cliente_telefone,
                u.nome  AS profissional_nome
             FROM agendamentos a
             INNER JOIN clientes c ON c.id = a.cliente_id
             INNER JOIN usuarios u ON u.id = a.profissional_id
             WHERE DATE(a.data_hora) = :data
             ORDER BY a.data_hora ASC"
        );
        $stmt->execute([':data' => $data]);
        return $stmt->fetchAll();
    }

    // ================================================
    // BUSCAR AGENDAMENTOS DO DIA POR PROFISSIONAL
    // Retorna agendamentos organizados assim:
    // [profissional_id => [hora => agendamento]]
    // ================================================
    public function buscarDiarioPorProfissional(
            string $data): array {

        $stmt = $this->db->prepare(
            "SELECT
                a.id,
                a.data_hora,
                a.servico,
                a.status,
                a.observacoes,
                c.id       AS cliente_id,
                c.nome     AS cliente_nome,
                c.telefone AS cliente_telefone,
                u.id       AS profissional_id,
                u.nome     AS profissional_nome
            FROM agendamentos a
            INNER JOIN clientes c ON c.id = a.cliente_id
            INNER JOIN usuarios u ON u.id = a.profissional_id
            WHERE DATE(a.data_hora) = :data
            AND u.ativo = 1
            ORDER BY u.nome ASC, a.data_hora ASC"
        );
        $stmt->execute([':data' => $data]);
        $resultado = $stmt->fetchAll();

        // Organiza por profissional e hora
        // [profissional_id => [hora => agendamento]]
        $organizado = [];
        foreach ($resultado as $ag) {
            $profId = $ag['profissional_id'];
            $hora   = (int) date('H', strtotime($ag['data_hora']));
            $organizado[$profId][$hora] = $ag;
        }

        return $organizado;
    }
}