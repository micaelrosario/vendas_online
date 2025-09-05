-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/09/2025 às 12:18
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sistema_vendas_online`
--
CREATE DATABASE IF NOT EXISTS `sistema_vendas_online` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sistema_vendas_online`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `prefixo_sku` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nome`, `descricao`, `prefixo_sku`) VALUES
(1, 'Eletrônicos', 'Dispositivos eletrônicos e acessórios', 'ELT'),
(2, 'Livros', 'Livros de ficção, não-ficção e acadêmicos', 'LIV'),
(3, 'Vestuário', 'Roupas, calçados e acessórios de moda', 'VES'),
(4, 'Papelaria', 'Papeis e Cartaz', 'PPA');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `cpf_cnpj` varchar(18) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `cpf_cnpj`, `nome`, `endereco`, `telefone`, `email`) VALUES
(1, '111.222.333-44', 'João da Silva', 'Rua das Flores, 123, Centro', '(11) 98765-4321', 'joao.silva@email.com'),
(2, '555.666.777-88', 'Maria Oliveira', 'Avenida Principal, 456, Bairro Novo', '(21) 99887-6655', 'maria.o@email.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

DROP TABLE IF EXISTS `itens_pedido`;
CREATE TABLE `itens_pedido` (
  `id_item_pedido` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `id_produto` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario_na_venda` decimal(10,2) NOT NULL
) ;

--
-- Despejando dados para a tabela `itens_pedido`
--

INSERT INTO `itens_pedido` (`id_item_pedido`, `id_pedido`, `id_produto`, `quantidade`, `preco_unitario_na_venda`) VALUES
(6, 8, 9, 4, 1400.00);

--
-- Acionadores `itens_pedido`
--
DROP TRIGGER IF EXISTS `trg_ajustar_estoque_apos_pedido`;
DELIMITER $$
CREATE TRIGGER `trg_ajustar_estoque_apos_pedido` AFTER INSERT ON `itens_pedido` FOR EACH ROW BEGIN
    UPDATE produtos
    SET quantidade_estoque = quantidade_estoque - NEW.quantidade
    WHERE id_produto = NEW.id_produto;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_envio` varchar(50) DEFAULT 'Pendente',
  `codigo_rastreio` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_cliente`, `data_pedido`, `status_envio`, `codigo_rastreio`) VALUES
(8, 1, '2025-09-04 19:24:26', 'Pendente', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE `produtos` (
  `id_produto` int(11) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco_venda` decimal(10,2) NOT NULL,
  `quantidade_estoque` int(11) NOT NULL,
  `data_registro` date DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL
) ;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id_produto`, `sku`, `nome`, `descricao`, `preco_venda`, `quantidade_estoque`, `data_registro`, `id_categoria`, `imagem`) VALUES
(1, 'ELT001', 'Iphone 16 Pro Titâneo', 'Cinco cores vibrantes. Chip A18. Compre o iPhone 16. Explore os detalhes. Conheça o iPhone 16. Maior duração da bateria. Câmera principal de 48 MP. iOS 18. Controle da Câmera. Captura espacial.', 8889.00, 10, '2025-09-03', 1, '68b971db3afb5.jpg'),
(9, 'ELT002', 'PS4', 'Console da Playstation', 1400.00, 5, '2025-09-04', 1, 'prod_68b9e65f2e1e19.14920537.png'),
(10, 'ELT003', 'Echo Dot (4ª Geração)', 'Caixa de som inteligente com Alexa, controle por voz.', 350.00, 45, '2025-09-05', 1, 'prod_68babb4c85bd68.51567761.jpg'),
(11, 'ELT004', 'Kindle (11ª geração)', 'Leitor de e-books com luz embutida e bateria de longa duração.', 450.00, 23, '2025-09-05', 1, 'prod_68babbb19ea500.29708873.jpg'),
(12, 'ELT005', 'PlayStation 5 Slim Digital', 'Console de última geração, modelo slim, sem entrada para disco.', 4500.00, 8, '2025-09-05', 1, 'prod_68babbd5704994.35402971.jpg'),
(13, 'ELT006', 'Smart Lâmpada Wi-Fi Positivo Casa Inteligente', 'Lâmpada controlável por app e assistente de voz.', 90.00, 48, '2025-09-05', 1, 'prod_68babc04048fe6.07776003.jpg');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `cpf_cnpj` (`cpf_cnpj`);

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id_item_pedido`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_produto` (`id_produto`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id_produto`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id_item_pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  ADD CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id_produto`);

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
