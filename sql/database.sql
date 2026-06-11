SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS atendimentos;
DROP TABLE IF EXISTS pets;
DROP TABLE IF EXISTS servicos;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS usuarios;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    role ENUM('admin', 'recepcionista') NOT NULL DEFAULT 'recepcionista',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE clientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(191) NULL,
    telefone VARCHAR(20) NOT NULL,
    celular VARCHAR(20) NULL,
    cpf VARCHAR(14) NULL UNIQUE,
    endereco VARCHAR(200) NULL,
    bairro VARCHAR(100) NULL,
    cidade VARCHAR(100) NULL,
    estado CHAR(2) NULL,
    cep VARCHAR(9) NULL,
    observacoes TEXT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clientes_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT UNSIGNED NOT NULL,
    nome VARCHAR(50) NOT NULL,
    especie ENUM('Cachorro', 'Gato', 'Passaro', 'Roedor', 'Reptil', 'Outro') NOT NULL,
    raca VARCHAR(50) NULL,
    sexo ENUM('Macho', 'Femea') NOT NULL,
    cor VARCHAR(50) NULL,
    data_nascimento DATE NULL,
    peso DECIMAL(5,2) NULL,
    observacoes TEXT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pets_cliente (cliente_id),
    INDEX idx_pets_nome (nome),
    CONSTRAINT fk_pets_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE servicos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NULL,
    preco DECIMAL(10,2) NOT NULL,
    duracao_minutos INT UNSIGNED NOT NULL DEFAULT 30,
    categoria VARCHAR(50) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE atendimentos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pet_id INT UNSIGNED NOT NULL,
    servico_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NULL,
    data_atendimento DATE NOT NULL,
    hora_atendimento TIME NOT NULL,
    status ENUM('Agendado', 'Em Atendimento', 'Concluido', 'Cancelado') NOT NULL DEFAULT 'Agendado',
    valor DECIMAL(10,2) NOT NULL,
    observacoes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_atendimentos_pet (pet_id),
    INDEX idx_atendimentos_servico (servico_id),
    INDEX idx_atendimentos_data (data_atendimento),
    CONSTRAINT fk_atendimentos_pet FOREIGN KEY (pet_id) REFERENCES pets(id),
    CONSTRAINT fk_atendimentos_servico FOREIGN KEY (servico_id) REFERENCES servicos(id),
    CONSTRAINT fk_atendimentos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Credenciais demonstrativas: admin123 e recepcao123.
INSERT INTO usuarios (nome, email, senha, role) VALUES
('Administrador Demo', 'admin@petshop.com', '$2y$10$nnEfZl7VuGn9UN9Kzjp63OuePimI6.56R6VJ4hcvq3c37sF2uyipu', 'admin'),
('Recepcao Demo', 'recepcao@petshop.com', '$2y$10$Vn2OeMgm5Oe1.J52RF0grucs5nk.7wx51FBW3cig/ext/GFA8XqH6', 'recepcionista');

INSERT INTO clientes (nome, email, telefone, celular, cpf, cidade, estado, cep) VALUES
('Cliente Demonstracao', 'cliente@example.com', '(11) 3000-0000', '(11) 90000-0000', '111.111.111-11', 'Sao Paulo', 'SP', '01000-000'),
('Maria Exemplo', 'maria@example.com', '(11) 3000-0001', '(11) 90000-0001', '222.222.222-22', 'Sao Paulo', 'SP', '01000-001');

INSERT INTO pets (cliente_id, nome, especie, raca, sexo, cor, data_nascimento, peso) VALUES
(1, 'Rex', 'Cachorro', 'SRD', 'Macho', 'Caramelo', '2021-03-15', 18.50),
(2, 'Luna', 'Gato', 'SRD', 'Femea', 'Branco', '2022-07-20', 4.20);

INSERT INTO servicos (nome, descricao, preco, duracao_minutos, categoria) VALUES
('Banho', 'Banho completo demonstrativo', 60.00, 60, 'Banho e Tosa'),
('Consulta Veterinaria', 'Consulta clinica demonstrativa', 120.00, 30, 'Veterinario');

INSERT INTO atendimentos (pet_id, servico_id, usuario_id, data_atendimento, hora_atendimento, status, valor) VALUES
(1, 1, 2, CURDATE(), '09:00:00', 'Agendado', 60.00),
(2, 2, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '10:00:00', 'Concluido', 120.00);
