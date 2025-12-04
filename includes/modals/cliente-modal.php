<div id="clienteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Novo Cliente</h2>
            <button class="close-btn" onclick="closeClienteModal()">&times;</button>
        </div>
        <form id="clienteForm" onsubmit="saveCliente(event)">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone" required>
            </div>
            <div class="form-group">
                <label for="cidade">Cidade</label>
                <input type="text" id="cidade" name="cidade">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Salvar</button>
        </form>
    </div>
</div>

<script>
function openClienteModal() {
    document.getElementById('clienteModal').classList.add('active');
}

function closeClienteModal() {
    document.getElementById('clienteModal').classList.remove('active');
    document.getElementById('clienteForm').reset();
}

function saveCliente(event) {
    event.preventDefault();
    const formData = new FormData(document.getElementById('clienteForm'));
    
    fetch('/api/clientes.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cliente salvo com sucesso!');
            closeClienteModal();
            location.reload();
        } else {
            alert('Erro ao salvar cliente: ' + data.message);
        }
    });
}

function editCliente(id) {
    console.log('Editar cliente:', id);
}

function deleteCliente(id) {
    if (confirm('Tem certeza que deseja deletar este cliente?')) {
        fetch(`/api/clientes.php?action=delete&id=${id}`, { method: 'DELETE' })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cliente deletado com sucesso!');
                location.reload();
            }
        });
    }
}
</script>
