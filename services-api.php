<?php
// API para gerenciamento de serviços
include 'config.php';
include 'auth-middleware.php';

verificarPermissao();
header('Content-Type: application/json; charset=utf-8');

$metodo = $_SERVER['REQUEST_METHOD'];
$acao = isset($_GET['acao']) ? $_GET['acao'] : '';

// Listar todos os serviços ativos
if ($acao == 'listar' && $metodo == 'GET') {
    $sql = "SELECT * FROM servicos WHERE ativo = TRUE ORDER BY nome";
    $result = $conn->query($sql);
    $servicos = [];
    
    while ($row = $result->fetch_assoc()) {
        $servicos[] = $row;
    }
    
    echo json_encode(['sucesso' => true, 'servicos' => $servicos]);
}

// Criar novo serviço
elseif ($acao == 'criar' && $metodo == 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    
    $nome = escapar($dados['nome']);
    $descricao = escapar($dados['descricao'] ?? '');
    $preco = floatval($dados['preco']);
    $duracao_minutos = intval($dados['duracao_minutos']);
    
    $sql = "INSERT INTO servicos (nome, descricao, preco, duracao_minutos, ativo) 
            VALUES ('$nome', '$descricao', $preco, $duracao_minutos, TRUE)";
    
    if ($conn->query($sql)) {
        $id = $conn->insert_id;
        registrarAtividade($_SESSION['usuario_id'], 'servico_criado', "Serviço: $nome");
        echo json_encode(['sucesso' => true, 'id' => $id, 'mensagem' => 'Serviço criado com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Atualizar serviço
elseif ($acao == 'atualizar' && $metodo == 'PUT') {
    $dados = json_decode(file_get_contents('php://input'), true);
    
    $id = intval($dados['id']);
    $nome = escapar($dados['nome']);
    $descricao = escapar($dados['descricao'] ?? '');
    $preco = floatval($dados['preco']);
    $duracao_minutos = intval($dados['duracao_minutos']);
    
    $sql = "UPDATE servicos SET nome='$nome', descricao='$descricao', preco=$preco, duracao_minutos=$duracao_minutos WHERE id=$id";
    
    if ($conn->query($sql)) {
        registrarAtividade($_SESSION['usuario_id'], 'servico_atualizado', "Serviço ID: $id");
        echo json_encode(['sucesso' => true, 'mensagem' => 'Serviço atualizado com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Deletar serviço (soft delete - apenas marca como inativo)
elseif ($acao == 'deletar' && $metodo == 'DELETE') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $id = intval($dados['id']);
    
    $sql = "UPDATE servicos SET ativo = FALSE WHERE id = $id";
    
    if ($conn->query($sql)) {
        registrarAtividade($_SESSION['usuario_id'], 'servico_deletado', "Serviço ID: $id");
        echo json_encode(['sucesso' => true, 'mensagem' => 'Serviço deletado com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => $conn->error]);
    }
}

// Obter serviço por ID
elseif ($acao == 'obter' && $metodo == 'GET') {
    $id = intval($_GET['id']);
    
    $result = $conn->query("SELECT * FROM servicos WHERE id = $id");
    
    if ($result->num_rows > 0) {
        $servico = $result->fetch_assoc();
        echo json_encode(['sucesso' => true, 'servico' => $servico]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => 'Serviço não encontrado']);
    }
}

// Estatísticas de serviço
elseif ($acao == 'estatisticas' && $metodo == 'GET') {
    $sql = "SELECT s.id, s.nome, COUNT(a.id) as total_agendamentos, SUM(f.valor_total) as receita_total
            FROM servicos s
            LEFT JOIN agendamentos a ON s.id = a.servico_id
            LEFT JOIN faturas f ON a.id = f.agendamento_id AND f.status = 'pago'
            WHERE s.ativo = TRUE
            GROUP BY s.id
            ORDER BY total_agendamentos DESC
            LIMIT 10";
    
    $result = $conn->query($sql);
    $servicos = [];
    
    while ($row = $result->fetch_assoc()) {
        $row['receita_total'] = formatarMoeda($row['receita_total'] ?? 0);
        $servicos[] = $row;
    }
    
    echo json_encode(['servicos' => $servicos]);
}

else {
    echo json_encode(['erro' => 'Ação não encontrada']);
}

$conn->close();
?>
