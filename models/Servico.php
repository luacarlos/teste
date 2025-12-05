<?php
require_once __DIR__ . '/../config/Database.php';

class Servico {
    private $db;
    private $table = 'servicos';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table} WHERE ativo = TRUE ORDER BY nome ASC";
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
        $descricao = escapeSql($data['descricao'] ?? '');
        $preco = escapeSql($data['preco']);
        $duracao = escapeSql($data['duracao_minutos'] ?? 30);

        $query = "INSERT INTO {$this->table} (nome, descricao, preco, duracao_minutos) 
                  VALUES ('$nome', '$descricao', '$preco', '$duracao')";
        
        return $this->db->query($query) ? $this->db->insert_id : false;
    }

    public function update($id, $data) {
        $id = escapeSql($id);
        $nome = escapeSql($data['nome']);
        $descricao = escapeSql($data['descricao'] ?? '');
        $preco = escapeSql($data['preco']);
        $duracao = escapeSql($data['duracao_minutos'] ?? 30);

        $query = "UPDATE {$this->table} SET nome='$nome', descricao='$descricao', preco='$preco', duracao_minutos='$duracao' WHERE id='$id'";
        
        return $this->db->query($query);
    }

    public function delete($id) {
        $id = escapeSql($id);
        $query = "UPDATE {$this->table} SET ativo = FALSE WHERE id = '$id'";
        return $this->db->query($query);
    }

    public function count() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE ativo = TRUE";
        $result = $this->db->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>
