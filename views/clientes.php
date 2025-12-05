<?php
require_once __DIR__ . '/../controllers/ClienteController.php';

$controller = new ClienteController();
$clientes = $controller->index();

// Processar ações de POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        $acao = sanitizeInput($_POST['acao']);
        
        if ($acao === 'criar') {
            $data = [
                'nome' => sanitizeInput($_POST['nome']),
                'email' => sanitizeInput($_POST['email']),
                'telefone' => sanitizeInput($_POST['telefone']),
                'endereco' => sanitizeInput($_POST['endereco']),
                'cidade' => sanitizeInput($_POST['cidade'])
            ];
            $controller->store($data);
            header('Location: ?page=clientes');
            exit;
        } elseif ($acao === 'atualizar') {
            $data = [
                'nome' => sanitizeInput($_POST['nome']),
                'email' => sanitizeInput($_POST['email']),
                'telefone' => sanitizeInput($_POST['telefone']),
                'endereco' => sanitizeInput($_POST['endereco']),
                'cidade' => sanitizeInput($_POST['cidade'])
            ];
            $controller->update($_POST['id'], $data);
            header('Location: ?page=clientes');
            exit;
        } elseif ($acao === 'deletar') {
            $controller->destroy($_POST['id']);
            header('Location: ?page=clientes');
            exit;
        }
    }
}
?>

<div class="page-header">
    <h3>Gerenciar Clientes</h3>
    <button class="btn btn-primary" onclick="abrirModalCliente()">Novo Cliente</button>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th>Email</th>
                <th>Cidade</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($clientes)): ?>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($cliente['nome']); ?></strong></td>
                        <td><?php echo htmlspecialchars($cliente['telefone'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($cliente['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($cliente['cidade'] ?? '-'); ?></td>
                        <td>
                            <button class="btn btn-primary btn-small" onclick="editarCliente(<?php echo $cliente['id']; ?>)">Editar</button>
                            <button class="btn btn-secondary btn-small" onclick="deletarCliente(<?php echo $cliente['id']; ?>)">Deletar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Nenhum cliente cadastrado</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Cliente -->
<div id="clienteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Novo Cliente</h3>
            <button class="btn-close" onclick="fecharModalCliente()">&times;</button>
        </div>
        <form method="POST" class="modal-body">
            <input type="hidden" name="acao" value="criar">
            <input type="hidden" name="id" id="clienteId">
            
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label>Telefone</label>
                <input type="tel" name="telefone" class="form-control">
            </div>
            <div class="form-group">
                <label>Endereço</label>
                <input type="text" name="endereco" class="form-control">
            </div>
            <div class="form-group">
                <label>Cidade</label>
                <input type="text" name="cidade" class="form-control">
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModalCliente()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalCliente() {
    document.getElementById('clienteModal').classList.add('show');
}

function fecharModalCliente() {
    document.getElementById('clienteModal').classList.remove('show');
}

function editarCliente(id) {
    alert('Função de edição será implementada em breve!');
}

function deletarCliente(id) {
    if (confirm('Tem certeza que deseja deletar este cliente?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="acao" value="deletar"><input type="hidden" name="id" value="' + id + '">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
