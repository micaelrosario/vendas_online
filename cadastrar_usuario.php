<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
</head>

<body>
    <?php
    // import do PHP
    require_once './src/bootstrap_components.php';
    require_once './src/nav_bar.php';
    require_once './src/db_connection_pdo.php';
    ?>

    <div class="container">
        <h2 class="text-center">Cadastrar usuário:</h2>
        <!-- estamos fazendo um POST para esta pagina (exemplo_db.php)  -->
        <!-- O PHP consegue detectar isso usando a variavel $_POST[id_do_campo] -->
        <!-- No nosso caso é $_POST["precoId"] -->
        <form class="col mb-4" action="cadastrar_usuario.php" method="post">
            <?php
            createInput("email", "email",    "Email:");
            createInput("senha", "password", "Senha:");
            createInput("primeiro_nome", "text", "Primeiro nome:");
            createInput("sobrenome",     "text", "Sobrenome:");
            createInput("dt_nascimento", "date", "Data de nascimento:");
            ?>
            <button type="submit" class="btn btn-primary">Enviar consulta</button>
        </form>

        <?php
        // Verifica se o usuario enviou dados atraves do metodo POST do HTTP
        $email = "";
        if (isset($_POST['email'])) {
            $email = $_POST["email"];
        }

        $senha = "";
        if (isset($_POST['senha'])) {
            $senha = $_POST["senha"];
        }

        $primeiro_nome = "";
        if (isset($_POST['primeiro_nome'])) {
            $primeiro_nome = $_POST["primeiro_nome"];
        }

        $sobrenome = "";
        if (isset($_POST['sobrenome'])) {
            $sobrenome = $_POST["sobrenome"];
        }

        $dt_nascimento = "";
        if (isset($_POST['dt_nascimento'])) {
            $dt_nascimento = $_POST["dt_nascimento"];
        }

        // testa se todos os atributos foram preenchidos pelo usuario
        if (
            $email !== "" &&
            $senha !== "" &&
            $primeiro_nome !== "" &&
            $sobrenome !== "" &&
            $dt_nascimento !== ""
        ) {
            try {
                // faz a consulta SELECT
                $consulta_sql = <<<HEREDOC
                    INSERT INTO Usuario (email, senha, primeiro_nome, sobrenome, dt_nascimento, dt_registro)
                    VALUES ( ?, ?, ?, ?, ?, ? )
                HEREDOC;
                $resultados = $conn->prepare($consulta_sql);
                $resultados->execute(array(
                    $email,
                    $senha,
                    $primeiro_nome,
                    $sobrenome,
                    $dt_nascimento,
                    date('Y-m-d')
                ));
                echo <<<HEREDOC
                    <h4 class="text-center">Cadastro feito com sucesso</h4>
                HEREDOC;
            } catch (PDOException $e) {
                // Handle the error
                echo "<b>Error:</b> " . $e->getMessage();
            }
        } else {
            echo <<<HEREDOC
            <h4 class="text-center">Nenhum dado fornecido pelo usuário ou os dados fornecidos sao insuficientes</h4>
            HEREDOC;
        }

        // desconecte o PHP do DB teste (importante fazer isso sempre que terminarmos de acessar o DB)
        require './src/db_disconnect_pdo.php';
        ?>

    </div>

    <!-- carrega javascript do Bootstrap -->
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>