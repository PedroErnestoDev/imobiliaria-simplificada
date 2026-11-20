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

-- E-mail: admin@imobiliaria.com | Senha: admin123456

INSERT IGNORE INTO usuarios (nome, email, senha_hash, papel, ativo) VALUES
('Administrador', 'admin@imobiliaria.com', '$2y$12$ct9iqhrvH/l7EclefIekPOCjT7seFrLFBssZZIBNY2TaiyRu3yRSy', 'admin', 1);

-- Tabela de imoveis (cadastro de imóveis para venda/aluguel)
CREATE TABLE IF NOT EXISTS imoveis (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    usuario_id INT UNSIGNED NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    finalidade ENUM('venda', 'aluguel') NOT NULL DEFAULT 'venda',
    tipo ENUM('casa', 'apartamento', 'condominio', 'terreno', 'na_planta') NOT NULL DEFAULT 'casa',
    cep VARCHAR(10) DEFAULT '',
    endereco VARCHAR(200) DEFAULT '',
    numero VARCHAR(20) DEFAULT NULL,
    complemento VARCHAR(100) DEFAULT NULL,
    bairro VARCHAR(100) DEFAULT '',
    cidade VARCHAR(100) DEFAULT '',
    estado VARCHAR(2) DEFAULT '',
    quartos INT NOT NULL DEFAULT 0,
    banheiros INT NOT NULL DEFAULT 0,
    vagas INT NOT NULL DEFAULT 0,
    area DECIMAL(10,2) NOT NULL DEFAULT 0,
    valor DECIMAL(12,2) NOT NULL DEFAULT 0,
    condominio DECIMAL(10,2) DEFAULT NULL,
    iptu DECIMAL(10,2) DEFAULT NULL,
    telefone_contato VARCHAR(30) DEFAULT NULL,
    whatsapp VARCHAR(30) DEFAULT NULL,
    email_contato VARCHAR(150) DEFAULT NULL,
    latitude DECIMAL(10,6) DEFAULT NULL,
    longitude DECIMAL(10,6) DEFAULT NULL,
    status ENUM('disponivel', 'indisponivel') NOT NULL DEFAULT 'disponivel',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabela de imagens dos imoveis
CREATE TABLE IF NOT EXISTS imagens_imoveis (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    imovel_id INT UNSIGNED NOT NULL,
    caminho_imagem VARCHAR(500) NOT NULL,
    imagem_principal TINYINT(1) NOT NULL DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (imovel_id) REFERENCES imoveis(id) ON DELETE CASCADE
) ENGINE=InnoDB;
