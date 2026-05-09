<?php

/**
 * AgendaController — Calendário visual de agendamentos
 */
class AgendaController extends Controller {

    private AgendaModel   $agendaModel;
    private UsuarioModel  $usuarioModel;

    public function __construct() {
        Session::requerLogin();
        $this->agendaModel  = new AgendaModel();
        $this->usuarioModel = new UsuarioModel();
    }

    // ================================================
    // ACTION: index — Visão mensal (padrão)
    // URL: index.php?controller=agenda
    // ================================================
    public function index(): void {

        // Mês e ano atual ou passado via GET
        $mes  = (int) $this->get('mes', date('n'));
        $ano  = (int) $this->get('ano', date('Y'));
        $profissionalId = (int) $this->get('profissional', 0);

        // Garante valores válidos
        if ($mes < 1 || $mes > 12) $mes = (int) date('n');
        if ($ano  < 2000 || $ano > 2100) $ano = (int) date('Y');

        // Calcula mês anterior e próximo para navegação
        $mesAnterior = $mes - 1;
        $anoAnterior = $ano;
        if ($mesAnterior < 1) {
            $mesAnterior = 12;
            $anoAnterior--;
        }

        $mesSeguinte = $mes + 1;
        $anoSeguinte = $ano;
        if ($mesSeguinte > 12) {
            $mesSeguinte = 1;
            $anoSeguinte++;
        }

        // Busca agendamentos do mês organizados por dia
        $agendamentos = $this->agendaModel
            ->buscarPorMes($mes, $ano, $profissionalId);

        // Lista de profissionais para o filtro
        $profissionais = $this->usuarioModel
            ->listarProfissionais();

        $this->view('agenda/mensal', [
            'tituloPagina'   => 'Agenda — ' .
                $this->nomeMes($mes) . ' ' . $ano,
            'mes'            => $mes,
            'ano'            => $ano,
            'mesAnterior'    => $mesAnterior,
            'anoAnterior'    => $anoAnterior,
            'mesSeguinte'    => $mesSeguinte,
            'anoSeguinte'    => $anoSeguinte,
            'agendamentos'   => $agendamentos,
            'profissionais'  => $profissionais,
            'profissionalId' => $profissionalId,
            'nomeMes'        => $this->nomeMes($mes),
        ]);
    }

    // ================================================
    // ACTION: semanal — Visão semanal
    // URL: index.php?controller=agenda&action=semanal
    // ================================================
    public function semanal(): void {

        // Data base da semana (segunda-feira)
        $dataBase = $this->get('data', date('Y-m-d'));
        $profissionalId = (int) $this->get('profissional', 0);

        // Encontra a segunda-feira da semana
        $timestamp  = strtotime($dataBase);
        $diaSemana  = (int) date('N', $timestamp); // 1=seg, 7=dom
        $segunda    = date('Y-m-d',
            strtotime('-' . ($diaSemana - 1) . ' days',
            $timestamp));
        $domingo    = date('Y-m-d',
            strtotime('+' . (7 - $diaSemana) . ' days',
            $timestamp));

        // Semana anterior e próxima
        $semanaAnterior = date('Y-m-d',
            strtotime('-7 days', strtotime($segunda)));
        $semanaSeguinte = date('Y-m-d',
            strtotime('+7 days', strtotime($segunda)));

        // Gera os 7 dias da semana
        $diasSemana = [];
        for ($i = 0; $i < 7; $i++) {
            $diasSemana[] = date('Y-m-d',
                strtotime("+{$i} days", strtotime($segunda)));
        }

        // Busca agendamentos da semana
        $agendamentos = $this->agendaModel
            ->buscarPorSemana($segunda, $domingo,
                $profissionalId);

        // Lista de profissionais
        $profissionais = $this->usuarioModel
            ->listarProfissionais();

        // Horários de funcionamento (8h às 20h)
        $horarios = range(8, 20);

        $this->view('agenda/semanal', [
            'tituloPagina'    => 'Agenda Semanal',
            'segunda'         => $segunda,
            'domingo'         => $domingo,
            'diasSemana'      => $diasSemana,
            'semanaAnterior'  => $semanaAnterior,
            'semanaSeguinte'  => $semanaSeguinte,
            'agendamentos'    => $agendamentos,
            'profissionais'   => $profissionais,
            'profissionalId'  => $profissionalId,
            'horarios'        => $horarios,
        ]);
    }

    // ================================================
    // ACTION: dia — Lista agendamentos de um dia
    // Chamado via AJAX pelo JavaScript
    // ================================================
    public function dia(): void {

        $data = $this->get('data', date('Y-m-d'));
        $agendamentos = $this->agendaModel->buscarPorDia($data);

        // Retorna JSON para o popup
        $this->json([
            'data'         => date('d/m/Y', strtotime($data)),
            'agendamentos' => $agendamentos,
        ]);
    }

    // ================================================
    // HELPER — Nome do mês em português
    // ================================================
    private function nomeMes(int $mes): string {
        $nomes = [
            1  => 'Janeiro',   2  => 'Fevereiro',
            3  => 'Março',     4  => 'Abril',
            5  => 'Maio',      6  => 'Junho',
            7  => 'Julho',     8  => 'Agosto',
            9  => 'Setembro',  10 => 'Outubro',
            11 => 'Novembro',  12 => 'Dezembro',
        ];
        return $nomes[$mes] ?? '';
    }

    // ================================================
    // ACTION: diario — Visão diária por profissional
    // URL: index.php?controller=agenda&action=diario
    // ================================================
    public function diario(): void {

        // Data selecionada (padrão = hoje)
        $data = $this->get('data', date('Y-m-d'));

        // Valida o formato da data
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            $data = date('Y-m-d');
        }

        // Dia anterior e próximo para navegação
        $diaAnterior = date('Y-m-d',
            strtotime('-1 day', strtotime($data)));
        $diaSeguinte = date('Y-m-d',
            strtotime('+1 day', strtotime($data)));

        // Busca agendamentos do dia organizados
        // por profissional e hora
        $agendamentos = $this->agendaModel
            ->buscarDiarioPorProfissional($data);

        // Busca todos os profissionais ativos
        $profissionais = $this->usuarioModel
            ->listarProfissionais();

        // Horários de funcionamento (08h às 20h)
        $horarios = range(8, 20);

        // Nome do dia da semana em português
        $nomesDias = [
            'Sunday'    => 'Domingo',
            'Monday'    => 'Segunda-feira',
            'Tuesday'   => 'Terça-feira',
            'Wednesday' => 'Quarta-feira',
            'Thursday'  => 'Quinta-feira',
            'Friday'    => 'Sexta-feira',
            'Saturday'  => 'Sábado',
        ];
        $nomeDiaSemana = $nomesDias[date('l',
            strtotime($data))] ?? '';

        $this->view('agenda/diario', [
            'tituloPagina'  => 'Agenda Diária — ' .
                date('d/m/Y', strtotime($data)),
            'data'          => $data,
            'diaAnterior'   => $diaAnterior,
            'diaSeguinte'   => $diaSeguinte,
            'agendamentos'  => $agendamentos,
            'profissionais' => $profissionais,
            'horarios'      => $horarios,
            'nomeDiaSemana' => $nomeDiaSemana,
            'hoje'          => date('Y-m-d'),
        ]);
    }
}