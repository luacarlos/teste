<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../controllers/AgendamentoController.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autenticado']);
    exit;
}

$controller = new AgendamentoController();
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? sanitizeInput($_GET['action']) : null;

try {
    if ($method === 'GET') {
        if ($action === 'byDate' && isset($_GET['data'])) {
            echo json_encode(['success' => true, 'data' => $controller->getByDate($_GET['data'])]);
        }
    } elseif ($method === 'POST' && $action === 'store') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $controller->store($data);
        echo json_encode(['success' => true, 'id' => $id]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
