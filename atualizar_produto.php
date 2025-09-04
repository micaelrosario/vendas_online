<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Produto</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
</head>

<body>
    <?php
    // Imports do PHP
    require_once './src/nav_bar.php';
    require_once './src/db_connection_pdo.php';

    // Inicializa as variáveis do produto (sem preco_venda)
    $id_produto = '';
    $nome = '';
    $descricao = '';
    $preco_venda = '';
    $quantidade_estoque = '';
    $id_categoria_produto = '';
    $produto_encontrado = false;

    // ETAPA 1: Carregar os dados do produto para o formulário
    if (isset($_GET['id'])) {
        $id_produto = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id_produto) {
            try {
                // Busca os dados do produto específico
                $sql = "SELECT * FROM produtos WHERE id_produto = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$id_produto]);
                $produto = $stmt->fetch(PDO::FETCH_ASSOC);

                // Se o produto foi encontrado, preenche as variáveis
                if ($produto) {
                    $produto_encontrado = true;
                    $nome = $produto['nome'];
                    $descricao = $produto['descricao'];
                    $preco_venda = $produto['preco_venda'];
                    $quantidade_estoque = $produto['quantidade_estoque'];
                    $id_categoria_produto = $produto['id_categoria'];
                }
            } catch (PDOException $e) {
                echo "<div class='container'><div class='alert alert-danger'><b>Erro ao buscar produto:</b> " . $e->getMessage() . "</div></div>";
            }
        }
    }
    
    // Busca todas as categorias para preencher o <select>
    $categorias = [];
    try {
        $stmt_cat = $conn->query("SELECT id_categoria, nome FROM categorias ORDER BY nome ASC");
        $categorias = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<div class='container'><div class='alert alert-danger'><b>Erro ao buscar categorias:</b> " . $e->getMessage() . "</div></div>";
    }
    ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <h2 class="text-center my-4">Atualizar Produto</h2>

                <?php if ($produto_encontrado): ?>
                    <form action="atualizar_produto.php" method="post">
                        <input type="hidden" name="id_produto" value="<?php echo htmlspecialchars($id_produto); ?>">

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome:</label>
                            <input type="text" name="nome" id="nome" class="form-control" value="<?php echo htmlspecialchars($nome); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição:</label>
                            <textarea name="descricao" id="descricao" class="form-control" rows="4" required><?php echo htmlspecialchars($descricao); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="preco_venda" class="form-label">Preço de Custo:</label>
                            <input type="number" name="preco_venda" id="preco_venda" class="form-control" step="0.01" value="<?php echo htmlspecialchars($preco_venda); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="quantidade_estoque" class="form-label">Quantidade em Estoque:</label>
                            <input type="number" name="quantidade_estoque" id="quantidade_estoque" class="form-control" value="<?php echo htmlspecialchars($quantidade_estoque); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="id_categoria" class="form-label">Categoria:</label>
                            <select name="id_categoria" id="id_categoria" class="form-select" required>
                                <option value="">Selecione uma categoria</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo htmlspecialchars($categoria['id_categoria']); ?>" <?php echo ($categoria['id_categoria'] == $id_categoria_produto) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($categoria['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                <?php elseif (isset($_GET['id'])): ?>
                    <div class="alert alert-danger text-center">Produto não encontrado com o ID fornecido.</div>
                <?php endif; ?>


                <?php
                // ETAPA 2: Processar os dados enviados pelo formulário (método POST)
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Coleta e filtra os dados do formulário
                    $id_produto_post = filter_input(INPUT_POST, 'id_produto', FILTER_VALIDATE_INT);
                    $nome_post = trim(filter_input(INPUT_POST, 'nome'));
                    $descricao_post = trim(filter_input(INPUT_POST, 'descricao'));
                    $preco_venda_post = filter_input(INPUT_POST, 'preco_venda', FILTER_VALIDATE_FLOAT);
                    $quantidade_estoque_post = filter_input(INPUT_POST, 'quantidade_estoque', FILTER_VALIDATE_INT);
                    $id_categoria_post = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);

                    // Validações das regras de negócio
                    $erros = [];
                    if ($quantidade_estoque_post < 0) {
                        $erros[] = "A quantidade em estoque não pode ser negativa.";
                    }
                    if (empty($nome_post) || empty($id_categoria_post) || $id_produto_post === false) {
                        $erros[] = "Todos os campos obrigatórios devem ser preenchidos.";
                    }

                    if (empty($erros)) {
                        try {
                            $sql_update = <<<HEREDOC
                                UPDATE produtos SET 
                                    nome = ?, 
                                    descricao = ?, 
                                    preco_venda = ?, 
                                    quantidade_estoque = ?, 
                                    id_categoria = ?
                                WHERE id_produto = ?
                            HEREDOC;
                            
                            $stmt_update = $conn->prepare($sql_update);
                            $stmt_update->execute([
                                $nome_post,
                                $descricao_post,
                                $preco_venda_post,
                                $quantidade_estoque_post,
                                $id_categoria_post,
                                $id_produto_post
                            ]);

                            echo <<<HTML
                                <div class="alert alert-success text-center mt-4">
                                    Produto atualizado com sucesso!
                                </div>
                                <h5 class="text-center">Redirecionando para a lista de produtos em 3 segundos...</h5>
                                <script>
                                    setTimeout(function() {
                                        window.location.href = "consulta_produto.php";
                                    }, 3000);
                                </script>
                            HTML;

                        } catch (PDOException $e) {
                            echo "<div class='alert alert-danger mt-4'><b>Erro ao atualizar produto:</b> " . $e->getMessage() . "</div>";
                        }
                    } else {
                        // Exibe os erros de validação
                        echo '<div class="alert alert-danger text-center mt-4">';
                        foreach ($erros as $erro) {
                            echo $erro . '<br>';
                        }
                        echo '</div>';
                    }
                }

                require './src/db_disconnect_pdo.php';
                ?>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>