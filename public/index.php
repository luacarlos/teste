<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Auth.php';

// Verificar autenticação
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page = isset($_GET['page']) ? sanitizeInput($_GET['page']) : 'dashboard';
$allowed_pages = ['dashboard', 'clientes', 'animais', 'agendamentos', 'servicos', 'faturas', 'relatorios'];

if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Sistema de Gestão</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container">
        <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php require_once __DIR__ . '/../includes/header.php'; ?>
            
            <div class="pages-container">
                <?php 
                $view_path = __DIR__ . '/../views/' . $page . '.php';
                if (file_exists($view_path)) {
                    require_once $view_path;
                } else {
                    require_once __DIR__ . '/../views/dashboard.php';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Adicionando scripts necessários -->
    <script src="/assets/js/utils.js"></script>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/api.js"></script>
</body>
</html>
