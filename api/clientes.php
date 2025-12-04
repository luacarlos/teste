<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/Database.php';

$db = Database::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = escapeSql($_POST['nome'] ?? '');
    $email = escapeSql($_POST['email'] ?? '');
    $telefone = escapeSql($_POST['telefone'] ?? '');
    $cidade = escapeSql($_POST['cidade'] ?? '');

    $query = "INSERT INTO clientes (nome, email, telefone, cidade, data_criacao) VALUES ('$nome', '$email', '$telefone', '$cidade', NOW())";
    
    if ($db->query($query)) {
        echo json_encode(['success' => true, 'message' => 'Cliente criado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => $db->error]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    
    $query = "DELETE FROM clientes WHERE id = $id";
    
    if ($db->query($query)) {
        echo json_encode(['success' => true, 'message' => 'Cliente deletado']);
    } else {
        echo json_encode(['success' => false, 'message' => $db->error]);
    }
    exit;
}

$clientes = $db->query("SELECT * FROM clientes");
echo json_encode($clientes->fetch_all(MYSQLI_ASSOC));
