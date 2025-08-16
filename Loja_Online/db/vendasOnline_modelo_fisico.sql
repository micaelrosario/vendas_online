-- --------------------------------------------------------
-- Criação do Banco de Dados
-- --------------------------------------------------------
-- Remove o banco de dados se ele já existir, para garantir um início limpo
DROP DATABASE IF EXISTS sistema_vendas_online;
CREATE DATABASE sistema_vendas_online CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usa o banco de dados recém-criado
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
    preco_venda DECIMAL(10, 2) NOT NULL,
    quantidade_estoque INT NOT NULL,
    data_registro DATE,
    id_categoria INT,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria),
    CHECK (preco_venda > preco_custo),
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

-- Dados de exemplo para a tabela 'produtos'
-- A chave estrangeira 'id_categoria' aponta para a categoria correspondente.
INSERT INTO produtos (sku, nome, descricao, preco_custo, preco_venda, quantidade_estoque, id_categoria) VALUES
('ELT001', 'Smartphone XYZ', 'Smartphone de última geração com 128GB.', 1500.00, 2500.00, 50, 1),
('ELT002', 'Smartwatch ABC', 'Relógio inteligente com monitoramento de saúde.', 300.00, 600.00, 100, 1),
('LIV001', 'O Guia do Mochileiro', 'Livro de ficção científica clássico.', 20.00, 45.00, 200, 2),
('LIV002', 'A Arte da Guerra', 'Clássico livro sobre estratégia militar.', 15.00, 30.00, 150, 2),
('VES001', 'Camiseta Básica', 'Camiseta de algodão na cor preta.', 10.00, 25.00, 500, 3);

-- Dados de exemplo para a tabela 'clientes'
INSERT INTO clientes (cpf_cnpj, nome, endereco, telefone, email) VALUES
('111.222.333-44', 'João da Silva', 'Rua das Flores, 123, Centro', '(11) 98765-4321', 'joao.silva@email.com'),
('555.666.777-88', 'Maria Oliveira', 'Avenida Principal, 456, Bairro Novo', '(21) 99887-6655', 'maria.o@email.com');

-- Dados de exemplo para a tabela 'pedidos'
-- Note que 'id_cliente' aponta para os clientes criados acima.
INSERT INTO pedidos (id_cliente, status_envio, codigo_rastreio) VALUES
(1, 'Enviado', 'RA123456789BR'),  -- Pedido do João
(2, 'Pendente', NULL);              -- Pedido da Maria

-- Dados de exemplo para a tabela 'itens_pedido'
-- Pedido 1 (do João): comprou 2 smartphones e 1 camiseta
INSERT INTO itens_pedido (id_pedido, id_produto, quantidade, preco_unitario_na_venda) VALUES
(1, 1, 2, 2500.00), -- 2 smartphones
(1, 5, 1, 25.00);  -- 1 camiseta

-- Pedido 2 (da Maria): comprou 3 livros 'O Guia do Mochileiro'
INSERT INTO itens_pedido (id_pedido, id_produto, quantidade, preco_unitario_na_venda) VALUES
(2, 3, 3, 45.00);  -- 3 livros