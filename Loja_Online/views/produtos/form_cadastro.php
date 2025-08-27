<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos</title>
    <link href="../../css/index.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>

    <?php
    require_once '../../src/bootstrap_components.php';
    require_once '../../src/nav_bar.php';
    require_once '../../src/db_connection_pdo.php';

    $query_categorias = "SELECT id_categoria, nome FROM categorias;";
    // Prepara a consulta
    $stmt = $pdo->prepare($query_categorias);
    // Executa a consulta
    $stmt->execute();
    // Busca resultados em um array
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="container">

        <h2 class="text-center py-4">Cadastrar Produto:</h2>
        <form action="controllers/produtoController.php" method="post">
            <?php
            createInput("sku", "text", "SKU:");
            createInput("nome", "text", "Nome do Produto:");
            createInput("descricao", "text", "Descrição:");
            createInput("preco_custo", "number", "Preço do Produto:");
            createInput("preco_venda", "number", "Valor Total:");
            createInput("qtd_estoque", "number", "Quantidade no Estoque:");
            ?>

            <div class="mb-3">
                <label for="categorias" class="form-label">Selecione a Categoria</label>
                <select name="id_categoria" id="categorias" class="form-control">
                    <option value="">Nenhum</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo htmlspecialchars($categoria['id_categoria']); ?>">
                            <?php echo htmlspecialchars($categoria['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3 text-center">
                <button type="submit" class="btn btn-primary">Salvar Produto</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>