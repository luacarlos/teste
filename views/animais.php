<?php
require_once __DIR__ . '/../controllers/AnimalController.php';
require_once __DIR__ . '/../controllers/ClienteController.php';

$animalController = new AnimalController();
$clienteController = new ClienteController();
$animais = $animalController->index();
$clientes = $clienteController->index();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        $acao = sanitizeInput($_POST['acao']);
        
        if ($acao === 'criar') {
            $data = [
                'cliente_id' => sanitizeInput($_POST['cliente_id']),
                'nome' => sanitizeInput($_POST['nome']),
                'raca' => sanitizeInput($_POST['raca']),
                'tipo' => sanitizeInput($_POST['tipo']),
                'data_nascimento' => sanitizeInput($_POST['data_nascimento']),
                'peso' => sanitizeInput($_POST['peso'])
            ];
            $animalController->store($data);
            header('Location: ?page=animais');
            exit;
        }
    }
}
?>

<div class="page-header">
    <h3>Gerenciar Pets</h3>
    <button class="btn btn-primary" onclick="abrirModalAnimal()">Novo Pet</button>
</div>

<div class="pets-grid">
    <?php if (!empty($animais)): ?>
        <?php foreach ($animais as $animal): ?>
            <div class="pet-card">
                <h4><?php echo htmlspecialchars($animal['nome']); ?></h4>
                <p><strong>Tipo:</strong> <?php echo htmlspecialchars($animal['tipo'] ?? '-'); ?></p>
                <p><strong>Raça:</strong> <?php echo htmlspecialchars($animal['raca'] ?? '-'); ?></p>
                <p><strong>Proprietário:</strong> <?php echo htmlspecialchars($animal['cliente_nome'] ?? '-'); ?></p>
                <?php if ($animal['data_nascimento']): ?>
                    <p><strong>Nascimento:</strong> <?php echo date('d/m/Y', strtotime($animal['data_nascimento'])); ?></p>
                <?php endif; ?>
                <p><strong>Peso:</strong> <?php echo htmlspecialchars($animal['peso'] ?? '-'); ?> kg</p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center">Nenhum pet cadastrado</p>
    <?php endif; ?>
</div>

<!-- Modal de Animal -->
<div id="animalModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Novo Pet</h3>
            <button class="btn-close" onclick="fecharModalAnimal()">&times;</button>
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
                <label>Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Tipo</label>
                <select name="tipo" class="form-control" required>
                    <option value="cachorro">Cachorro</option>
                    <option value="gato">Gato</option>
                    <option value="passaro">Pássaro</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
            <div class="form-group">
                <label>Raça</label>
                <input type="text" name="raca" class="form-control">
            </div>
            <div class="form-group">
                <label>Data de Nascimento</label>
                <input type="date" name="data_nascimento" class="form-control">
            </div>
            <div class="form-group">
                <label>Peso (kg)</label>
                <input type="number" name="peso" step="0.1" class="form-control">
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModalAnimal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalAnimal() {
    document.getElementById('animalModal').classList.add('show');
}

function fecharModalAnimal() {
    document.getElementById('animalModal').classList.remove('show');
}
</script>
