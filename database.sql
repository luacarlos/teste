-- PetShop CRM Database
CREATE DATABASE IF NOT EXISTS petshop_crm;
USE petshop_crm;

-- Tabela de usuários
CREATE TABLE usuarios (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  senha VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de clientes
CREATE TABLE clientes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  telefone VARCHAR(20),
  endereco TEXT,
  cidade VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de animais de estimação
CREATE TABLE animais (
  id INT PRIMARY KEY AUTO_INCREMENT,
  cliente_id INT NOT NULL,
  nome VARCHAR(100) NOT NULL,
  raca VARCHAR(50),
  tipo VARCHAR(30),
  data_nascimento DATE,
  peso DECIMAL(5,2),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- Tabela de serviços
CREATE TABLE servicos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  descricao TEXT,
  preco DECIMAL(10,2) NOT NULL,
  duracao_minutos INT,
  ativo BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de agendamentos
CREATE TABLE agendamentos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  cliente_id INT NOT NULL,
  animal_id INT NOT NULL,
  servico_id INT NOT NULL,
  data_agendamento DATETIME NOT NULL,
  status ENUM('pendente', 'confirmado', 'concluido', 'cancelado') DEFAULT 'pendente',
  observacoes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
  FOREIGN KEY (animal_id) REFERENCES animais(id) ON DELETE CASCADE,
  FOREIGN KEY (servico_id) REFERENCES servicos(id)
);

-- Tabela de faturas
CREATE TABLE faturas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  agendamento_id INT,
  cliente_id INT NOT NULL,
  valor_total DECIMAL(10,2) NOT NULL,
  status ENUM('pendente', 'pago', 'cancelado') DEFAULT 'pendente',
  data_vencimento DATE,
  data_pagamento DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE SET NULL,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- Dados iniciais
INSERT INTO usuarios (nome, email, senha) VALUES ('Admin', 'admin@petshop.com', SHA2('123456', 256));

INSERT INTO servicos (nome, descricao, preco, duracao_minutos) VALUES
('Banho', 'Banho completo com shampoo', 50.00, 30),
('Tosa', 'Tosa higiênica ou estética', 80.00, 45),
('Consulta Veterinária', 'Avaliação de saúde', 120.00, 30),
('Vacinação', 'Aplicação de vacinas', 60.00, 15),
('Limpeza de Ouvidos', 'Limpeza profissional', 40.00, 15);
