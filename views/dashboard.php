<?php
require_once __DIR__ . '/../controllers/DashboardController.php';

$controller = new DashboardController();
$stats = $controller->getStats();
?>

<div class="page-header">
    <h3>Painel de Controle</h3>
</div>

<div class="dashboard-grid">
    <div class="stats-card">
        <h3>Total de Clientes</h3>
        <p class="stat-value"><?php echo $stats['total_clientes']; ?></p>
    </div>
    <div class="stats-card">
        <h3>Total de Pets</h3>
        <p class="stat-value"><?php echo $stats['total_animais']; ?></p>
    </div>
    <div class="stats-card">
        <h3>Agendamentos Hoje</h3>
        <p class="stat-value"><?php echo $stats['agendamentos_hoje']; ?></p>
    </div>
    <div class="stats-card">
        <h3>Receita do Mês</h3>
        <p class="stat-value">R$ <?php echo number_format($stats['receita_mes'], 2, ',', '.'); ?></p>
    </div>
</div>

<div class="card full-width">
    <h3>Próximos Agendamentos</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Pet</th>
                <th>Serviço</th>
                <th>Data/Hora</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($stats['agendamentos_proximos'])): ?>
                <?php foreach ($stats['agendamentos_proximos'] as $agend): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($agend['cliente_nome']); ?></td>
                        <td><?php echo htmlspecialchars($agend['animal_nome']); ?></td>
                        <td><?php echo htmlspecialchars($agend['servico_nome']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($agend['data_agendamento'])); ?></td>
                        <td>
                            <button class="btn btn-primary btn-small">Editar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Nenhum agendamento próximo</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
