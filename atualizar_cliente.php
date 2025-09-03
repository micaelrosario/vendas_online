<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Cliente</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
</head>

<body>
    <?php
    // Imports do PHP
    require_once './src/nav_bar.php';
    require_once './src/db_connection_pdo.php';

    // Inicializa as variáveis do cliente
    $id_cliente = '';
    $nome = '';
    $cpf_cnpj = '';
    $email = '';
    $endereco = '';
    $telefone = '';
    $cliente_encontrado = false;

    // ETAPA 1: Carregar os dados do cliente para o formulário
    // Verifica se um ID foi passado pela URL (método GET)
    if (isset($_GET['id'])) {
        $id_cliente = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id_cliente) {
            try {
                // Busca os dados do cliente específico
                $sql = "SELECT * FROM clientes WHERE id_cliente = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$id_cliente]);
                $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

                // Se o cliente foi encontrado, preenche as variáveis
                if ($cliente) {
                    $cliente_encontrado = true;
                    $nome = $cliente['nome'];
                    $cpf_cnpj = $cliente['cpf_cnpj'];
                    $email = $cliente['email'];
                    $endereco = $cliente['endereco'];
                    $telefone = $cliente['telefone'];
                }
            } catch (PDOException $e) {
                echo "<div class='container'><div class='alert alert-danger'><b>Erro ao buscar cliente:</b> " . $e->getMessage() . "</div></div>";
            }
        }
    }
    ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <h2 class="text-center my-4">Atualizar Cliente</h2>

                <?php if ($cliente_encontrado): ?>
                    <form action="atualizar_cliente.php" method="post">
                        <input type="hidden" name="id_cliente" value="<?php echo htmlspecialchars($id_cliente); ?>">

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo / Razão Social:</label>
                            <input type="text" name="nome" id="nome" class="form-control" value="<?php echo htmlspecialchars($nome); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cpf_cnpj" class="form-label">CPF / CNPJ:</label>
                            <input type="text" name="cpf_cnpj" id="cpf_cnpj" class="form-control" value="<?php echo htmlspecialchars($cpf_cnpj); ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
                            </div>
                            <div class="col-md-5 mb-3">
                                <label for="telefone" class="form-label">Telefone:</label>
                                <input type="text" name="telefone" id="telefone" class="form-control" value="<?php echo htmlspecialchars($telefone); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="endereco" class="form-label">Endereço:</label>
                            <input type="text" name="endereco" id="endereco" class="form-control" value="<?php echo htmlspecialchars($endereco); ?>">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                <?php elseif (isset($_GET['id'])): ?>
                    <div class="alert alert-danger text-center">Cliente não encontrado com o ID fornecido.</div>
                <?php endif; ?>


                <?php
                // ETAPA 2: Processar os dados enviados pelo formulário (método POST)
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Coleta e filtra os dados do formulário
                    $id_cliente_post = filter_input(INPUT_POST, 'id_cliente', FILTER_VALIDATE_INT);
                    $nome_post = trim(filter_input(INPUT_POST, 'nome'));
                    $cpf_cnpj_post = trim(filter_input(INPUT_POST, 'cpf_cnpj'));
                    $email_post = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
                    $endereco_post = trim(filter_input(INPUT_POST, 'endereco'));
                    $telefone_post = trim(filter_input(INPUT_POST, 'telefone'));

                    // Validação simples para garantir que os campos principais não estão vazios
                    if (!empty($nome_post) && !empty($cpf_cnpj_post) && $id_cliente_post) {
                        try {
                            $sql_update = <<<HEREDOC
                                UPDATE clientes SET 
                                    nome = ?, 
                                    cpf_cnpj = ?, 
                                    email = ?, 
                                    endereco = ?, 
                                    telefone = ?
                                WHERE id_cliente = ?
                            HEREDOC;
                            
                            $stmt_update = $conn->prepare($sql_update);
                            $stmt_update->execute([
                                $nome_post,
                                $cpf_cnpj_post,
                                $email_post,
                                $endereco_post,
                                $telefone_post,
                                $id_cliente_post
                            ]);

                            echo <<<HTML
                                <div class="alert alert-success text-center mt-4">
                                    Cliente atualizado com sucesso!
                                </div>
                                <h5 class="text-center">Redirecionando para a lista de clientes em 3 segundos...</h5>
                                <script>
                                    setTimeout(function() {
                                        window.location.href = "consulta_cliente.php";
                                    }, 3000);
                                </script>
                            HTML;

                        } catch (PDOException $e) {
                            // Erro de chave duplicada (CPF/CNPJ)
                            if ($e->getCode() == '23000') {
                                echo "<div class='alert alert-danger mt-4'><b>Erro ao atualizar:</b> Já existe um cliente com este CPF/CNPJ.</div>";
                            } else {
                                echo "<div class='alert alert-danger mt-4'><b>Erro ao atualizar cliente:</b> " . $e->getMessage() . "</div>";
                            }
                        }
                    } else {
                        // Exibe erro se os campos obrigatórios estiverem vazios
                        echo '<div class="alert alert-danger text-center mt-4">Nome e CPF/CNPJ são obrigatórios.</div>';
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