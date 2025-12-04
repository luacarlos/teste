<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-brand">
            <h1>🐾 PetShop CRM</h1>
        </div>
        <div class="navbar-menu">
            <a href="/public/index.php?page=dashboard" class="nav-link">Painel</a>
            <a href="/public/index.php?page=clientes" class="nav-link">Clientes</a>
            <a href="/public/index.php?page=animais" class="nav-link">Animais</a>
            <a href="/public/index.php?page=agendamentos" class="nav-link">Agendamentos</a>
            <a href="/public/index.php?page=servicos" class="nav-link">Serviços</a>
            <a href="/public/index.php?page=faturas" class="nav-link">Faturas</a>
            <a href="/public/index.php?page=relatorios" class="nav-link">Relatórios</a>
        </div>
        <div class="navbar-user">
            <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuário'); ?></span>
            <a href="/public/logout.php" class="btn btn-small">Sair</a>
        </div>
    </div>
</nav>
