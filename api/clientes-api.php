<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../controllers/ClienteController.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autenticado']);
    exit;
}

$controller = new ClienteController();
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? sanitizeInput($_GET['action']) : null;

try {
    if ($method === 'GET') {
        if ($action === 'list') {
            echo json_encode(['success' => true, 'data' => $controller->index()]);
        } elseif ($action === 'show' && isset($_GET['id'])) {
            $cliente = $controller->show($_GET['id']);
            echo json_encode(['success' => true, 'data' => $cliente]);
        }
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($action === 'store') {
            $id = $controller->store($data);
            echo json_encode(['success' => true, 'id' => $id]);
        } elseif ($action === 'update' && isset($_GET['id'])) {
            $controller->update($_GET['id'], $data);
            echo json_encode(['success' => true]);
        }
    } elseif ($method === 'DELETE' && isset($_GET['id'])) {
        $controller->destroy($_GET['id']);
        echo json_encode(['success' => true]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
