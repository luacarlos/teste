<?php
header('Content-Type: application/json; charset=utf-8');
include 'config.php';

verificarAutenticacao();

$metodo = $_SERVER['REQUEST_METHOD'];
$acao = isset($_GET['acao']) ? $_GET['acao'] : '';

// Clientes
if ($acao == 'obter_clientes') {
    $result = $conn->query("SELECT * FROM clientes ORDER BY nome");
    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
    echo json_encode($clientes);
}

elseif ($acao == 'criar_cliente' && $metodo == 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $nome = escapar($dados['nome']);
    $email = escapar($dados['email']);
    $telefone = escapar($dados['telefone']);
    $endereco = escapar($dados['endereco']);
    $cidade = escapar($dados['cidade']);
    
    $sql = "INSERT INTO clientes (nome, email, telefone, endereco, cidade) VALUES ('$nome', '$email', '$telefone', '$endereco', '$cidade')";
    
    if ($conn->query($sql)) {
        echo json_encode(['sucesso' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

elseif ($acao == 'atualizar_cliente' && $metodo == 'PUT') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $id = escapar($dados['id']);
    $nome = escapar($dados['nome']);
    $email = escapar($dados['email']);
    $telefone = escapar($dados['telefone']);
    $endereco = escapar($dados['endereco']);
    $cidade = escapar($dados['cidade']);
    
    $sql = "UPDATE clientes SET nome='$nome', email='$email', telefone='$telefone', endereco='$endereco', cidade='$cidade' WHERE id=$id";
    
    if ($conn->query($sql)) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

elseif ($acao == 'deletar_cliente' && $metodo == 'DELETE') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $id = escapar($dados['id']);
    
    $sql = "DELETE FROM clientes WHERE id=$id";
    
    if ($conn->query($sql)) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Animais
elseif ($acao == 'obter_animais') {
    $result = $conn->query("SELECT * FROM animais ORDER BY nome");
    $animais = [];
    while ($row = $result->fetch_assoc()) {
        $animais[] = $row;
    }
    echo json_encode($animais);
}

elseif ($acao == 'criar_animal' && $metodo == 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $cliente_id = escapar($dados['cliente_id']);
    $nome = escapar($dados['nome']);
    $raca = escapar($dados['raca']);
    $tipo = escapar($dados['tipo']);
    $data_nascimento = escapar($dados['data_nascimento']);
    $peso = escapar($dados['peso']);
    
    $sql = "INSERT INTO animais (cliente_id, nome, raca, tipo, data_nascimento, peso) VALUES ($cliente_id, '$nome', '$raca', '$tipo', '$data_nascimento', $peso)";
    
    if ($conn->query($sql)) {
        echo json_encode(['sucesso' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Serviços
elseif ($acao == 'obter_servicos') {
    $result = $conn->query("SELECT * FROM servicos WHERE ativo=TRUE ORDER BY nome");
    $servicos = [];
    while ($row = $result->fetch_assoc()) {
        $servicos[] = $row;
    }
    echo json_encode($servicos);
}

elseif ($acao == 'criar_servico' && $metodo == 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $nome = escapar($dados['nome']);
    $descricao = escapar($dados['descricao']);
    $preco = escapar($dados['preco']);
    $duracao_minutos = escapar($dados['duracao_minutos']);
    
    $sql = "INSERT INTO servicos (nome, descricao, preco, duracao_minutos) VALUES ('$nome', '$descricao', $preco, $duracao_minutos)";
    
    if ($conn->query($sql)) {
        echo json_encode(['sucesso' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Agendamentos
elseif ($acao == 'obter_agendamentos') {
    $result = $conn->query("SELECT * FROM agendamentos ORDER BY data_agendamento DESC");
    $agendamentos = [];
    while ($row = $result->fetch_assoc()) {
        $agendamentos[] = $row;
    }
    echo json_encode($agendamentos);
}

elseif ($acao == 'criar_agendamento' && $metodo == 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $cliente_id = escapar($dados['cliente_id']);
    $animal_id = escapar($dados['animal_id']);
    $servico_id = escapar($dados['servico_id']);
    $data_agendamento = escapar($dados['data_agendamento']);
    $observacoes = escapar($dados['observacoes']);
    
    $sql = "INSERT INTO agendamentos (cliente_id, animal_id, servico_id, data_agendamento, observacoes) VALUES ($cliente_id, $animal_id, $servico_id, '$data_agendamento', '$observacoes')";
    
    if ($conn->query($sql)) {
        echo json_encode(['sucesso' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Faturas
elseif ($acao == 'obter_faturas') {
    $result = $conn->query("SELECT * FROM faturas ORDER BY created_at DESC");
    $faturas = [];
    while ($row = $result->fetch_assoc()) {
        $faturas[] = $row;
    }
    echo json_encode($faturas);
}

elseif ($acao == 'criar_fatura' && $metodo == 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $agendamento_id = isset($dados['agendamento_id']) ? escapar($dados['agendamento_id']) : 'NULL';
    $cliente_id = escapar($dados['cliente_id']);
    $valor_total = escapar($dados['valor_total']);
    $data_vencimento = escapar($dados['data_vencimento']);
    $status = escapar($dados['status'] ?? 'pendente');
    
    $sql = "INSERT INTO faturas (agendamento_id, cliente_id, valor_total, data_vencimento, status) VALUES ($agendamento_id, $cliente_id, $valor_total, '$data_vencimento', '$status')";
    
    if ($conn->query($sql)) {
        echo json_encode(['sucesso' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Relatórios
elseif ($acao == 'relatorio_receita') {
    $mes = isset($_GET['mes']) ? escapar($_GET['mes']) : date('Y-m');
    $result = $conn->query("SELECT SUM(valor_total) as total FROM faturas WHERE status='pago' AND DATE_FORMAT(created_at, '%Y-%m') = '$mes'");
    $row = $result->fetch_assoc();
    echo json_encode(['receita_mes' => formatarMoeda($row['total'] ?? 0)]);
}

elseif ($acao == 'relatorio_servicos') {
    $result = $conn->query("SELECT s.nome, COUNT(a.id) as total FROM agendamentos a JOIN servicos s ON a.servico_id = s.id GROUP BY s.id ORDER BY total DESC LIMIT 5");
    $servicos = [];
    while ($row = $result->fetch_assoc()) {
        $servicos[] = $row;
    }
    echo json_encode($servicos);
}

elseif ($acao == 'relatorio_clientes') {
    $resultado = $conn->query("SELECT COUNT(*) as total FROM clientes");
    $row = $resultado->fetch_assoc();
    echo json_encode(['total_clientes' => $row['total']]);
}

else {
    echo json_encode(['erro' => 'Ação não encontrada']);
}

$conn->close();
?>
