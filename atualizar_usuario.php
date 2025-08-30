<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Usuário</title>
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
        <h2 class="text-center">Atualizar usuário:</h2>
        <!-- estamos fazendo um POST para esta pagina (exemplo_db.php)  -->
        <!-- O PHP consegue detectar isso usando a variavel $_POST[id_do_campo] -->
        <!-- No nosso caso é $_POST["precoId"] -->
        <form class="col mb-4" action="atualizar_usuario.php" method="post">
            <?php
            $id = "";
            $email = "";
            $senha = "";
            $primeiro_nome = "";
            $sobrenome = "";
            $dt_nascimento = "";

            // PEGUE O ID ENVIADO PELO USUARIO
            if (isset($_GET['id'])) {
                try {
                    $id = $_GET["id"];
                    // faz a consulta SELECT
                    $consulta_sql = <<<HEREDOC
                        SELECT * 
                        FROM Usuario 
                        WHERE id = ? 
                    HEREDOC;
                    $resultados = $conn->prepare($consulta_sql);
                    $resultados->execute(array(
                        $id
                    ));
                    $dados = $resultados->fetch(PDO::FETCH_ASSOC);
                    if ($dados !== false && $dados !== null) {
                        $email = $dados['email'];
                        $senha = $dados['senha'];
                        $primeiro_nome = $dados['primeiro_nome'];
                        $sobrenome = $dados['sobrenome'];
                        $dt_nascimento = $dados['dt_nascimento'];
                    }
                } catch (PDOException $e) {
                    // Handle the error
                    echo "<b>Error:</b> " . $e->getMessage();
                }
            }

            createInput("id",            "number",    "ID:",        $id,    false);
            createInput("email",         "email",     "Email:",     $email);
            createInput("senha",         "password",  "Senha:",     $senha);
            createInput("primeiro_nome", "text",      "Nome:",      $primeiro_nome);
            createInput("sobrenome",     "text",      "Sobrenome:", $sobrenome);
            createInput("dt_nascimento", "date",      "Data de Nascimento:", $dt_nascimento);
            ?>
            <button type="submit" class="btn btn-primary">Enviar consulta</button>
        </form>

        <?php
        // Verifica se o usuario enviou dados atraves do metodo POST do HTTP
        $id = null;
        if (isset($_POST['id'])) {
            $id = $_POST["id"];
        }
        $email = null;
        if (isset($_POST['email'])) {
            $email = $_POST["email"];
        }
        $senha = null;
        if (isset($_POST['senha'])) {
            $senha = $_POST["senha"];
        }
        $primeiro_nome = null;
        if (isset($_POST['primeiro_nome'])) {
            $primeiro_nome = $_POST["primeiro_nome"];
        }
        $sobrenome = null;
        if (isset($_POST['sobrenome'])) {
            $sobrenome = $_POST["sobrenome"];
        }
        $dt_nascimento = null;
        if (isset($_POST['dt_nascimento'])) {
            $dt_nascimento = $_POST["dt_nascimento"];
        }

        // testa se os atributos de chave primaria foram preenchidos pelo usuario
        if ($id !== null) {
            try {
                // faz a consulta SELECT
                $consulta_sql = <<<HEREDOC
                    UPDATE Usuario 
                    SET email = ?, senha = ?, primeiro_nome = ?, sobrenome = ?, dt_nascimento = ?
                    WHERE id = ?
                HEREDOC;
                $resultados = $conn->prepare($consulta_sql);
                $resultados->execute(array(
                    $email,
                    $senha,
                    $primeiro_nome,
                    $sobrenome,
                    $dt_nascimento,
                    $id,
                ));
                echo <<<HEREDOC
                    <h4 class="text-center">Usuario atualizado com sucesso.</h4>
                    <h5 class="text-center">Redirecionando de volta em 3 segundos...</h5>

                    <!-- redirecionar usuario de volta -->
                    <script>
                        // Redirect after 3 seconds
                        setTimeout(function() {
                            window.location.href = "consulta_usuario.php";
                        }, 3000);
                    </script>
                HEREDOC;
            } catch (PDOException $e) {
                // Handle the error
                echo "<b>Error:</b> " . $e->getMessage();
            }
        } else {
            echo <<<HEREDOC
            <h4 class="text-center">Nenhum dado fornecido ou os dados fornecidos sao insuficientes</h4>
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