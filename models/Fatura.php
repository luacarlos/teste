<?php
require_once __DIR__ . '/../config/Database.php';

class Fatura {
    private $db;
    private $table = 'faturas';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $query = "SELECT f.*, c.nome as cliente_nome FROM {$this->table} f 
                  LEFT JOIN clientes c ON f.cliente_id = c.id ORDER BY f.created_at DESC";
        $result = $this->db->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById($id) {
        $id = escapeSql($id);
        $query = "SELECT f.*, c.nome as cliente_nome FROM {$this->table} f 
                  LEFT JOIN clientes c ON f.cliente_id = c.id WHERE f.id = '$id'";
        $result = $this->db->query($query);
        return $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    public function create($data) {
        $cliente_id = escapeSql($data['cliente_id']);
        $agendamento_id = escapeSql($data['agendamento_id'] ?? null);
        $valor_total = escapeSql($data['valor_total']);
        $data_vencimento = escapeSql($data['data_vencimento'] ?? date('Y-m-d', strtotime('+7 days')));

        $query = "INSERT INTO {$this->table} (cliente_id, agendamento_id, valor_total, data_vencimento) 
                  VALUES ('$cliente_id', '$agendamento_id', '$valor_total', '$data_vencimento')";
        
        return $this->db->query($query) ? $this->db->insert_id : false;
    }

    public function update($id, $data) {
        $id = escapeSql($id);
        $status = escapeSql($data['status'] ?? 'pendente');
        $data_pagamento = $data['status'] === 'pago' ? date('Y-m-d') : null;

        $query = "UPDATE {$this->table} SET status='$status', data_pagamento='$data_pagamento' WHERE id='$id'";
        
        return $this->db->query($query);
    }

    public function delete($id) {
        $id = escapeSql($id);
        $query = "DELETE FROM {$this->table} WHERE id = '$id'";
        return $this->db->query($query);
    }

    public function getRevenueMonth($ano, $mes) {
        $ano = escapeSql($ano);
        $mes = escapeSql($mes);
        $query = "SELECT SUM(valor_total) as total FROM {$this->table} 
                  WHERE YEAR(created_at) = '$ano' AND MONTH(created_at) = '$mes' AND status = 'pago'";
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }
}
?>
