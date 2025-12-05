<?php
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Animal.php';
require_once __DIR__ . '/../models/Agendamento.php';
require_once __DIR__ . '/../models/Fatura.php';

class DashboardController {
    private $clienteModel;
    private $animalModel;
    private $agendamentoModel;
    private $faturaModel;

    public function __construct() {
        $this->clienteModel = new Cliente();
        $this->animalModel = new Animal();
        $this->agendamentoModel = new Agendamento();
        $this->faturaModel = new Fatura();
    }

    public function getStats() {
        $hoje = date('Y-m-d');
        $mesAtual = date('Y-m');
        
        return [
            'total_clientes' => $this->clienteModel->count(),
            'total_animais' => $this->animalModel->count(),
            'agendamentos_hoje' => $this->agendamentoModel->countPorDia($hoje),
            'receita_mes' => $this->faturaModel->getRevenueMonth(date('Y'), date('m')),
            'agendamentos_proximos' => $this->getProximos5Agendamentos()
        ];
    }

    public function getProximos5Agendamentos() {
        $agendamentos = $this->agendamentoModel->getAll();
        $hoje = new DateTime();
        $proximos = [];

        foreach ($agendamentos as $agend) {
            $data_agend = new DateTime($agend['data_agendamento']);
            if ($data_agend >= $hoje && count($proximos) < 5) {
                $proximos[] = $agend;
            }
        }
        
        return $proximos;
    }
}
?>
