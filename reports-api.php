<?php
// API para relatórios e análises
include 'config.php';
include 'auth-middleware.php';

verificarPermissao();
header('Content-Type: application/json; charset=utf-8');

$metodo = $_SERVER['REQUEST_METHOD'];
$acao = isset($_GET['acao']) ? $_GET['acao'] : '';

// Relatório de receita mensal
if ($acao == 'receita_mensal' && $metodo == 'GET') {
    $ano = isset($_GET['ano']) ? intval($_GET['ano']) : intval(date('Y'));
    
    $sql = "SELECT 
            MONTH(f.created_at) as mes,
            SUM(CASE WHEN f.status = 'pago' THEN f.valor_total ELSE 0 END) as receita,
            SUM(CASE WHEN f.status = 'pendente' THEN f.valor_total ELSE 0 END) as pendente,
            COUNT(CASE WHEN f.status = 'pago' THEN 1 END) as faturas_pagas
            FROM faturas f
            WHERE YEAR(f.created_at) = $ano
            GROUP BY MONTH(f.created_at)
            ORDER BY mes ASC";
    
    $result = $conn->query($sql);
    $dados = [];
    
    while ($row = $result->fetch_assoc()) {
        $mes_nome = formatarMes($row['mes']);
        $dados[] = [
            'mes' => $mes_nome,
            'receita' => floatval($row['receita'] ?? 0),
            'pendente' => floatval($row['pendente'] ?? 0),
            'faturas_pagas' => $row['faturas_pagas']
        ];
    }
    
    echo json_encode(['dados' => $dados]);
}

// Serviços mais procurados
elseif ($acao == 'servicos_populares' && $metodo == 'GET') {
    $mes = isset($_GET['mes']) ? escapar($_GET['mes']) : date('Y-m');
    
    $sql = "SELECT 
            s.id,
            s.nome,
            COUNT(a.id) as total_agendamentos,
            SUM(f.valor_total) as receita
            FROM servicos s
            LEFT JOIN agendamentos a ON s.id = a.servico_id AND DATE_FORMAT(a.data_agendamento, '%Y-%m') = '$mes'
            LEFT JOIN faturas f ON a.id = f.agendamento_id AND f.status = 'pago'
            WHERE s.ativo = TRUE
            GROUP BY s.id
            ORDER BY total_agendamentos DESC
            LIMIT 10";
    
    $result = $conn->query($sql);
    $dados = [];
    
    while ($row = $result->fetch_assoc()) {
        $dados[] = [
            'nome' => $row['nome'],
            'agendamentos' => intval($row['total_agendamentos']),
            'receita' => floatval($row['receita'] ?? 0)
        ];
    }
    
    echo json_encode(['dados' => $dados]);
}

// Crescimento de clientes
elseif ($acao == 'crescimento_clientes' && $metodo == 'GET') {
    $ano = isset($_GET['ano']) ? intval($_GET['ano']) : intval(date('Y'));
    
    $sql = "SELECT 
            MONTH(created_at) as mes,
            COUNT(*) as novos_clientes
            FROM clientes
            WHERE YEAR(created_at) = $ano
            GROUP BY MONTH(created_at)
            ORDER BY mes ASC";
    
    $result = $conn->query($sql);
    $dados = [];
    
    while ($row = $result->fetch_assoc()) {
        $mes_nome = formatarMes($row['mes']);
        $dados[] = [
            'mes' => $mes_nome,
            'clientes' => intval($row['novos_clientes'])
        ];
    }
    
    echo json_encode(['dados' => $dados]);
}

// Taxa de ocupação (agendamentos vs capacidade)
elseif ($acao == 'taxa_ocupacao' && $metodo == 'GET') {
    $data = isset($_GET['data']) ? escapar($_GET['data']) : date('Y-m-d');
    
    // Assumindo 10 slots por dia (08:00-18:00, 30 min cada)
    $slots_totais = 20;
    
    $agendamentos = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE DATE(data_agendamento) = '$data' AND status != 'cancelado'")->fetch_assoc()['total'];
    
    $taxa = round(($agendamentos / $slots_totais) * 100);
    
    echo json_encode([
        'slots_totais' => $slots_totais,
        'slots_ocupados' => intval($agendamentos),
        'taxa_ocupacao' => intval($taxa),
        'data' => $data
    ]);
}

// Clientes mais ativos (maior número de agendamentos)
elseif ($acao == 'clientes_ativos' && $metodo == 'GET') {
    $mes = isset($_GET['mes']) ? escapar($_GET['mes']) : date('Y-m');
    
    $sql = "SELECT 
            c.id,
            c.nome,
            COUNT(a.id) as total_agendamentos,
            SUM(f.valor_total) as gasto_total
            FROM clientes c
            LEFT JOIN agendamentos a ON c.id = a.cliente_id AND DATE_FORMAT(a.data_agendamento, '%Y-%m') = '$mes'
            LEFT JOIN faturas f ON c.id = f.cliente_id AND f.status = 'pago' AND DATE_FORMAT(f.created_at, '%Y-%m') = '$mes'
            GROUP BY c.id
            HAVING total_agendamentos > 0
            ORDER BY total_agendamentos DESC
            LIMIT 10";
    
    $result = $conn->query($sql);
    $dados = [];
    
    while ($row = $result->fetch_assoc()) {
        $dados[] = [
            'nome' => $row['nome'],
            'agendamentos' => intval($row['total_agendamentos']),
            'gasto_total' => floatval($row['gasto_total'] ?? 0)
        ];
    }
    
    echo json_encode(['dados' => $dados]);
}

// Dashboard resumido
elseif ($acao == 'dashboard_resumo' && $metodo == 'GET') {
    $mes = isset($_GET['mes']) ? escapar($_GET['mes']) : date('Y-m');
    
    $total_clientes = $conn->query("SELECT COUNT(*) as total FROM clientes")->fetch_assoc()['total'];
    $total_agendamentos = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE DATE_FORMAT(data_agendamento, '%Y-%m') = '$mes'")->fetch_assoc()['total'];
    $receita_mes = $conn->query("SELECT SUM(valor_total) as total FROM faturas WHERE status = 'pago' AND DATE_FORMAT(created_at, '%Y-%m') = '$mes'")->fetch_assoc()['total'] ?? 0;
    $faturas_pendentes = $conn->query("SELECT SUM(valor_total) as total FROM faturas WHERE status = 'pendente' AND DATE_FORMAT(created_at, '%Y-%m') = '$mes'")->fetch_assoc()['total'] ?? 0;
    
    echo json_encode([
        'total_clientes' => intval($total_clientes),
        'total_agendamentos_mes' => intval($total_agendamentos),
        'receita_mes' => floatval($receita_mes),
        'pendente' => floatval($faturas_pendentes),
        'ticket_medio' => intval($total_agendamentos) > 0 ? round($receita_mes / $total_agendamentos, 2) : 0
    ]);
}

// Exportar relatório para CSV
elseif ($acao == 'exportar_csv' && $metodo == 'GET') {
    $tipo = isset($_GET['tipo']) ? escapar($_GET['tipo']) : 'faturas';
    $mes = isset($_GET['mes']) ? escapar($_GET['mes']) : date('Y-m');
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="relatorio_' . $tipo . '_' . $mes . '.csv"');
    
    if ($tipo == 'faturas') {
        $sql = "SELECT f.id, c.nome, f.valor_total, f.status, f.created_at FROM faturas f JOIN clientes c ON f.cliente_id = c.id WHERE DATE_FORMAT(f.created_at, '%Y-%m') = '$mes' ORDER BY f.created_at DESC";
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
        fputcsv($output, ['ID', 'Cliente', 'Valor', 'Status', 'Data'], ';');
        
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['id'],
                $row['nome'],
                $row['valor_total'],
                $row['status'],
                $row['created_at']
            ], ';');
        }
        fclose($output);
    } elseif ($tipo == 'agendamentos') {
        $sql = "SELECT a.id, c.nome, p.nome as pet, s.nome as servico, a.data_agendamento, a.status FROM agendamentos a JOIN clientes c ON a.cliente_id = c.id JOIN animais p ON a.animal_id = p.id JOIN servicos s ON a.servico_id = s.id WHERE DATE_FORMAT(a.data_agendamento, '%Y-%m') = '$mes' ORDER BY a.data_agendamento DESC";
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
        fputcsv($output, ['ID', 'Cliente', 'Pet', 'Serviço', 'Data', 'Status'], ';');
        
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['id'],
                $row['nome'],
                $row['pet'],
                $row['servico'],
                $row['data_agendamento'],
                $row['status']
            ], ';');
        }
        fclose($output);
    }
    exit();
}

else {
    echo json_encode(['erro' => 'Ação não encontrada']);
}

// Função auxiliar para formatar mês
function formatarMes($numero) {
    $meses = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
    return $meses[$numero];
}

$conn->close();
?>
