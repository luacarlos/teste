<?php
// API específica para gestão de clientes
include 'config.php';
include 'auth-middleware.php';

verificarPermissao();
header('Content-Type: application/json; charset=utf-8');

$metodo = $_SERVER['REQUEST_METHOD'];
$acao = isset($_GET['acao']) ? $_GET['acao'] : '';

// Obter todos os clientes com informações adicionais
if ($acao == 'listar' && $metodo == 'GET') {
    $pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
    $limite = 10;
    $offset = ($pagina - 1) * $limite;
    $busca = isset($_GET['busca']) ? escapar($_GET['busca']) : '';
    
    $sql = "SELECT * FROM clientes WHERE nome LIKE '%$busca%' OR email LIKE '%$busca%' OR telefone LIKE '%$busca%' ORDER BY nome LIMIT $limite OFFSET $offset";
    $result = $conn->query($sql);
    
    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $total_pets = $conn->query("SELECT COUNT(*) as total FROM animais WHERE cliente_id = " . $row['id'])->fetch_assoc();
        $row['total_pets'] = $total_pets['total'];
        $clientes[] = $row;
    }
    
    $total_result = $conn->query("SELECT COUNT(*) as total FROM clientes WHERE nome LIKE '%$busca%' OR email LIKE '%$busca%' OR telefone LIKE '%$busca%'");
    $total = $total_result->fetch_assoc()['total'];
    
    echo json_encode([
        'sucesso' => true,
        'clientes' => $clientes,
        'total' => $total,
        'paginas' => ceil($total / $limite)
    ]);
}

// Criar novo cliente
elseif ($acao == 'criar' && $metodo == 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    
    $nome = escapar($dados['nome']);
    $email = escapar($dados['email']);
    $telefone = escapar($dados['telefone']);
    $endereco = escapar($dados['endereco']);
    $cidade = escapar($dados['cidade']);
    
    // Validar email único
    $check = $conn->query("SELECT id FROM clientes WHERE email = '$email'");
    if ($check->num_rows > 0 && !empty($email)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Email já cadastrado']);
        exit();
    }
    
    $sql = "INSERT INTO clientes (nome, email, telefone, endereco, cidade) VALUES ('$nome', '$email', '$telefone', '$endereco', '$cidade')";
    
    if ($conn->query($sql)) {
        $id = $conn->insert_id;
        registrarAtividade($_SESSION['usuario_id'], 'cliente_criado', "Cliente: $nome");
        echo json_encode(['sucesso' => true, 'id' => $id, 'mensagem' => 'Cliente criado com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Atualizar cliente
elseif ($acao == 'atualizar' && $metodo == 'PUT') {
    $dados = json_decode(file_get_contents('php://input'), true);
    
    $id = intval($dados['id']);
    $nome = escapar($dados['nome']);
    $email = escapar($dados['email']);
    $telefone = escapar($dados['telefone']);
    $endereco = escapar($dados['endereco']);
    $cidade = escapar($dados['cidade']);
    
    // Validar email único
    $check = $conn->query("SELECT id FROM clientes WHERE email = '$email' AND id != $id");
    if ($check->num_rows > 0 && !empty($email)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Email já cadastrado']);
        exit();
    }
    
    $sql = "UPDATE clientes SET nome='$nome', email='$email', telefone='$telefone', endereco='$endereco', cidade='$cidade' WHERE id=$id";
    
    if ($conn->query($sql)) {
        registrarAtividade($_SESSION['usuario_id'], 'cliente_atualizado', "Cliente ID: $id");
        echo json_encode(['sucesso' => true, 'mensagem' => 'Cliente atualizado com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Obter cliente por ID
elseif ($acao == 'obter' && $metodo == 'GET') {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM clientes WHERE id = $id");
    
    if ($result->num_rows > 0) {
        $cliente = $result->fetch_assoc();
        $total_pets = $conn->query("SELECT COUNT(*) as total FROM animais WHERE cliente_id = $id")->fetch_assoc();
        $cliente['total_pets'] = $total_pets['total'];
        echo json_encode(['sucesso' => true, 'cliente' => $cliente]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => 'Cliente não encontrado']);
    }
}

// Deletar cliente
elseif ($acao == 'deletar' && $metodo == 'DELETE') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $id = intval($dados['id']);
    
    // Iniciar transação
    $conn->begin_transaction();
    
    try {
        // Deletar animais associados
        $conn->query("DELETE FROM agendamentos WHERE animal_id IN (SELECT id FROM animais WHERE cliente_id = $id)");
        $conn->query("DELETE FROM animais WHERE cliente_id = $id");
        
        // Deletar faturas associadas
        $conn->query("DELETE FROM faturas WHERE cliente_id = $id");
        
        // Deletar agendamentos associados
        $conn->query("DELETE FROM agendamentos WHERE cliente_id = $id");
        
        // Deletar cliente
        $conn->query("DELETE FROM clientes WHERE id = $id");
        
        $conn->commit();
        registrarAtividade($_SESSION['usuario_id'], 'cliente_deletado', "Cliente ID: $id");
        echo json_encode(['sucesso' => true, 'mensagem' => 'Cliente deletado com sucesso']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['sucesso' => false, 'erro' => 'Erro ao deletar cliente: ' . $e->getMessage()]);
    }
}

// Obter clientes para seleção
elseif ($acao == 'para_selecao' && $metodo == 'GET') {
    $result = $conn->query("SELECT id, nome FROM clientes ORDER BY nome");
    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
    echo json_encode($clientes);
}

// Estatísticas de cliente
elseif ($acao == 'estatisticas' && $metodo == 'GET') {
    $id = intval($_GET['id']);
    
    $total_agendamentos = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE cliente_id = $id")->fetch_assoc()['total'];
    $total_pets = $conn->query("SELECT COUNT(*) as total FROM animais WHERE cliente_id = $id")->fetch_assoc()['total'];
    $gasto_total = $conn->query("SELECT SUM(valor_total) as total FROM faturas WHERE cliente_id = $id AND status = 'pago'")->fetch_assoc()['total'] ?? 0;
    
    echo json_encode([
        'total_agendamentos' => $total_agendamentos,
        'total_pets' => $total_pets,
        'gasto_total' => formatarMoeda($gasto_total)
    ]);
}

else {
    echo json_encode(['erro' => 'Ação não encontrada']);
}

$conn->close();
?>
