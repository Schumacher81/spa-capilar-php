<?php


/**
 * DashboardModel — Consultas para o Dashboard
 * 
 * Reúne todas as queries de resumo do sistema
 */
class DashboardModel extends Model {

    protected string $tabela = 'clientes';

    // ================================================
    // TOTAL DE CLIENTES ATIVOS
    // ================================================
    public function totalClientes(): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM clientes WHERE ativo = 1"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    // ================================================
    // TOTAL DE AGENDAMENTOS DE HOJE
    // ================================================
    public function agendamentosHoje(): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM agendamentos 
             WHERE DATE(data_hora) = CURDATE()
             AND status = 'AGENDADO'"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    // ================================================
    // TOTAL DE AGENDAMENTOS DO MÊS
    // ================================================
    public function agendamentosMes(): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM agendamentos 
             WHERE MONTH(data_hora) = MONTH(NOW())
             AND YEAR(data_hora)  = YEAR(NOW())"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    // ================================================
    // TOTAL DE ATENDIMENTOS DO MÊS
    // ================================================
    public function atendimentosMes(): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM atendimentos
             WHERE MONTH(realizado_em) = MONTH(NOW())
             AND YEAR(realizado_em)  = YEAR(NOW())"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    // ================================================
    // FATURAMENTO DO MÊS
    // ================================================
    public function faturamentoMes(): float {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(valor), 0) 
             FROM atendimentos
             WHERE MONTH(realizado_em) = MONTH(NOW())
             AND YEAR(realizado_em)  = YEAR(NOW())"
        );
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }

    // ================================================
    // PRÓXIMOS AGENDAMENTOS (próximos 7 dias)
    // ================================================
    public function proximosAgendamentos(int $limite = 8): array {
        $stmt = $this->db->prepare(
            "SELECT 
                a.id,
                a.data_hora,
                a.servico,
                a.status,
                c.nome  AS cliente_nome,
                c.telefone AS cliente_telefone,
                u.nome  AS profissional_nome
             FROM agendamentos a
             INNER JOIN clientes c ON c.id = a.cliente_id
             INNER JOIN usuarios u ON u.id = a.profissional_id
             WHERE a.data_hora >= NOW()
             AND a.data_hora <= DATE_ADD(NOW(), INTERVAL 7 DAY)
             AND a.status = 'AGENDADO'
             ORDER BY a.data_hora ASC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ================================================
    // ÚLTIMOS CLIENTES CADASTRADOS
    // ================================================
    public function ultimosClientes(int $limite = 5): array {
        $stmt = $this->db->prepare(
            "SELECT id, nome, telefone, criado_em
             FROM clientes
             WHERE ativo = 1
             ORDER BY criado_em DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ================================================
    // AGENDAMENTOS POR STATUS (para gráfico simples)
    // ================================================
    public function agendamentosPorStatus(): array {
        $stmt = $this->db->prepare(
            "SELECT status, COUNT(*) as total
             FROM agendamentos
             GROUP BY status"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}