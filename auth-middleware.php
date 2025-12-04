<?php
// Middleware de autenticação reutilizável
function verificarPermissao($permissao_necessaria = 'usuario') {
    if (!isset($_SESSION['usuario_id'])) {
        http_response_code(401);
        echo json_encode(['erro' => 'Não autenticado']);
        exit();
    }
    
    // Se for requisição AJAX, retornar JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        return true;
    }
    
    return true;
}

function obterUsuarioAtual() {
    global $conn;
    if (!isset($_SESSION['usuario_id'])) {
        return null;
    }
    
    $usuario_id = $_SESSION['usuario_id'];
    $result = $conn->query("SELECT id, nome, email, created_at FROM usuarios WHERE id = $usuario_id");
    return $result->fetch_assoc();
}

// Função para registrar atividades de login
function registrarAtividade($usuario_id, $acao, $descricao = '') {
    global $conn;
    $acao = $conn->real_escape_string($acao);
    $descricao = $conn->real_escape_string($descricao);
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $conn->query("INSERT INTO atividades_usuario (usuario_id, acao, descricao, ip_address) VALUES ($usuario_id, '$acao', '$descricao', '$ip')");
}
?>
