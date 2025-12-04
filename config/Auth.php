<?php
class Auth {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function login($email, $password) {
        $email = escapeSql($email);
        $query = "SELECT id, email, nome, senha FROM usuarios WHERE email = '$email' LIMIT 1";
        $result = $this->db->query($query);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['senha'])) {
                return $user;
            }
        }
        return false;
    }

    public function register($email, $password, $nome) {
        $email = escapeSql($email);
        $nome = escapeSql($nome);
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO usuarios (email, senha, nome, data_criacao) VALUES ('$email', '$hashed_password', '$nome', NOW())";
        
        if ($this->db->query($query)) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function logout() {
        session_destroy();
    }

    public function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'nome' => $_SESSION['user_name']
        ];
    }
}
