-- MySQL 8.0 initialization script for Imobiliária Simplificada
-- Database: imobiliaria

CREATE DATABASE IF NOT EXISTS imobiliaria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE imobiliaria;

-- Tabela de usuarios (autenticacao e controle de acesso)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    papel ENUM("admin", "corretor") NOT NULL DEFAULT "corretor",
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

--- __E-mail:__ `admin@imobiliaria.com`  __Senha:__ `admin123456`

INSERT IGNORE INTO usuarios (nome, email, senha_hash, papel, ativo) VALUES
('Administrador', 'admin@imobiliaria.com', '$2y$12$ct9iqhrvH/l7EclefIekPOCjT7seFrLFBssZZIBNY2TaiyRu3yRSy', 'admin', 1);
