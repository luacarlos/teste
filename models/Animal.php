<?php
require_once __DIR__ . '/../config/Database.php';

class Animal {
    private $db;
    private $table = 'animais';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $query = "SELECT a.*, c.nome as cliente_nome FROM {$this->table} a 
                  LEFT JOIN clientes c ON a.cliente_id = c.id ORDER BY a.nome ASC";
        $result = $this->db->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById($id) {
        $id = escapeSql($id);
        $query = "SELECT a.*, c.nome as cliente_nome FROM {$this->table} a 
                  LEFT JOIN clientes c ON a.cliente_id = c.id WHERE a.id = '$id'";
        $result = $this->db->query($query);
        return $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    public function getByClienteId($cliente_id) {
        $cliente_id = escapeSql($cliente_id);
        $query = "SELECT * FROM {$this->table} WHERE cliente_id = '$cliente_id' ORDER BY nome ASC";
        $result = $this->db->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function create($data) {
        $cliente_id = escapeSql($data['cliente_id']);
        $nome = escapeSql($data['nome']);
        $raca = escapeSql($data['raca'] ?? '');
        $tipo = escapeSql($data['tipo'] ?? '');
        $data_nascimento = escapeSql($data['data_nascimento'] ?? null);
        $peso = escapeSql($data['peso'] ?? 0);

        $query = "INSERT INTO {$this->table} (cliente_id, nome, raca, tipo, data_nascimento, peso) 
                  VALUES ('$cliente_id', '$nome', '$raca', '$tipo', '$data_nascimento', '$peso')";
        
        return $this->db->query($query) ? $this->db->insert_id : false;
    }

    public function update($id, $data) {
        $id = escapeSql($id);
        $nome = escapeSql($data['nome']);
        $raca = escapeSql($data['raca'] ?? '');
        $tipo = escapeSql($data['tipo'] ?? '');
        $peso = escapeSql($data['peso'] ?? 0);

        $query = "UPDATE {$this->table} SET nome='$nome', raca='$raca', tipo='$tipo', peso='$peso' WHERE id='$id'";
        
        return $this->db->query($query);
    }

    public function delete($id) {
        $id = escapeSql($id);
        $query = "DELETE FROM {$this->table} WHERE id = '$id'";
        return $this->db->query($query);
    }

    public function count() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>
