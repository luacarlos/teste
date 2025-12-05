<?php
require_once __DIR__ . '/../controllers/ServicoController.php';

$controller = new ServicoController();
$servicos = $controller->index();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = sanitizeInput($_POST['acao']);
    
    if ($acao === 'criar') {
        $data = [
            'nome' => sanitizeInput($_POST['nome']),
            'descricao' => sanitizeInput($_POST['descricao']),
            'preco' => sanitizeInput($_POST['preco']),
            'duracao_minutos' => sanitizeInput($_POST['duracao_minutos'])
        ];
        $controller->store($data);
        header('Location: ?page=servicos');
        exit;
    } elseif ($acao === 'deletar') {
        $controller->destroy($_POST['id']);
        header('Location: ?page=servicos');
        exit;
    }
}
?>

<div class="page-header">
    <h3>Gerenciar Serviços</h3>
    <button class="btn btn-primary" onclick="abrirModalServico()">Novo Serviço</button>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Serviço</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Duração</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($servicos)): ?>
                <?php foreach ($servicos as $servico): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($servico['nome']); ?></strong></td>
                        <td><?php echo htmlspecialchars($servico['descricao'] ?? '-'); ?></td>
                        <td>R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo $servico['duracao_minutos']; ?> min</td>
                        <td>
                            <button class="btn btn-secondary btn-small" onclick="deletarServico(<?php echo $servico['id']; ?>)">Deletar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Nenhum serviço cadastrado</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Serviço -->
<div id="servicoModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Novo Serviço</h3>
            <button class="btn-close" onclick="fecharModalServico()">&times;</button>
        </div>
        <form method="POST" class="modal-body">
            <input type="hidden" name="acao" value="criar">
            
            <div class="form-group">
                <label>Nome do Serviço</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Descrição</label>
                <textarea name="descricao" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Preço (R$)</label>
                <input type="number" name="preco" step="0.01" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Duração (minutos)</label>
                <input type="number" name="duracao_minutos" class="form-control" value="30">
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModalServico()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalServico() {
    document.getElementById('servicoModal').classList.add('show');
}

function fecharModalServico() {
    document.getElementById('servicoModal').classList.remove('show');
}

function deletarServico(id) {
    if (confirm('Tem certeza que deseja deletar este serviço?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="acao" value="deletar"><input type="hidden" name="id" value="' + id + '">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
