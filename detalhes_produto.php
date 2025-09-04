<?php
// Imports do PHP
require_once './src/nav_bar.php';
require_once './src/db_connection_pdo.php';

$produto = null;
$mensagem = '';

if (isset($_GET['id'])) {
    $id_produto = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id_produto) {
        try {
            // Busca todos os detalhes do produto
            // Note o uso de LEFT JOIN para garantir que o produto seja encontrado mesmo sem categoria
            $sql = "SELECT p.*, c.nome AS nome_categoria, c.descricao AS descricao_categoria FROM produtos AS p LEFT JOIN categorias AS c ON p.id_categoria = c.id_categoria WHERE p.id_produto = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_produto]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                $mensagem = "Produto não encontrado.";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao buscar detalhes do produto: " . $e->getMessage();
        }
    } else {
        $mensagem = "ID do produto inválido.";
    }
} else {
    $mensagem = "Nenhum produto especificado.";
}

require './src/db_disconnect_pdo.php';
?>
<!DOCTYPE html>
<html lang="pt_br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Produto</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
</head>
<body>

    <div class="container my-5">
        <?php if ($produto): ?>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <?php 
                    $caminho_imagem = !empty($produto['imagem']) ? 'img/' . $produto['imagem'] : 'img/default_product.jpg'; 
                    ?>
                    <img src="<?php echo htmlspecialchars($caminho_imagem); ?>" class="img-fluid rounded shadow-sm" alt="Imagem de <?php echo htmlspecialchars($produto['nome']); ?>">
                </div>

                <div class="col-md-6 my-4">
                    <h1 class="mb-3"><?php echo htmlspecialchars($produto['nome']); ?></h1>
                    <p class="lead text-muted"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="h2 text-success">R$ <?php echo number_format($produto['preco_venda'], 2, ',', '.'); ?></p>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($produto['nome_categoria'] ?? 'Sem Categoria'); ?></span>
                    </div>
                    <p><b>SKU:</b> <?php echo htmlspecialchars($produto['sku']); ?></p>
                    <p><b>Em estoque:</b> <?php echo htmlspecialchars($produto['quantidade_estoque']); ?></p>
                    
                    <button class="btn btn-primary btn-lg mt-3">Adicionar ao Carrinho</button>
                    <a href="index.php" class="btn btn-outline-secondary btn-lg mt-3">Voltar aos Produtos</a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>