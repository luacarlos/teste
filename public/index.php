<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Auth.php';

// Se não está autenticado, redireciona para login
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<main class="main-content">
    <?php
    $page = isset($_GET['page']) ? sanitizeInput($_GET['page']) : 'dashboard';
    
    $allowed_pages = ['dashboard', 'clientes', 'animais', 'agendamentos', 'servicos', 'faturas', 'relatorios'];
    
    if (in_array($page, $allowed_pages)) {
        include __DIR__ . '/../pages/' . $page . '.php';
    } else {
        include __DIR__ . '/../pages/dashboard.php';
    }
    ?>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
