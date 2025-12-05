<?php
require_once __DIR__ . '/../controllers/FaturaController.php';
require_once __DIR__ . '/../controllers/ClienteController.php';

$faturaController = new FaturaController();
$clienteController = new ClienteController();
$faturas = $faturaController->index();
$clientes = $clienteController->index();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = sanitizeInput($_POST['acao']);
    
    if ($acao === 'criar') {
        $data = [
            'cliente_id' => sanitizeInput($_POST['cliente_id']),
            'valor_total' => sanitizeInput($_POST['valor_total']),
            'data_vencimento' => sanitizeInput($_POST['data_vencimento']),
            'status' => 'pendente'
        ];
        $faturaController->store($data);
        header('Location: ?page=faturas');
        exit;
    }
}
?>

<div class="page-header">
    <h3>Gerenciar Faturas</h3>
    <button class="btn btn-primary" onclick="abrirModalFatura()">Nova Fatura</button>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Nº Fatura</th>
                <th>Cliente</th>
                <th>Valor</th>
                <th>Vencimento</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($faturas)): ?>
                <?php foreach ($faturas as $fatura): ?>
                    <tr>
                        <td><strong>#<?php echo $fatura['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($fatura['cliente_nome']); ?></td>
                        <td>R$ <?php echo number_format($fatura['valor_total'], 2, ',', '.'); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($fatura['data_vencimento'])); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($fatura['status'])); ?></td>
                        <td>
                            <button class="btn btn-primary btn-small">Editar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Nenhuma fatura registrada</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Fatura -->
<div id="faturaModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Nova Fatura</h3>
            <button class="btn-close" onclick="fecharModalFatura()">&times;</button>
        </div>
        <form method="POST" class="modal-body">
            <input type="hidden" name="acao" value="criar">
            
            <div class="form-group">
                <label>Cliente</label>
                <select name="cliente_id" class="form-control" required>
                    <option value="">Selecione um cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?php echo $cliente['id']; ?>"><?php echo htmlspecialchars($cliente['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Valor Total (R$)</label>
                <input type="number" name="valor_total" step="0.01" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Data de Vencimento</label>
                <input type="date" name="data_vencimento" class="form-control" required>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModalFatura()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalFatura() {
    document.getElementById('faturaModal').classList.add('show');
}

function fecharModalFatura() {
    document.getElementById('faturaModal').classList.remove('show');
}
</script>
