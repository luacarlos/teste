<?php
require_once __DIR__ . '/../config/Database.php';

class Agendamento {
    private $db;
    private $table = 'agendamentos';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $query = "SELECT a.*, c.nome as cliente_nome, an.nome as animal_nome, s.nome as servico_nome 
                  FROM {$this->table} a 
                  LEFT JOIN clientes c ON a.cliente_id = c.id 
                  LEFT JOIN animais an ON a.animal_id = an.id 
                  LEFT JOIN servicos s ON a.servico_id = s.id 
                  ORDER BY a.data_agendamento DESC";
        $result = $this->db->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById($id) {
        $id = escapeSql($id);
        $query = "SELECT a.*, c.nome as cliente_nome, an.nome as animal_nome, s.nome as servico_nome 
                  FROM {$this->table} a 
                  LEFT JOIN clientes c ON a.cliente_id = c.id 
                  LEFT JOIN animais an ON a.animal_id = an.id 
                  LEFT JOIN servicos s ON a.servico_id = s.id 
                  WHERE a.id = '$id'";
        $result = $this->db->query($query);
        return $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    public function getByDate($data) {
        $data = escapeSql($data);
        $query = "SELECT a.*, c.nome as cliente_nome, an.nome as animal_nome, s.nome as servico_nome 
                  FROM {$this->table} a 
                  LEFT JOIN clientes c ON a.cliente_id = c.id 
                  LEFT JOIN animais an ON a.animal_id = an.id 
                  LEFT JOIN servicos s ON a.servico_id = s.id 
                  WHERE DATE(a.data_agendamento) = '$data' 
                  ORDER BY a.data_agendamento ASC";
        $result = $this->db->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function create($data) {
        $cliente_id = escapeSql($data['cliente_id']);
        $animal_id = escapeSql($data['animal_id']);
        $servico_id = escapeSql($data['servico_id']);
        $data_agendamento = escapeSql($data['data_agendamento']);
        $observacoes = escapeSql($data['observacoes'] ?? '');

        $query = "INSERT INTO {$this->table} (cliente_id, animal_id, servico_id, data_agendamento, observacoes) 
                  VALUES ('$cliente_id', '$animal_id', '$servico_id', '$data_agendamento', '$observacoes')";
        
        return $this->db->query($query) ? $this->db->insert_id : false;
    }

    public function update($id, $data) {
        $id = escapeSql($id);
        $status = escapeSql($data['status'] ?? 'pendente');
        $observacoes = escapeSql($data['observacoes'] ?? '');

        $query = "UPDATE {$this->table} SET status='$status', observacoes='$observacoes' WHERE id='$id'";
        
        return $this->db->query($query);
    }

    public function delete($id) {
        $id = escapeSql($id);
        $query = "DELETE FROM {$this->table} WHERE id = '$id'";
        return $this->db->query($query);
    }

    public function countPorDia($data) {
        $data = escapeSql($data);
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE DATE(data_agendamento) = '$data'";
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>
