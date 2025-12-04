<?php
// API para gerenciamento de agendamentos
include 'config.php';
include 'auth-middleware.php';

verificarPermissao();
header('Content-Type: application/json; charset=utf-8');

$metodo = $_SERVER['REQUEST_METHOD'];
$acao = isset($_GET['acao']) ? $_GET['acao'] : '';

// Listar agendamentos com filtros
if ($acao == 'listar' && $metodo == 'GET') {
    $data_inicio = isset($_GET['data_inicio']) ? escapar($_GET['data_inicio']) : date('Y-m-01');
    $data_fim = isset($_GET['data_fim']) ? escapar($_GET['data_fim']) : date('Y-m-t');
    $status = isset($_GET['status']) ? escapar($_GET['status']) : '';
    
    $sql = "SELECT a.*, c.nome as cliente_nome, p.nome as animal_nome, s.nome as servico_nome, s.preco 
            FROM agendamentos a 
            JOIN clientes c ON a.cliente_id = c.id 
            JOIN animais p ON a.animal_id = p.id 
            JOIN servicos s ON a.servico_id = s.id 
            WHERE DATE(a.data_agendamento) BETWEEN '$data_inicio' AND '$data_fim'";
    
    if (!empty($status)) {
        $sql .= " AND a.status = '$status'";
    }
    
    $sql .= " ORDER BY a.data_agendamento ASC";
    
    $result = $conn->query($sql);
    $agendamentos = [];
    
    while ($row = $result->fetch_assoc()) {
        $agendamentos[] = $row;
    }
    
    echo json_encode(['sucesso' => true, 'agendamentos' => $agendamentos]);
}

// Criar agendamento
elseif ($acao == 'criar' && $metodo == 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    
    $cliente_id = intval($dados['cliente_id']);
    $animal_id = intval($dados['animal_id']);
    $servico_id = intval($dados['servico_id']);
    $data_agendamento = escapar($dados['data_agendamento']);
    $observacoes = escapar($dados['observacoes'] ?? '');
    
    // Validar se o cliente e animal existem
    $cliente_check = $conn->query("SELECT id FROM clientes WHERE id = $cliente_id");
    $animal_check = $conn->query("SELECT id FROM animais WHERE id = $animal_id AND cliente_id = $cliente_id");
    $servico_check = $conn->query("SELECT id FROM servicos WHERE id = $servico_id");
    
    if ($cliente_check->num_rows == 0 || $animal_check->num_rows == 0 || $servico_check->num_rows == 0) {
        echo json_encode(['sucesso' => false, 'erro' => 'Dados inválidos']);
        exit();
    }
    
    // Verificar conflitos de horário
    $conflito = $conn->query("SELECT id FROM agendamentos WHERE data_agendamento = '$data_agendamento' AND status != 'cancelado'");
    if ($conflito->num_rows > 0) {
        echo json_encode(['sucesso' => false, 'erro' => 'Já existe agendamento neste horário']);
        exit();
    }
    
    $sql = "INSERT INTO agendamentos (cliente_id, animal_id, servico_id, data_agendamento, observacoes, status) 
            VALUES ($cliente_id, $animal_id, $servico_id, '$data_agendamento', '$observacoes', 'pendente')";
    
    if ($conn->query($sql)) {
        $id = $conn->insert_id;
        registrarAtividade($_SESSION['usuario_id'], 'agendamento_criado', "Agendamento ID: $id");
        echo json_encode(['sucesso' => true, 'id' => $id, 'mensagem' => 'Agendamento criado com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Atualizar status de agendamento
elseif ($acao == 'atualizar_status' && $metodo == 'PUT') {
    $dados = json_decode(file_get_contents('php://input'), true);
    
    $id = intval($dados['id']);
    $novo_status = escapar($dados['status']);
    
    $status_validos = ['pendente', 'confirmado', 'concluido', 'cancelado'];
    if (!in_array($novo_status, $status_validos)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Status inválido']);
        exit();
    }
    
    $sql = "UPDATE agendamentos SET status = '$novo_status' WHERE id = $id";
    
    if ($conn->query($sql)) {
        registrarAtividade($_SESSION['usuario_id'], 'agendamento_status_alterado', "Agendamento $id: $novo_status");
        echo json_encode(['sucesso' => true, 'mensagem' => 'Status atualizado com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Obter agendamento por ID
elseif ($acao == 'obter' && $metodo == 'GET') {
    $id = intval($_GET['id']);
    
    $result = $conn->query("SELECT a.*, c.nome as cliente_nome, p.nome as animal_nome, s.nome as servico_nome, s.preco 
                           FROM agendamentos a 
                           JOIN clientes c ON a.cliente_id = c.id 
                           JOIN animais p ON a.animal_id = p.id 
                           JOIN servicos s ON a.servico_id = s.id 
                           WHERE a.id = $id");
    
    if ($result->num_rows > 0) {
        $agendamento = $result->fetch_assoc();
        echo json_encode(['sucesso' => true, 'agendamento' => $agendamento]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => 'Agendamento não encontrado']);
    }
}

// Deletar agendamento
elseif ($acao == 'deletar' && $metodo == 'DELETE') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $id = intval($dados['id']);
    
    $sql = "DELETE FROM agendamentos WHERE id = $id";
    
    if ($conn->query($sql)) {
        registrarAtividade($_SESSION['usuario_id'], 'agendamento_deletado', "Agendamento ID: $id");
        echo json_encode(['sucesso' => true, 'mensagem' => 'Agendamento deletado com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Obter disponibilidade de horários
elseif ($acao == 'horarios_disponiveis' && $metodo == 'GET') {
    $data = escapar($_GET['data']);
    $duracao_servico = intval($_GET['duracao'] ?? 60);
    
    // Horário de funcionamento: 08:00 às 18:00
    $horarios = [];
    for ($hora = 8; $hora < 18; $hora++) {
        for ($minuto = 0; $minuto < 60; $minuto += 30) {
            $horario = sprintf("%02d:%02d", $hora, $minuto);
            $horarios[] = $horario;
        }
    }
    
    // Remover horários já agendados
    $agendados = $conn->query("SELECT TIME(data_agendamento) as hora FROM agendamentos WHERE DATE(data_agendamento) = '$data' AND status != 'cancelado'");
    
    $horarios_reservados = [];
    while ($row = $agendados->fetch_assoc()) {
        $horarios_reservados[] = $row['hora'];
    }
    
    $horarios_disponiveis = array_diff($horarios, $horarios_reservados);
    
    echo json_encode(['horarios' => array_values($horarios_disponiveis)]);
}

// Lembretes de agendamento (próximos 3 dias)
elseif ($acao == 'proximos' && $metodo == 'GET') {
    $sql = "SELECT a.*, c.nome as cliente_nome, c.telefone, p.nome as animal_nome, s.nome as servico_nome
            FROM agendamentos a
            JOIN clientes c ON a.cliente_id = c.id
            JOIN animais p ON a.animal_id = p.id
            JOIN servicos s ON a.servico_id = s.id
            WHERE DATE(a.data_agendamento) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)
            AND a.status != 'cancelado'
            ORDER BY a.data_agendamento ASC";
    
    $result = $conn->query($sql);
    $agendamentos = [];
    
    while ($row = $result->fetch_assoc()) {
        $agendamentos[] = $row;
    }
    
    echo json_encode(['agendamentos' => $agendamentos]);
}

else {
    echo json_encode(['erro' => 'Ação não encontrada']);
}

$conn->close();
?>
