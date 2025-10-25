-- encoding: UTF-8
-- encoding: UTF-8
-- Criacao do banco de dados
-- Tabela de usuarios (sistema de login)
-- Tabela de usuarios (sistema de login)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    especie ENUM('Cachorro', 'Gato', 'Passaro', 'Roedor', 'Reptil', 'Outro') NOT NULL,
    role ENUM('admin', 'recepcionista') DEFAULT 'recepcionista',
    ativo BOOLEAN DEFAULT TRUE,
    sexo ENUM('Macho', 'Femea') NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefone VARCHAR(20) NOT NULL,
    celular VARCHAR(20),
    endereco VARCHAR(200),
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    estado CHAR(2),
    observacoes TEXT,
    ativo BOOLEAN DEFAULT TRUE,
('Joao Silva Santos', 'joao.silva@email.com', '(11) 3456-7890', '(11) 98765-4321', '123.456.789-00', 'Rua das Flores, 123', 'Centro', 'Sao Paulo', 'SP', '01234-567'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
('Maria Oliveira Costa', 'maria.oliveira@email.com', '(11) 3234-5678', '(11) 97654-3210', '234.567.890-11', 'Av. Paulista, 456', 'Bela Vista', 'Sao Paulo', 'SP', '01310-100'),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
('Pedro Santos Almeida', 'pedro.santos@email.com', '(11) 3345-6789', '(11) 96543-2109', '345.678.901-22', 'Rua Augusta, 789', 'Consolacao', 'Sao Paulo', 'SP', '01305-000'),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
('Juliana Souza Martins', 'juliana.souza@email.com', '(11) 3678-9013', '(11) 93210-9876', '678.901.234-55', 'Rua Haddock Lobo, 987', 'Cerqueira Cesar', 'Sao Paulo', 'SP', '01414-001'),

('Fernanda Lima Santos', 'fernanda.lima@email.com', '(11) 3890-1235', '(11) 91098-7654', '890.123.456-77', 'Rua da Consolacao, 147', 'Consolacao', 'Sao Paulo', 'SP', '01301-000');
-- Tabela de pets
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    nome VARCHAR(50) NOT NULL,
    especie ENUM('Cachorro', 'Gato', 'Passaro', 'Roedor', 'Reptil', 'Outro') NOT NULL,
('Thor', 'Cachorro', 'Pastor Alemao', 'Macho', 'Preto e Marrom', '2019-05-10', 35.0),
    raca VARCHAR(50),
('Luna', 'Cachorro', 'Golden Retriever', 'Femea', 'Dourado', '2022-01-08', 25.3),
    sexo ENUM('Macho', 'Femea') NOT NULL,
('Bella', 'Cachorro', 'Poodle', 'Femea', 'Branco', '2020-09-25', 8.7),
    cor VARCHAR(50),
('Nina', 'Gato', 'Siames', 'Femea', 'Bege', '2020-12-05', 3.8),
    data_nascimento DATE,
('Banho Medio Porte', 'Banho completo para caes de medio porte', 70.00, 60, 'Banho e Tosa'),
    peso DECIMAL(5,2),
('Tosa Higienica', 'Tosa higienica completa', 40.00, 30, 'Banho e Tosa'),
    observacoes TEXT,
('Consulta Veterinaria', 'Consulta clinica geral', 120.00, 30, 'Veterinario'),
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
('Vacinacao Multipla (V10)', 'Vacina V10 para caes', 80.00, 15, 'Vacina'),
('Vacinacao Antirrabica', 'Vacina antirrabica', 60.00, 15, 'Vacina'),
('Vacinacao Gatos (V4)', 'Vacina V4 para gatos', 75.00, 15, 'Vacina'),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

('Castracao Canina', 'Cirurgia de castracao para caes', 400.00, 120, 'Cirurgia'),
('Castracao Felina', 'Cirurgia de castracao para gatos', 300.00, 90, 'Cirurgia'),
('Hospedagem Diaria', 'Hospedagem com alimentacao', 80.00, 1440, 'Hospedagem'),
-- Inserir atendimentos de teste (ultimos 30 dias)
CREATE TABLE IF NOT EXISTS servicos (
(2, 6, 2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '09:00:00', 'Concluido', 60.00),
    id INT AUTO_INCREMENT PRIMARY KEY,
(4, 2, 2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '10:00:00', 'Concluido', 70.00),
    nome VARCHAR(100) NOT NULL,
(7, 7, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '11:00:00', 'Concluido', 120.00),
    descricao TEXT,
(9, 8, 2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '14:00:00', 'Concluido', 80.00),
    duracao_minutos INT DEFAULT 30,
(1, 5, 2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '09:30:00', 'Concluido', 80.00),
    ativo BOOLEAN DEFAULT TRUE,
(3, 3, 2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '10:00:00', 'Concluido', 90.00),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
(1, 8, 2, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '09:00:00', 'Concluido', 80.00),
-- Criar indices para melhor performance
CREATE INDEX idx_pets_cliente ON pets(cliente_id);
-- Tabela de atendimentos
CREATE TABLE IF NOT EXISTS atendimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT NOT NULL,
    servico_id INT NOT NULL,
    usuario_id INT,
    data_atendimento DATE NOT NULL,
    hora_atendimento TIME NOT NULL,
    status ENUM('Agendado', 'Em Atendimento', 'Concluido', 'Cancelado') DEFAULT 'Agendado',
    valor DECIMAL(10,2) NOT NULL,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir usuarios padrao (senha: admin123 e recepcao123)
INSERT INTO usuarios (nome, email, senha, role) VALUES
('Administrador', 'admin@petshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Recepcionista', 'recepcao@petshop.com', '$2y$10$4IiPXlmMU3Z7YYPBhiHQW.PEZ2Z1pXF4GVx6TqeJlLUbQUQ7xQOPy', 'recepcionista');

-- Inserir clientes de teste
INSERT INTO clientes (nome, email, telefone, celular, cpf, endereco, bairro, cidade, estado, cep) VALUES
('Joao Silva Santos', 'joao.silva@email.com', '(11) 3456-7890', '(11) 98765-4321', '123.456.789-00', 'Rua das Flores, 123', 'Centro', 'Sao Paulo', 'SP', '01234-567'),
('Maria Oliveira Costa', 'maria.oliveira@email.com', '(11) 3234-5678', '(11) 97654-3210', '234.567.890-11', 'Av. Paulista, 456', 'Bela Vista', 'Sao Paulo', 'SP', '01310-100'),
('Pedro Santos Almeida', 'pedro.santos@email.com', '(11) 3345-6789', '(11) 96543-2109', '345.678.901-22', 'Rua Augusta, 789', 'Consolacao', 'Sao Paulo', 'SP', '01305-000'),
('Ana Paula Ferreira', 'ana.ferreira@email.com', '(11) 3456-7891', '(11) 95432-1098', '456.789.012-33', 'Rua Oscar Freire, 321', 'Jardins', 'Sao Paulo', 'SP', '01426-001'),
('Carlos Eduardo Lima', 'carlos.lima@email.com', '(11) 3567-8902', '(11) 94321-0987', '567.890.123-44', 'Av. Faria Lima, 654', 'Pinheiros', 'Sao Paulo', 'SP', '05426-200'),
('Juliana Souza Martins', 'juliana.souza@email.com', '(11) 3678-9013', '(11) 93210-9876', '678.901.234-55', 'Rua Haddock Lobo, 987', 'Cerqueira Cesar', 'Sao Paulo', 'SP', '01414-001'),
('Roberto Pereira Costa', 'roberto.pereira@email.com', '(11) 3789-0124', '(11) 92109-8765', '789.012.345-66', 'Av. Reboucas, 258', 'Pinheiros', 'Sao Paulo', 'SP', '05401-100'),
('Fernanda Lima Santos', 'fernanda.lima@email.com', '(11) 3890-1235', '(11) 91098-7654', '890.123.456-77', 'Rua da Consolacao, 147', 'Consolacao', 'Sao Paulo', 'SP', '01301-000');

-- Inserir pets de teste
INSERT INTO pets (cliente_id, nome, especie, raca, sexo, cor, data_nascimento, peso) VALUES
(1, 'Rex', 'Cachorro', 'Labrador', 'Macho', 'Dourado', '2020-03-15', 28.5),
(1, 'Mimi', 'Gato', 'Persa', 'Femea', 'Branco', '2021-07-20', 4.2),
(2, 'Thor', 'Cachorro', 'Pastor Alemao', 'Macho', 'Preto e Marrom', '2019-05-10', 35.0),
(3, 'Luna', 'Cachorro', 'Golden Retriever', 'Femea', 'Dourado', '2022-01-08', 25.3),
(3, 'Bolinha', 'Gato', 'SRD', 'Macho', 'Laranja', '2021-11-12', 5.1),
(4, 'Bella', 'Cachorro', 'Poodle', 'Femea', 'Branco', '2020-09-25', 8.7),
(5, 'Max', 'Cachorro', 'Bulldog Frances', 'Macho', 'Tigrado', '2021-04-18', 12.3),
(5, 'Nina', 'Gato', 'Siames', 'Femea', 'Bege', '2020-12-05', 3.8),
(6, 'Zeus', 'Cachorro', 'Rottweiler', 'Macho', 'Preto', '2019-08-30', 42.5),
(7, 'Pipoca', 'Cachorro', 'Shih Tzu', 'Femea', 'Branco e Marrom', '2022-03-14', 6.2),
(7, 'Mel', 'Gato', 'Maine Coon', 'Femea', 'Rajado', '2021-06-22', 6.8),
(8, 'Bob', 'Cachorro', 'Beagle', 'Macho', 'Tricolor', '2020-10-17', 15.4);

-- Inserir servicos
INSERT INTO servicos (nome, descricao, preco, duracao_minutos, categoria) VALUES
('Banho Pequeno Porte', 'Banho completo para caes de pequeno porte', 50.00, 45, 'Banho e Tosa'),
('Banho Medio Porte', 'Banho completo para caes de medio porte', 70.00, 60, 'Banho e Tosa'),
('Banho Grande Porte', 'Banho completo para caes de grande porte', 90.00, 90, 'Banho e Tosa'),
('Tosa Higienica', 'Tosa higienica completa', 40.00, 30, 'Banho e Tosa'),
('Tosa Completa', 'Tosa completa com acabamento', 80.00, 90, 'Banho e Tosa'),
('Banho em Gatos', 'Banho especializado para gatos', 60.00, 45, 'Banho e Tosa'),
('Consulta Veterinaria', 'Consulta clinica geral', 120.00, 30, 'Veterinario'),
('Vacinacao Multipla (V10)', 'Vacina V10 para caes', 80.00, 15, 'Vacina'),
('Vacinacao Antirrabica', 'Vacina antirrabica', 60.00, 15, 'Vacina'),
('Vacinacao Gatos (V4)', 'Vacina V4 para gatos', 75.00, 15, 'Vacina'),
('Exame de Sangue', 'Hemograma completo', 150.00, 20, 'Veterinario'),
('Castracao Canina', 'Cirurgia de castracao para caes', 400.00, 120, 'Cirurgia'),
('Castracao Felina', 'Cirurgia de castracao para gatos', 300.00, 90, 'Cirurgia'),
('Hospedagem Diaria', 'Hospedagem com alimentacao', 80.00, 1440, 'Hospedagem'),
('Corte de Unhas', 'Corte de unhas e limpeza', 25.00, 15, 'Outro');

INSERT INTO atendimentos (pet_id, servico_id, usuario_id, data_atendimento, hora_atendimento, status, valor) VALUES
(1, 2, 2, CURDATE(), '09:00:00', 'Agendado', 70.00),
(3, 3, 2, CURDATE(), '10:30:00', 'Agendado', 90.00),
(6, 1, 2, CURDATE(), '14:00:00', 'Agendado', 50.00),
(10, 4, 2, CURDATE(), '15:30:00', 'Agendado', 40.00),

(2, 6, 2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '09:00:00', 'Concluido', 60.00),
(4, 2, 2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '10:00:00', 'Concluido', 70.00),
(7, 7, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '11:00:00', 'Concluido', 120.00),
(9, 8, 2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '14:00:00', 'Concluido', 80.00),

(1, 5, 2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '09:30:00', 'Concluido', 80.00),
 (1, 5, 2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '09:30:00', 'Concluido', 80.00),

(12, 2, 2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '14:00:00', 'Concluido', 70.00),

(6, 1, 2, DATE_SUB(CURDATE(), INTERVAL 7 DAY), '09:00:00', 'Concluido', 50.00),
(7, 12, 1, DATE_SUB(CURDATE(), INTERVAL 7 DAY), '10:00:00', 'Concluido', 400.00),
(10, 5, 2, DATE_SUB(CURDATE(), INTERVAL 7 DAY), '14:00:00', 'Concluido', 80.00),

(1, 8, 2, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '09:00:00', 'Concluido', 80.00),
(4, 2, 2, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '11:00:00', 'Concluido', 70.00),
(9, 9, 2, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '15:00:00', 'Concluido', 60.00),

(2, 13, 1, DATE_SUB(CURDATE(), INTERVAL 15 DAY), '09:00:00', 'Concluido', 300.00),
(5, 6, 2, DATE_SUB(CURDATE(), INTERVAL 15 DAY), '11:00:00', 'Concluido', 60.00),
(11, 6, 2, DATE_SUB(CURDATE(), INTERVAL 15 DAY), '14:00:00', 'Concluido', 60.00),

(3, 7, 1, DATE_SUB(CURDATE(), INTERVAL 20 DAY), '10:00:00', 'Concluido', 120.00),
(12, 2, 2, DATE_SUB(CURDATE(), INTERVAL 20 DAY), '14:00:00', 'Concluido', 70.00),

(1, 2, 2, DATE_SUB(CURDATE(), INTERVAL 25 DAY), '09:00:00', 'Concluido', 70.00),
(6, 1, 2, DATE_SUB(CURDATE(), INTERVAL 25 DAY), '11:00:00', 'Concluido', 50.00),

(7, 11, 1, DATE_SUB(CURDATE(), INTERVAL 30 DAY), '10:00:00', 'Concluido', 150.00),
(10, 15, 2, DATE_SUB(CURDATE(), INTERVAL 30 DAY), '15:00:00', 'Concluido', 25.00);

CREATE INDEX idx_pets_cliente ON pets(cliente_id);
CREATE INDEX idx_atendimentos_pet ON atendimentos(pet_id);
CREATE INDEX idx_atendimentos_servico ON atendimentos(servico_id);
CREATE INDEX idx_atendimentos_data ON atendimentos(data_atendimento);
CREATE INDEX idx_clientes_nome ON clientes(nome);
CREATE INDEX idx_pets_nome ON pets(nome);
