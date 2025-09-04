<?php
session_start();
// Imports do PHP
require_once './src/nav_bar.php';
require_once './src/db_connection_pdo.php';

// Simula itens no carrinho (você deve adicionar itens aqui de outra página)
// Em um sistema real, você adicionaria produtos ao clicar no botão "Adicionar ao Carrinho"
// Este é apenas um exemplo para que a página funcione.
if (!isset($_SESSION['carrinho'])) {
    // Adicione IDs de produtos de teste aqui
    $_SESSION['carrinho'] = [1, 2, 4]; 
}

$itens_carrinho = [];
if (!empty($_SESSION['carrinho'])) {
    try {
        // Cria uma string de placeholders para a consulta SQL
        $placeholders = implode(',', array_fill(0, count($_SESSION['carrinho']), '?'));
        $sql = "SELECT p.id_produto, p.nome, p.preco_venda, p.imagem, c.nome AS nome_categoria FROM produtos AS p INNER JOIN categorias AS c ON p.id_categoria = c.id_categoria WHERE p.id_produto IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->execute($_SESSION['carrinho']);
        $itens_carrinho = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $mensagem_erro = "Erro ao carregar itens do carrinho: " . $e->getMessage();
        $itens_carrinho = [];
    }
}

require './src/db_disconnect_pdo.php';
?>
<!DOCTYPE html>
<html lang="pt_br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    <style>
        .card-produto {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .card-produto:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .carrinho-item {
            display: flex;
            align-items: center;
            border: 1px solid #dee2e6;
            border-radius: .25rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background-color: #fff;
        }
        .carrinho-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: .25rem;
        }
        .carrinho-item .item-details {
            flex-grow: 1;
            margin-left: 1rem;
        }
    </style>
</head>
<body>

    <div class="container my-5">
        <h2 class="text-center mb-4">Meu Carrinho</h2>
        <form action="processar_pedido.php" method="POST">
            <?php if (!empty($itens_carrinho)): ?>
                <?php foreach ($itens_carrinho as $item): ?>
                    <div class="carrinho-item shadow-sm">
                        <input type="checkbox" name="produtos_selecionados[]" value="<?php echo htmlspecialchars($item['id_produto']); ?>" class="form-check-input me-3" checked>
                        
                        <?php 
                        $caminho_imagem = !empty($item['imagem']) ? 'img/' . $item['imagem'] : 'img/default_product.png'; 
                        ?>
                        <img src="<?php echo htmlspecialchars($caminho_imagem); ?>" alt="Imagem do produto <?php echo htmlspecialchars($item['nome']); ?>">

                        <div class="item-details">
                            <h5><?php echo htmlspecialchars($item['nome']); ?></h5>
                            <p class="text-muted m-0"><?php echo htmlspecialchars($item['nome_categoria']); ?></p>
                            <p class="h5 text-primary mt-2">R$ <?php echo number_format($item['preco_venda'], 2, ',', '.'); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg">Finalizar Pedido</button>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    Seu carrinho está vazio. <a href="index.php" class="alert-link">Explore nossos produtos!</a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>