<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Categoria</title>
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

        <h2 class="text-center py-4">Cadastrar Categoria:</h2>
        <form action="ProdutoController.php" method="post">
            <?php
            createInput("nome", "text", "Nome:");
            createInput("categoria", "text", "Descrição:");
            ?>

            <div class="mb-3 text-center">
                <button type="submit" class="btn btn-primary">Salvar Categoria</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>