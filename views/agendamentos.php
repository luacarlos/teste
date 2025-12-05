<?php
require_once __DIR__ . '/../controllers/AgendamentoController.php';
require_once __DIR__ . '/../controllers/ClienteController.php';
require_once __DIR__ . '/../controllers/AnimalController.php';
require_once __DIR__ . '/../controllers/ServicoController.php';

$agendamentoController = new AgendamentoController();
$clienteController = new ClienteController();
$animalController = new AnimalController();
$servicoController = new ServicoController();

$data_filtro = isset($_POST['data_filtro']) ? sanitizeInput($_POST['data_filtro']) : date('Y-m-d');
$agendamentos = $agendamentoController->getByDate($data_filtro);
$clientes = $clienteController->index();
$animais = $animalController->index();
$servicos = $servicoController->index();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = sanitizeInput($_POST['acao']);
    
    if ($acao === 'criar') {
        $data = [
            'cliente_id' => sanitizeInput($_POST['cliente_id']),
            'animal_id' => sanitizeInput($_POST['animal_id']),
            'servico_id' => sanitizeInput($_POST['servico_id']),
            'data_agendamento' => sanitizeInput($_POST['data_agendamento']) . ' ' . sanitizeInput($_POST['hora_agendamento']),
            'observacoes' => sanitizeInput($_POST['observacoes'])
        ];
        $agendamentoController->store($data);
        header('Location: ?page=agendamentos');
        exit;
    }
}
?>

<div class="page-header">
    <h3>Agendamentos</h3>
    <button class="btn btn-primary" onclick="abrirModalAgendamento()">Novo Agendamento</button>
</div>

<div class="card">
    <form method="POST" style="margin-bottom: 20px;">
        <label>Filtrar por data:</label>
        <input type="date" name="data_filtro" value="<?php echo $data_filtro; ?>" onchange="this.form.submit()">
    </form>
    
    <table class="table">
        <thead>
            <tr>
                <th>Hora</th>
                <th>Cliente</th>
                <th>Pet</th>
                <th>Serviço</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($agendamentos)): ?>
                <?php foreach ($agendamentos as $agend): ?>
                    <tr>
                        <td><strong><?php echo date('H:i', strtotime($agend['data_agendamento'])); ?></strong></td>
                        <td><?php echo htmlspecialchars($agend['cliente_nome']); ?></td>
                        <td><?php echo htmlspecialchars($agend['animal_nome']); ?></td>
                        <td><?php echo htmlspecialchars($agend['servico_nome']); ?></td>
                        <td><?php echo htmlspecialchars($agend['status']); ?></td>
                        <td>
                            <button class="btn btn-primary btn-small">Editar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Nenhum agendamento para <?php echo date('d/m/Y', strtotime($data_filtro)); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Agendamento -->
<div id="agendamentoModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Novo Agendamento</h3>
            <button class="btn-close" onclick="fecharModalAgendamento()">&times;</button>
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
                <label>Pet</label>
                <select name="animal_id" class="form-control" required>
                    <option value="">Selecione um pet</option>
                    <?php foreach ($animais as $animal): ?>
                        <option value="<?php echo $animal['id']; ?>"><?php echo htmlspecialchars($animal['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Serviço</label>
                <select name="servico_id" class="form-control" required>
                    <option value="">Selecione um serviço</option>
                    <?php foreach ($servicos as $servico): ?>
                        <option value="<?php echo $servico['id']; ?>"><?php echo htmlspecialchars($servico['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Data</label>
                <input type="date" name="data_agendamento" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Hora</label>
                <input type="time" name="hora_agendamento" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Observações</label>
                <textarea name="observacoes" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModalAgendamento()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalAgendamento() {
    document.getElementById('agendamentoModal').classList.add('show');
}

function fecharModalAgendamento() {
    document.getElementById('agendamentoModal').classList.remove('show');
}
</script>
