<?php
// Configuração do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'petshop_crm');

// Criar conexão
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexão
if ($conn->connect_error) {
  die("Erro na conexão: " . $conn->connect_error);
}

// Definir charset
$conn->set_charset("utf8mb4");

// Iniciar sessão
session_start();

// Funções de utilidade
function formatarData($data) {
  $dt = new DateTime($data);
  return $dt->format('d/m/Y');
}

function formatarDataHora($data) {
  $dt = new DateTime($data);
  return $dt->format('d/m/Y H:i');
}

function formatarMoeda($valor) {
  return 'R$ ' . number_format($valor, 2, ',', '.');
}

function verificarAutenticacao() {
  if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
  }
}

function escapar($string) {
  global $conn;
  return $conn->real_escape_string($string);
}
?>
