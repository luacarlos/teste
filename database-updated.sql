-- Adicionar tabelas de auditoria
CREATE TABLE IF NOT EXISTS atividades_usuario (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_id INT NOT NULL,
  acao VARCHAR(50),
  descricao TEXT,
  ip_address VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Criar índices para melhor performance
CREATE INDEX idx_cliente_nome ON clientes(nome);
CREATE INDEX idx_animal_cliente ON animais(cliente_id);
CREATE INDEX idx_agendamento_data ON agendamentos(data_agendamento);
CREATE INDEX idx_fatura_status ON faturas(status);
CREATE INDEX idx_fatura_cliente ON faturas(cliente_id);
