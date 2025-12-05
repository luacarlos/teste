<?php
// Arquivo de configuração centralizado
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'petshop_crm');
define('DB_PORT', 3306);

// Configurações de aplicação
define('APP_NAME', 'PetShop CRM');
define('APP_URL', 'http://localhost');
define('APP_ROOT', __DIR__ . '/..');

// Cores da paleta
define('COLOR_PRIMARY', '#0ca5b0');
define('COLOR_SECONDARY', '#4e3f30');
define('COLOR_LIGHT_CREAM', '#fefeeb');
define('COLOR_CREAM', '#f8f4e4');
define('COLOR_SAGE', '#a5b3aa');

// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
