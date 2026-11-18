
-- ==========================================
-- SISTEMA DE GERENCIAMENTO IMOBILIÁRIO
-- Banco de dados: MySQL 8
-- ==========================================

CREATE DATABASE IF NOT EXISTS imobiliaria
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE imobiliaria;

-- ==========================================
-- 1. USUARIOS
-- ==========================================

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    tipo ENUM('admin', 'corretor') NOT NULL DEFAULT 'corretor',
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 2. IMOVEIS
-- ==========================================

CREATE TABLE imoveis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    usuario_id INT,

    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,

    finalidade ENUM('venda', 'aluguel') NOT NULL,

    tipo ENUM(
        'casa',
        'apartamento',
        'condominio',
        'terreno',
        'na_planta'
    ) NOT NULL,

    -- Endereco
    cep VARCHAR(9) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    numero VARCHAR(20),
    complemento VARCHAR(100),
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado CHAR(2) NOT NULL,

    -- Caracteristicas
    quartos INT NOT NULL DEFAULT 0,
    banheiros INT NOT NULL DEFAULT 0,
    vagas INT NOT NULL DEFAULT 0,
    area DECIMAL(10,2),

    -- Valores
    valor DECIMAL(12,2) NOT NULL,
    condominio DECIMAL(10,2) DEFAULT 0,
    iptu DECIMAL(10,2) DEFAULT 0,

    -- Geolocalizacao
    latitude DECIMAL(10,7),
    longitude DECIMAL(10,7),

    status ENUM(
        'disponivel',
        'indisponivel'
    ) NOT NULL DEFAULT 'disponivel',

    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE SET NULL,

    CHECK (valor > 0),
    CHECK (quartos >= 0),
    CHECK (banheiros >= 0),
    CHECK (vagas >= 0),
    CHECK (area IS NULL OR area > 0)
);

-- ==========================================
-- 3. IMAGENS DOS IMOVEIS
-- ==========================================

CREATE TABLE imagens_imoveis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    imovel_id INT NOT NULL,
    caminho_imagem VARCHAR(255) NOT NULL,
    imagem_principal BOOLEAN NOT NULL DEFAULT FALSE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (imovel_id)
        REFERENCES imoveis(id)
        ON DELETE CASCADE
);

-- ==========================================
-- 4. CARACTERISTICAS
-- ==========================================

CREATE TABLE caracteristicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    categoria ENUM('interna', 'externa') NOT NULL,

    UNIQUE (nome, categoria)
);

-- ==========================================
-- 5. RELACIONAMENTO IMOVEL / CARACTERISTICAS
-- ==========================================

CREATE TABLE imovel_caracteristicas (
    imovel_id INT NOT NULL,
    caracteristica_id INT NOT NULL,

    PRIMARY KEY (imovel_id, caracteristica_id),

    FOREIGN KEY (imovel_id)
        REFERENCES imoveis(id)
        ON DELETE CASCADE,

    FOREIGN KEY (caracteristica_id)
        REFERENCES caracteristicas(id)
        ON DELETE CASCADE
);

-- ==========================================
-- 6. CARACTERISTICAS INICIAIS
-- ==========================================

INSERT INTO caracteristicas (nome, categoria) VALUES
('Box banheiro', 'interna'),
('Armário cozinha', 'interna'),
('Armário quarto', 'interna'),
('Portão eletrônico', 'externa'),
('Interfone', 'externa');

-- ==========================================
-- FIM DA INICIALIZACAO
-- ==========================================
