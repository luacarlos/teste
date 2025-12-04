<?php
include 'config.php';

// Obter dados do banco para inicializar a aplicação
$clientes = $conn->query("SELECT * FROM clientes ORDER BY nome")->fetch_all(MYSQLI_ASSOC);
$animais = $conn->query("SELECT * FROM animais ORDER BY nome")->fetch_all(MYSQLI_ASSOC);
$servicos = $conn->query("SELECT * FROM servicos WHERE ativo=TRUE ORDER BY nome")->fetch_all(MYSQLI_ASSOC);
$agendamentos = $conn->query("SELECT * FROM agendamentos ORDER BY data_agendamento DESC")->fetch_all(MYSQLI_ASSOC);
$faturas = $conn->query("SELECT * FROM faturas ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<script>
// Dados iniciais do banco de dados
const appData = {
    customers: <?php echo json_encode($clientes); ?>,
    pets: <?php echo json_encode($animais); ?>,
    appointments: <?php echo json_encode($agendamentos); ?>,
    services: <?php echo json_encode($servicos); ?>,
    invoices: <?php echo json_encode($faturas); ?>,
}

// Função para mapear dados do PHP ao formato do JS
function mapearDadosPhp() {
    appData.customers = appData.customers.map(c => ({
        id: c.id,
        name: c.nome,
        phone: c.telefone,
        email: c.email,
        address: c.endereco
    }))
    
    appData.pets = appData.pets.map(p => ({
        id: p.id,
        customer_id: p.cliente_id,
        name: p.nome,
        breed: p.raca,
        type: p.tipo,
        birthdate: p.data_nascimento
    }))
    
    appData.services = appData.services.map(s => ({
        id: s.id,
        name: s.nome,
        description: s.descricao,
        price: s.preco,
        duration: s.duracao_minutos
    }))
    
    appData.appointments = appData.appointments.map(a => ({
        id: a.id,
        customer_id: a.cliente_id,
        pet_id: a.animal_id,
        service_id: a.servico_id,
        date: a.data_agendamento.split(' ')[0],
        time: a.data_agendamento.split(' ')[1],
        notes: a.observacoes,
        status: a.status
    }))
    
    appData.invoices = appData.invoices.map(i => ({
        id: i.id,
        customer_id: i.cliente_id,
        date: i.created_at.split(' ')[0],
        description: 'Fatura #' + i.id,
        total: i.valor_total,
        status: i.status === 'pago' ? 'paid' : 'pending'
    }))
}

mapearDadosPhp()
</script>
