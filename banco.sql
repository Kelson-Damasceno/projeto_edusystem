-- Criar o banco de dados
CREATE DATABASE IF NOT EXISTS escola;
USE escola;

-- Tabela de usuários (login)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL,
    senha VARCHAR(255) NOT NULL
);

-- Inserir usuário padrão: admin / 1234
INSERT INTO usuarios (usuario, senha) VALUES ('admin', MD5('1234'));

-- Tabela de alunos
CREATE TABLE IF NOT EXISTS alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    ra VARCHAR(20) NOT NULL UNIQUE,
    curso VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dados de exemplo
INSERT INTO alunos (nome, ra, curso, email, telefone) VALUES
('João Silva', '2024001', 'Análise e Desenvolvimento de Sistemas', 'joao@email.com', '(11) 99999-0001'),
('Maria Souza', '2024002', 'Ciência da Computação', 'maria@email.com', '(11) 99999-0002'),
('Carlos Oliveira', '2024003', 'Sistemas de Informação', 'carlos@email.com', '(11) 99999-0003');
