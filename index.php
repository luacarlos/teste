<?php
include 'config.php';
verificarAutenticacao();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetShop CRM - Sistema de Gestão</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1 class="logo">🐾 PetShop CRM</h1>
            </div>
            <nav class="sidebar-nav">
                <a href="#" class="nav-item active" data-page="dashboard">
                    <span class="icon">📊</span>
                    <span>Painel</span>
                </a>
                <a href="#" class="nav-item" data-page="customers">
                    <span class="icon">👥</span>
                    <span>Clientes</span>
                </a>
                <a href="#" class="nav-item" data-page="pets">
                    <span class="icon">🐕</span>
                    <span>Pets</span>
                </a>
                <a href="#" class="nav-item" data-page="appointments">
                    <span class="icon">📅</span>
                    <span>Agendamentos</span>
                </a>
                <a href="#" class="nav-item" data-page="services">
                    <span class="icon">✂️</span>
                    <span>Serviços</span>
                </a>
                <a href="#" class="nav-item" data-page="invoices">
                    <span class="icon">💰</span>
                    <span>Faturas</span>
                </a>
                <a href="#" class="nav-item" data-page="reports">
                    <span class="icon">📈</span>
                    <span>Relatórios</span>
                </a>
                <a href="logout.php" class="nav-item" style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 16px;">
                    <span class="icon">🚪</span>
                    <span>Sair</span>
                </a>
            </nav>
        </aside>

        <!-- Pets Page -->
        <section class="content" id="pets">
            <h2>Pets</h2>
            <form id="petForm">
                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" class="form-control" name="name" placeholder="Nome do Pet">
                </div>
                <div class="form-group">
                    <label>Espécie</label>
                    <input type="text" class="form-control" name="species" placeholder="Espécie">
                </div>
                <div class="form-group">
                    <label>Raça</label>
                    <input type="text" class="form-control" name="breed" placeholder="Raça">
                </div>
                <div class="form-group">
                    <label>Peso (kg)</label>
                    <input type="number" class="form-control" name="weight" step="0.1" placeholder="Ex: 5.5">
                </div>
                <!-- ... rest of code here ... -->
            </form>
        </section>

    </div>

    <script src="app.php"></script>
    <script src="app.js"></script>
</body>
</html>
