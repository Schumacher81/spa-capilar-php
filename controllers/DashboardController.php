<?php


/**
 * DashboardController — Página principal do sistema
 */
class DashboardController extends Controller {

    private DashboardModel $dashboardModel;

    public function __construct() {
        // Verifica se está logado ao acessar qualquer
        // método deste controller
        Session::requerLogin();
        $this->dashboardModel = new DashboardModel();
    }

    // ================================================
    // ACTION: index
    // Página inicial após o login
    // ================================================
    public function index(): void {

        // Busca todos os dados para o dashboard
        $totalClientes      = $this->dashboardModel->totalClientes();
        $agendamentosHoje   = $this->dashboardModel->agendamentosHoje();
        $agendamentosMes    = $this->dashboardModel->agendamentosMes();
        $atendimentosMes    = $this->dashboardModel->atendimentosMes();
        $faturamentoMes     = $this->dashboardModel->faturamentoMes();
        $proximosAgs        = $this->dashboardModel->proximosAgendamentos();
        $ultimosClientes    = $this->dashboardModel->ultimosClientes();
        $agsPorStatus       = $this->dashboardModel->agendamentosPorStatus();

        // Organiza status em array associativo para fácil acesso na view
        $statusContagem = [
            'AGENDADO'  => 0,
            'REALIZADO' => 0,
            'CANCELADO' => 0
        ];
        foreach ($agsPorStatus as $item) {
            $statusContagem[$item['status']] = $item['total'];
        }

        $this->view('dashboard/index', [
            'tituloPagina'    => 'Dashboard',
            'totalClientes'   => $totalClientes,
            'agendamentosHoje'=> $agendamentosHoje,
            'agendamentosMes' => $agendamentosMes,
            'atendimentosMes' => $atendimentosMes,
            'faturamentoMes'  => $faturamentoMes,
            'proximosAgs'     => $proximosAgs,
            'ultimosClientes' => $ultimosClientes,
            'statusContagem'  => $statusContagem,
        ]);
    }
}