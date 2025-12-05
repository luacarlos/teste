<?php
require_once __DIR__ . '/../config/Database.php';

class Cliente {
    private $db;
    private $table = 'clientes';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY nome ASC";
        $result = $this->db->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById($id) {
        $id = escapeSql($id);
        $query = "SELECT * FROM {$this->table} WHERE id = '$id'";
        $result = $this->db->query($query);
        return $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    public function create($data) {
        $nome = escapeSql($data['nome']);
        $email = escapeSql($data['email'] ?? '');
        $telefone = escapeSql($data['telefone'] ?? '');
        $endereco = escapeSql($data['endereco'] ?? '');
        $cidade = escapeSql($data['cidade'] ?? '');

        $query = "INSERT INTO {$this->table} (nome, email, telefone, endereco, cidade) 
                  VALUES ('$nome', '$email', '$telefone', '$endereco', '$cidade')";
        
        return $this->db->query($query) ? $this->db->insert_id : false;
    }

    public function update($id, $data) {
        $id = escapeSql($id);
        $nome = escapeSql($data['nome']);
        $email = escapeSql($data['email'] ?? '');
        $telefone = escapeSql($data['telefone'] ?? '');
        $endereco = escapeSql($data['endereco'] ?? '');
        $cidade = escapeSql($data['cidade'] ?? '');

        $query = "UPDATE {$this->table} SET nome='$nome', email='$email', telefone='$telefone', 
                  endereco='$endereco', cidade='$cidade' WHERE id='$id'";
        
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
