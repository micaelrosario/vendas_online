<?php
// Imports do PHP
require_once './src/nav_bar.php';
require_once './src/db_connection_pdo.php';

// Aumenta a quantidade de produtos para fins de teste, se necessário
$quantidade_produtos = 8; // Exibe 8 produtos na página inicial

$produtos = [];
try {
    // Busca os produtos e a categoria associada
    // Adicionamos 'preco_venda' e 'imagem' conforme o design da Home
    // O filtro 'p.preco_venda IS NOT NULL' foi adicionado para mostrar apenas produtos com preço de venda definido
    $consulta_sql = "SELECT p.id_produto, p.nome, p.descricao, p.preco_venda, p.imagem, c.nome AS nome_categoria FROM produtos AS p INNER JOIN categorias AS c ON p.id_categoria = c.id_categoria WHERE p.preco_venda IS NOT NULL ORDER BY p.data_registro DESC LIMIT ?";
    $stmt = $conn->prepare($consulta_sql);
    $stmt->bindValue(1, $quantidade_produtos, PDO::PARAM_INT);
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='container'><div class='alert alert-danger'>Erro ao carregar produtos: " . $e->getMessage() . "</div></div>";
    $produtos = []; // Garante que a variável seja um array vazio em caso de erro
}

// Lógica para as imagens do banner (você pode ajustar os caminhos e textos)
$banners = [
    ['src' => 'img/jeans.jpg', 'alt' => 'Banner de Jeans'],
    ['src' => 'img/shoes.jpg', 'alt' => 'Banner de Sapatos'],
    ['src' => 'img/banner_gamer.jpg', 'alt' => 'Banner de Lançamento 3'],
];

require './src/db_disconnect_pdo.php';
?>
<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Loja Online</title>
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

        .carousel-item img {
            max-height: 400px;
            object-fit: cover;
            width: 100%;
        }

        /* Estilo do Rodapé */
        footer {
            background-color: #343a40;
            /* Cor escura do Bootstrap */
            color: #f8f9fa !important;
            /* Cor clara do texto */
            padding: 3rem 0;
        }

        footer li {
            color: #f8f9fa !important;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="container mb-5">
        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php foreach ($banners as $key => $banner): ?>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?php echo $key; ?>" class="<?php echo ($key === 0) ? 'active' : ''; ?>" aria-current="<?php echo ($key === 0) ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $key + 1; ?>"></button>
                <?php endforeach; ?>
            </div>
            <div class="carousel-inner">
                <?php foreach ($banners as $key => $banner): ?>
                    <div class="carousel-item <?php echo ($key === 0) ? 'active' : ''; ?>" data-bs-interval="5000">
                        <img src="<?php echo htmlspecialchars($banner['src']); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($banner['alt']); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <div class="container my-5">
        <h2 class="text-center mb-4">Produtos Recentes</h2>
        <?php if (!empty($produtos)): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <?php foreach ($produtos as $produto): ?>
                    <div class="col">
                        <a href="detalhes_produto.php?id=<?php echo htmlspecialchars($produto['id_produto']); ?>" class="card card-produto h-100 shadow-sm">
                            <?php
                            $caminho_imagem = !empty($produto['imagem']) ? 'img/' . $produto['imagem'] : 'img/default_product.jpg';
                            ?>
                            <img src="<?php echo htmlspecialchars($caminho_imagem); ?>" class="card-img-top" alt="Imagem do produto <?php echo htmlspecialchars($produto['nome']); ?>">

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($produto['nome']); ?></h5>
                                <p class="card-text text-muted small"><?php echo htmlspecialchars($produto['nome_categoria']); ?></p>
                                <p class="h4 mt-auto">R$ <?php echo number_format($produto['preco_venda'], 2, ',', '.'); ?></p>
                                <div class="d-grid mt-2">
                                    <button class="btn btn-primary" type="button">Detalhes</button>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">Nenhum produto para exibir no momento.</div>
        <?php endif; ?>
    </div>

    <footer class="mt-5 py-3 bg-gray text-center border-top">
        <div class="container">
            <p class="m-0">
                &copy; <?php echo date("Y"); ?> Loja Online. Todos os direitos reservados.
            </p>
        </div>
    </footer>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>