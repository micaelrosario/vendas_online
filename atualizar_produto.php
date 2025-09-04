<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Produto</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php
    // Imports do PHP
    require_once './src/nav_bar.php';
    require_once './src/db_connection_pdo.php';

    // Inicializa as variáveis do produto
    $id_produto = '';
    $nome = '';
    $descricao = '';
    $preco_venda = '';
    $quantidade_estoque = '';
    $id_categoria_produto = '';
    $imagem_atual = ''; // Nova variável para a imagem existente
    $produto_encontrado = false;
    $erros = []; // Inicializa o array de erros para exibir as mensagens

    // ETAPA 1: Carregar os dados do produto para o formulário
    if (isset($_GET['id'])) {
        $id_produto = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $_POST['preco_venda'] = str_replace(',', '.', $_POST['preco_venda']);

        // Mantenha o filter_input como está
        $preco_venda_post = filter_input(INPUT_POST, 'preco_venda', FILTER_VALIDATE_FLOAT);

        if ($id_produto) {
            try {
                // Busca os dados do produto específico, incluindo o nome da imagem
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
                    $imagem_atual = $produto['imagem'];
                }
            } catch (PDOException $e) {
                $erros[] = "<b>Erro ao buscar produto:</b> " . $e->getMessage();
            }
        }
    }

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
        if ($quantidade_estoque_post < 0) {
            $erros[] = "A quantidade em estoque não pode ser negativa.";
        }
        if (empty($nome_post) || empty($id_categoria_post) || $id_produto_post === false) {
            $erros[] = "Todos os campos obrigatórios devem ser preenchidos.";
        }

        // Inicializa o nome da imagem com o valor atual do banco
        $nome_imagem = $_POST['imagem_atual'] ?? null;

        // Verifica se uma nova imagem foi enviada
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $nome_imagem = uniqid() . '.' . $extensao;
            $destino = 'img/' . $nome_imagem;

            // Move o arquivo temporário para o destino final
            if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                $erros[] = "Erro ao fazer o upload da imagem.";
                $nome_imagem = $_POST['imagem_atual'] ?? null; // Mantém a imagem antiga em caso de erro
            } else {
                // Se o upload foi bem-sucedido, exclui a imagem antiga se ela existir e não for a padrão
                $imagem_antiga = $_POST['imagem_atual'] ?? null;
                if ($imagem_antiga && $imagem_antiga != 'default_product.png' && file_exists('img/' . $imagem_antiga)) {
                    unlink('img/' . $imagem_antiga);
                }
            }
        }

        if (empty($erros)) {
            try {
                $sql_update = <<<HEREDOC
                UPDATE produtos SET 
                    nome = ?, 
                    descricao = ?, 
                    preco_venda = ?, 
                    quantidade_estoque = ?, 
                    id_categoria = ?,
                    imagem = ?
                WHERE id_produto = ?
                HEREDOC;

                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->execute([
                    $nome_post,
                    $descricao_post,
                    $preco_venda_post,
                    $quantidade_estoque_post,
                    $id_categoria_post,
                    $nome_imagem,
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
        }
    }

    // Busca todas as categorias para preencher o <select>
    $categorias = [];
    try {
        $stmt_cat = $conn->query("SELECT id_categoria, nome FROM categorias ORDER BY nome ASC");
        $categorias = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $erros[] = "<b>Erro ao buscar categorias:</b> " . $e->getMessage();
    }
    ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <h2 class="text-center my-4">Atualizar Produto</h2>
                <?php if (!empty($erros) && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <div class="alert alert-danger text-center mt-4">
                        <?php foreach ($erros as $erro): ?>
                            <?php echo $erro . '<br>'; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($produto_encontrado): ?>
                    <form action="atualizar_produto.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_produto" value="<?php echo htmlspecialchars($id_produto); ?>">
                        <input type="hidden" name="imagem_atual" value="<?php echo htmlspecialchars($imagem_atual); ?>">

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
                            <input type="number" name="preco_venda" id="preco_venda" class="form-control" step="any" value="<?php echo htmlspecialchars($preco_venda); ?>" required>
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

                        <div class="mb-3">
                            <label for="imagem" class="form-label">Imagem:</label>
                            <input type="file" name="imagem" id="imagem" class="form-control">
                            <?php if ($imagem_atual): ?>
                                <small class="form-text text-muted mt-2">Imagem atual:</small><br>
                                <img src="<?php echo 'img/' . htmlspecialchars($imagem_atual); ?>" alt="Imagem atual" style="max-width: 150px; margin-top: 5px;">
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            <a href="consulta_produto.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                <?php elseif (isset($_GET['id'])): ?>
                    <div class="alert alert-danger text-center">Produto não encontrado com o ID fornecido.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php require './src/db_disconnect_pdo.php'; ?>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>