<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Categoria</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
</head>

<body>
    <?php
    require_once './src/bootstrap_components.php';
    require_once './src/nav_bar.php';
    require_once './src/db_connection_pdo.php';
    ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <h2 class="text-center my-4">Cadastrar Categoria:</h2>
                <form class="mb-4" action="cadastrar_categoria.php" method="post">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome da Categoria:</label>
                        <input type="text" name="nome" id="nome" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição:</label>
                        <textarea name="descricao" id="descricao" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Salvar Categoria</button>
                    </div>
                </form>

                <?php
                // Processa o formulário apenas se ele foi enviado
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
                    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';

                    // Valida se os campos não estão vazios
                    if (!empty($nome) && !empty($descricao)) {
                        try {
                            // Prepara e executa a inserção no banco de dados
                            $consulta_sql = "INSERT INTO categorias (nome, descricao) VALUES (?, ?)";
                            $stmt = $conn->prepare($consulta_sql);
                            $stmt->execute([$nome, $descricao]);

                            echo <<<HEREDOC
                            <div class="alert alert-success text-center mt-3" role="alert">
                                Categoria "<b>{$nome}</b>" cadastrada com sucesso!
                            </div>
                            HEREDOC;
                        } catch (PDOException $e) {
                            // Mostra um erro caso a inserção falhe
                            echo "<div class='alert alert-danger mt-3' role='alert'><b>Error:</b> " . $e->getMessage() . "</div>";
                        }
                    } else {
                        // Mensagem de erro se algum campo estiver vazio
                        echo <<<HEREDOC
                        <div class="alert alert-danger text-center mt-3" role="alert">
                            Por favor, preencha todos os campos.
                        </div>
                        HEREDOC;
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
