<?php
$current_page = isset($_GET['page']) ? sanitizeInput($_GET['page']) : 'dashboard';
$page_titles = [
    'dashboard' => 'Painel de Controle',
    'clientes' => 'Gerenciar Clientes',
    'animais' => 'Gerenciar Pets',
    'agendamentos' => 'Agendamentos',
    'servicos' => 'Serviços Oferecidos',
    'faturas' => 'Faturas',
    'relatorios' => 'Relatórios e Análises'
];
$page_title = $page_titles[$current_page] ?? 'Painel de Controle';
?>
<header class="top-header">
    <div class="header-left">
        <h2><?php echo $page_title; ?></h2>
    </div>
    <div class="header-right">
        <input type="text" class="search-box" id="searchBox" placeholder="Pesquisar...">
        <button class="btn-icon" onclick="toggleNotifications()">🔔</button>
        <button class="btn-icon" onclick="toggleUserMenu()">👤</button>
    </div>
</header>
