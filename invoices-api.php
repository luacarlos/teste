<?php
// API para gerenciamento de faturas
include 'config.php';
include 'auth-middleware.php';

verificarPermissao();
header('Content-Type: application/json; charset=utf-8');

$metodo = $_SERVER['REQUEST_METHOD'];
$acao = isset($_GET['acao']) ? $_GET['acao'] : '';

// Listar faturas com filtros
if ($acao == 'listar' && $metodo == 'GET') {
    $status = isset($_GET['status']) ? escapar($_GET['status']) : '';
    $mes = isset($_GET['mes']) ? escapar($_GET['mes']) : date('Y-m');
    
    $sql = "SELECT f.*, c.nome as cliente_nome, c.email as cliente_email
            FROM faturas f
            JOIN clientes c ON f.cliente_id = c.id
            WHERE DATE_FORMAT(f.created_at, '%Y-%m') = '$mes'";
    
    if (!empty($status)) {
        $sql .= " AND f.status = '$status'";
    }
    
    $sql .= " ORDER BY f.created_at DESC";
    
    $result = $conn->query($sql);
    $faturas = [];
    
    while ($row = $result->fetch_assoc()) {
        $faturas[] = $row;
    }
    
    echo json_encode(['sucesso' => true, 'faturas' => $faturas]);
}

// Criar fatura
elseif ($acao == 'criar' && $metodo == 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    
    $agendamento_id = !empty($dados['agendamento_id']) ? intval($dados['agendamento_id']) : null;
    $cliente_id = intval($dados['cliente_id']);
    $valor_total = floatval($dados['valor_total']);
    $data_vencimento = escapar($dados['data_vencimento'] ?? date('Y-m-d', strtotime('+7 days')));
    $status = 'pendente';
    
    $agendamento_sql = $agendamento_id ? $agendamento_id : 'NULL';
    
    $sql = "INSERT INTO faturas (agendamento_id, cliente_id, valor_total, data_vencimento, status)
            VALUES ($agendamento_sql, $cliente_id, $valor_total, '$data_vencimento', '$status')";
    
    if ($conn->query($sql)) {
        $id = $conn->insert_id;
        registrarAtividade($_SESSION['usuario_id'], 'fatura_criada', "Fatura ID: $id");
        echo json_encode(['sucesso' => true, 'id' => $id, 'mensagem' => 'Fatura criada com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Atualizar status da fatura
elseif ($acao == 'atualizar_status' && $metodo == 'PUT') {
    $dados = json_decode(file_get_contents('php://input'), true);
    
    $id = intval($dados['id']);
    $novo_status = escapar($dados['status']);
    
    $status_validos = ['pendente', 'pago', 'cancelado'];
    if (!in_array($novo_status, $status_validos)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Status inválido']);
        exit();
    }
    
    $sql = "UPDATE faturas SET status = '$novo_status'";
    if ($novo_status == 'pago') {
        $sql .= ", data_pagamento = NOW()";
    }
    $sql .= " WHERE id = $id";
    
    if ($conn->query($sql)) {
        registrarAtividade($_SESSION['usuario_id'], 'fatura_status_alterado', "Fatura $id: $novo_status");
        echo json_encode(['sucesso' => true, 'mensagem' => 'Status atualizado com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Obter fatura por ID
elseif ($acao == 'obter' && $metodo == 'GET') {
    $id = intval($_GET['id']);
    
    $result = $conn->query("SELECT f.*, c.nome as cliente_nome, c.email as cliente_email, c.telefone, c.endereco
                           FROM faturas f
                           JOIN clientes c ON f.cliente_id = c.id
                           WHERE f.id = $id");
    
    if ($result->num_rows > 0) {
        $fatura = $result->fetch_assoc();
        echo json_encode(['sucesso' => true, 'fatura' => $fatura]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => 'Fatura não encontrada']);
    }
}

// Deletar fatura
elseif ($acao == 'deletar' && $metodo == 'DELETE') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $id = intval($dados['id']);
    
    $sql = "DELETE FROM faturas WHERE id = $id AND status = 'pendente'";
    
    if ($conn->query($sql)) {
        if ($conn->affected_rows > 0) {
            registrarAtividade($_SESSION['usuario_id'], 'fatura_deletada', "Fatura ID: $id");
            echo json_encode(['sucesso' => true, 'mensagem' => 'Fatura deletada com sucesso']);
        } else {
            echo json_encode(['sucesso' => false, 'erro' => 'Apenas faturas pendentes podem ser deletadas']);
        }
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Resumo financeiro
elseif ($acao == 'resumo' && $metodo == 'GET') {
    $mes = isset($_GET['mes']) ? escapar($_GET['mes']) : date('Y-m');
    
    $total_pago = $conn->query("SELECT SUM(valor_total) as total FROM faturas WHERE status = 'pago' AND DATE_FORMAT(created_at, '%Y-%m') = '$mes'")->fetch_assoc()['total'] ?? 0;
    
    $total_pendente = $conn->query("SELECT SUM(valor_total) as total FROM faturas WHERE status = 'pendente' AND DATE_FORMAT(created_at, '%Y-%m') = '$mes'")->fetch_assoc()['total'] ?? 0;
    
    $total_faturas = $conn->query("SELECT COUNT(*) as total FROM faturas WHERE DATE_FORMAT(created_at, '%Y-%m') = '$mes'")->fetch_assoc()['total'];
    
    echo json_encode([
        'total_pago' => formatarMoeda($total_pago),
        'total_pendente' => formatarMoeda($total_pendente),
        'total_faturas' => $total_faturas,
        'percentual_recebimento' => $total_faturas > 0 ? round(($total_pago / ($total_pago + $total_pendente)) * 100) : 0
    ]);
}

else {
    echo json_encode(['erro' => 'Ação não encontrada']);
}

$conn->close();
?>
