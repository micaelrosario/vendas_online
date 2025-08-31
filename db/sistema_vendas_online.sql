-- --------------------------------------------------------
-- Criação do Banco de Dados
-- --------------------------------------------------------
DROP DATABASE IF EXISTS sistema_vendas_online;
CREATE DATABASE sistema_vendas_online CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sistema_vendas_online;

-- --------------------------------------------------------
-- Criação das Tabelas
-- --------------------------------------------------------

-- Tabela: categorias
CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT
) ENGINE=InnoDB;

-- Tabela: produtos
CREATE TABLE produtos (
    id_produto INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) UNIQUE NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco_custo DECIMAL(10, 2) NOT NULL,
    quantidade_estoque INT NOT NULL,
    data_registro DATE,
    id_categoria INT,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria),
    CHECK (quantidade_estoque >= 0)
) ENGINE=InnoDB;

-- Tabela: clientes
CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    cpf_cnpj VARCHAR(18) UNIQUE NOT NULL,
    nome VARCHAR(255) NOT NULL,
    endereco VARCHAR(255),
    telefone VARCHAR(20),
    email VARCHAR(255)
) ENGINE=InnoDB;

-- Tabela: pedidos
CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status_envio VARCHAR(50) DEFAULT 'Pendente',
    codigo_rastreio VARCHAR(255),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
) ENGINE=InnoDB;

-- Tabela: itens_pedido
CREATE TABLE itens_pedido (
    id_item_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT,
    id_produto INT,
    quantidade INT NOT NULL,
    preco_unitario_na_venda DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    FOREIGN KEY (id_produto) REFERENCES produtos(id_produto),
    CHECK (quantidade >= 0)
) ENGINE=InnoDB;


-- --------------------------------------------------------
-- Inserção de Dados de Exemplo
-- --------------------------------------------------------

-- Dados de exemplo para a tabela 'categorias'
INSERT INTO categorias (nome, descricao) VALUES
('Eletrônicos', 'Dispositivos eletrônicos e acessórios'),
('Livros', 'Livros de ficção, não-ficção e acadêmicos'),
('Vestuário', 'Roupas, calçados e acessórios de moda');

-- CORREÇÃO: Readicionando a inserção de produtos para evitar o erro de chave estrangeira
INSERT INTO produtos (sku, nome, descricao, preco_custo, quantidade_estoque, id_categoria, data_registro) VALUES
('ELT001', 'Smartphone XYZ', 'Smartphone de última geração com 128GB.', 1500.00, 50, 1, CURDATE()),
('ELT002', 'Smartwatch ABC', 'Relógio inteligente com monitoramento de saúde.', 300.00, 100, 1, CURDATE()),
('LIV001', 'O Guia do Mochileiro', 'Livro de ficção científica clássico.', 20.00, 200, 2, CURDATE()),
('LIV002', 'A Arte da Guerra', 'Clássico livro sobre estratégia militar.', 15.00, 150, 2, CURDATE()),
('VES001', 'Camiseta Básica', 'Camiseta de algodão na cor preta.', 10.00, 500, 3, CURDATE());

-- Dados de exemplo para a tabela 'clientes'
INSERT INTO clientes (cpf_cnpj, nome, endereco, telefone, email) VALUES
('111.222.333-44', 'João da Silva', 'Rua das Flores, 123, Centro', '(11) 98765-4321', 'joao.silva@email.com'),
('555.666.777-88', 'Maria Oliveira', 'Avenida Principal, 456, Bairro Novo', '(21) 99887-6655', 'maria.o@email.com');

-- Dados de exemplo para a tabela 'pedidos'
INSERT INTO pedidos (id_cliente, status_envio, codigo_rastreio) VALUES
(1, 'Enviado', 'RA123456789BR'),
(2, 'Pendente', NULL);

-- Dados de exemplo para a tabela 'itens_pedido'
-- Agora estas inserções funcionarão, pois os produtos com ID 1, 5 e 3 existem.
INSERT INTO itens_pedido (id_pedido, id_produto, quantidade, preco_unitario_na_venda) VALUES
(1, 1, 2, 2400.00),
(1, 5, 1, 16.00);

INSERT INTO itens_pedido (id_pedido, id_produto, quantidade, preco_unitario_na_venda) VALUES
(2, 3, 3, 32.00);
