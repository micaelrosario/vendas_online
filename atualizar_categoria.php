<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Categoria</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
</head>

<body>
    <?php
    // Imports do PHP
    require_once './src/nav_bar.php';
    require_once './src/db_connection_pdo.php';

    // Inicializa as variáveis da categoria
    $id_categoria = '';
    $nome = '';
    $descricao = '';
    $categoria_encontrada = false;

    // ETAPA 1: Carregar os dados da categoria para o formulário
    // Verifica se um ID foi passado pela URL (método GET)
    if (isset($_GET['id'])) {
        $id_categoria = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id_categoria) {
            try {
                // Busca os dados da categoria específica
                $sql = "SELECT * FROM categorias WHERE id_categoria = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$id_categoria]);
                $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

                // Se a categoria foi encontrada, preenche as variáveis
                if ($categoria) {
                    $categoria_encontrada = true;
                    $nome = $categoria['nome'];
                    $descricao = $categoria['descricao'];
                }
            } catch (PDOException $e) {
                echo "<div class='container'><div class='alert alert-danger'><b>Erro ao buscar categoria:</b> " . $e->getMessage() . "</div></div>";
            }
        }
    }
    ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <h2 class="text-center my-4">Atualizar Categoria</h2>

                <?php if ($categoria_encontrada): ?>
                    <form action="atualizar_categoria.php" method="post">
                        <input type="hidden" name="id_categoria" value="<?php echo htmlspecialchars($id_categoria); ?>">

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome da Categoria:</label>
                            <input type="text" name="nome" id="nome" class="form-control" value="<?php echo htmlspecialchars($nome); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição:</label>
                            <textarea name="descricao" id="descricao" class="form-control" rows="4" required><?php echo htmlspecialchars($descricao); ?></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                <?php elseif (isset($_GET['id'])): ?>
                    <div class="alert alert-danger text-center">Categoria não encontrada com o ID fornecido.</div>
                <?php endif; ?>


                <?php
                // ETAPA 2: Processar os dados enviados pelo formulário (método POST)
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Coleta e filtra os dados do formulário
                    $id_categoria_post = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);
                    $nome_post = trim(filter_input(INPUT_POST, 'nome'));
                    $descricao_post = trim(filter_input(INPUT_POST, 'descricao'));

                    // Validação simples para garantir que o nome não está vazio
                    if (!empty($nome_post) && $id_categoria_post) {
                        try {
                            $sql_update = <<<HEREDOC
                                UPDATE categorias SET 
                                    nome = ?, 
                                    descricao = ?
                                WHERE id_categoria = ?
                            HEREDOC;
                            
                            $stmt_update = $conn->prepare($sql_update);
                            $stmt_update->execute([
                                $nome_post,
                                $descricao_post,
                                $id_categoria_post
                            ]);

                            echo <<<HTML
                                <div class="alert alert-success text-center mt-4">
                                    Categoria atualizada com sucesso!
                                </div>
                                <h5 class="text-center">Redirecionando para a lista de categorias em 3 segundos...</h5>
                                <script>
                                    setTimeout(function() {
                                        window.location.href = "consulta_categoria.php";
                                    }, 3000);
                                </script>
                            HTML;

                        } catch (PDOException $e) {
                            echo "<div class='alert alert-danger mt-4'><b>Erro ao atualizar categoria:</b> " . $e->getMessage() . "</div>";
                        }
                    } else {
                        // Exibe erro se o nome estiver vazio
                        echo '<div class="alert alert-danger text-center mt-4">O nome da categoria é obrigatório.</div>';
                    }
                }

                // Desconecta apenas se não for um processamento de POST (para evitar fechar a conexão antes da hora)
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    require './src/db_disconnect_pdo.php';
                }
                ?>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>