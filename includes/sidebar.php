<?php
$current_page = isset($_GET['page']) ? sanitizeInput($_GET['page']) : 'dashboard';
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <h1 class="logo">🐾 PetShop CRM</h1>
    </div>
    <nav class="sidebar-nav">
        <a href="?page=dashboard" class="nav-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
            <span class="icon">📊</span>
            <span>Painel</span>
        </a>
        <a href="?page=clientes" class="nav-item <?php echo $current_page === 'clientes' ? 'active' : ''; ?>">
            <span class="icon">👥</span>
            <span>Clientes</span>
        </a>
        <a href="?page=animais" class="nav-item <?php echo $current_page === 'animais' ? 'active' : ''; ?>">
            <span class="icon">🐕</span>
            <span>Pets</span>
        </a>
        <a href="?page=agendamentos" class="nav-item <?php echo $current_page === 'agendamentos' ? 'active' : ''; ?>">
            <span class="icon">📅</span>
            <span>Agendamentos</span>
        </a>
        <a href="?page=servicos" class="nav-item <?php echo $current_page === 'servicos' ? 'active' : ''; ?>">
            <span class="icon">✂️</span>
            <span>Serviços</span>
        </a>
        <a href="?page=faturas" class="nav-item <?php echo $current_page === 'faturas' ? 'active' : ''; ?>">
            <span class="icon">💰</span>
            <span>Faturas</span>
        </a>
        <a href="?page=relatorios" class="nav-item <?php echo $current_page === 'relatorios' ? 'active' : ''; ?>">
            <span class="icon">📈</span>
            <span>Relatórios</span>
        </a>
        <a href="logout.php" class="nav-item" style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 16px;">
            <span class="icon">🚪</span>
            <span>Sair</span>
        </a>
    </nav>
</aside>
