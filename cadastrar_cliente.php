<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Cliente</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php
    require_once './src/nav_bar.php';
    require_once './src/db_connection_pdo.php';
    ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <h2 class="text-center my-4">Cadastrar Cliente</h2>

                <form class="mb-4" action="cadastrar_cliente.php" method="post">
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo / Razão Social:</label>
                        <input type="text" name="nome" id="nome" class="form-control" placeholder="Digite o nome ou razão social" required>
                    </div>

                    <div class="mb-3">
                        <label for="cpf_cnpj" class="form-label">CPF / CNPJ:</label>
                        <input type="text" name="cpf_cnpj" id="cpf_cnpj" class="form-control" placeholder="000.000.000-00 ou 00.000.000/0000-00" required>
                    </div>

                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="seuemail@exemplo.com" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="telefone" class="form-label">Telefone:</label>
                            <input type="tel" name="telefone" id="telefone" class="form-control" placeholder="(73) 99999-9999">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço:</label>
                        <input type="text" name="endereco" id="endereco" class="form-control" placeholder="Ex: Rua das Flores, 123, Centro">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Salvar Cliente</button>
                    </div>
                </form>

                <?php
                // Processa o formulário apenas se ele foi enviado via POST
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Coleta e limpa os dados usando filter_input para mais segurança
                    $nome = trim(filter_input(INPUT_POST, 'nome'));
                    $cpf_cnpj = trim(filter_input(INPUT_POST, 'cpf_cnpj'));
                    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
                    $telefone = trim(filter_input(INPUT_POST, 'telefone'));
                    $endereco = trim(filter_input(INPUT_POST, 'endereco'));

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
                            <script>
                                // Limpa o formulário após 2 segundos
                                setTimeout(() => {
                                    document.querySelector('form').reset();
                                    // Remove a mensagem de sucesso
                                    document.querySelector('.alert-success').remove();
                                }, 2000);
                            </script>
                            HEREDOC;

                        } catch (PDOException $e) {
                            // Trata erros, como CPF/CNPJ ou email duplicado
                            if ($e->getCode() == '23000') { // Código de erro SQLSTATE para violação de integridade (chave duplicada)
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