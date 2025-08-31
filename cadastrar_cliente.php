<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Cliente</title>
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
                <h2 class="text-center my-4">Cadastrar Cliente:</h2>
                <form class="mb-4" action="cadastrar_cliente.php" method="post">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo:</label>
                        <input type="text" name="nome" id="nome" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="cpf_cnpj" class="form-label">CPF ou CNPJ:</label>
                        <input type="text" name="cpf_cnpj" id="cpf_cnpj" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone:</label>
                        <input type="tel" name="telefone" id="telefone" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço:</label>
                        <input type="text" name="endereco" id="endereco" class="form-control">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Salvar Cliente</button>
                    </div>
                </form>

                <?php
                // Processa o formulário apenas se ele foi enviado
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
                    $cpf_cnpj = isset($_POST['cpf_cnpj']) ? trim($_POST['cpf_cnpj']) : '';
                    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : null; // Permite nulo
                    $endereco = isset($_POST['endereco']) ? trim($_POST['endereco']) : null; // Permite nulo

                    // Valida se os campos obrigatórios não estão vazios
                    if (!empty($nome) && !empty($cpf_cnpj) && !empty($email)) {
                        try {
                            // Prepara e executa a inserção no banco de dados
                            $consulta_sql = "INSERT INTO clientes (nome, cpf_cnpj, email, telefone, endereco) VALUES (?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($consulta_sql);
                            $stmt->execute([$nome, $cpf_cnpj, $email, $telefone, $endereco]);

                            echo <<<HEREDOC
                            <div class="alert alert-success text-center mt-3" role="alert">
                                Cliente "<b>{$nome}</b>" cadastrado com sucesso!
                            </div>
                            HEREDOC;
                        } catch (PDOException $e) {
                            // Trata erros, como CPF/CNPJ ou email duplicado
                            if ($e->errorInfo[1] == 1062) { // Código de erro para entrada duplicada
                                echo "<div class='alert alert-danger mt-3' role='alert'><b>Erro:</b> O CPF/CNPJ ou Email informado já está cadastrado.</div>";
                            } else {
                                echo "<div class='alert alert-danger mt-3' role='alert'><b>Erro ao cadastrar:</b> " . $e->getMessage() . "</div>";
                            }
                        }
                    } else {
                        // Mensagem de erro se algum campo obrigatório estiver vazio
                        echo <<<HEREDOC
                        <div class="alert alert-danger text-center mt-3" role="alert">
                            Por favor, preencha os campos obrigatórios (Nome, CPF/CNPJ e Email).
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
