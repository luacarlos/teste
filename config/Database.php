<?php
class Database {
    private static $instance = null;
    private $connection;
    private $host = 'localhost';
    private $db_name = 'petshop_crm';
    private $user = 'root';
    private $password = '';
    private $port = 3306;

    private function __construct() {
        try {
            $this->connection = new mysqli(
                $this->host,
                $this->user,
                $this->password,
                $this->db_name,
                $this->port
            );

            if ($this->connection->connect_error) {
                throw new Exception("Erro na conexão: " . $this->connection->connect_error);
            }

            $this->connection->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Erro de conexão com banco de dados: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql) {
        return $this->connection->query($sql);
    }

    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }

    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function __destruct() {
        $this->close();
    }
}

// Funções auxiliares de segurança
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function escapeSql($input) {
    $db = Database::getInstance();
    return $db->getConnection()->real_escape_string($input);
}
