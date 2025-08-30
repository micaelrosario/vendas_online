<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deletar Usuário</title>
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
        <h2 class="text-center">Deletar usuário:</h2>
        <!-- estamos fazendo um POST para esta pagina (exemplo_db.php)  -->
        <!-- O PHP consegue detectar isso usando a variavel $_POST[id_do_campo] -->
        <!-- No nosso caso é $_POST["precoId"] -->
        <form class="col mb-4" action="deletar_usuario.php" method="post">
            <?php
            // PEGUE O ID ENVIADO PELO USUARIO
            $id = "";
            if (isset($_GET['id'])) {
                $id = $_GET["id"];
            }
            createInput("id", "number",    "ID:", $id,  false);
            ?>
            <button type="submit" class="btn btn-primary">Enviar consulta</button>
        </form>

        <?php
        // Verifica se o usuario enviou dados atraves do metodo POST do HTTP
        $id = "";
        if (isset($_POST['id'])) {
            $id = $_POST["id"];
        }

        // testa se todos os atributos foram preenchidos pelo usuario
        if ($id !== "") {
            try {
                // faz a consulta SELECT
                $consulta_sql = <<<HEREDOC
                    DELETE FROM Usuario 
                    WHERE id = ?
                HEREDOC;
                $resultados = $conn->prepare($consulta_sql);
                $resultados->execute(array(
                    $id
                ));
                echo <<<HEREDOC
                    <h4 class="text-center">Usuario deletado com sucesso</h4>
                    <h5 class="text-center">Redirecionando de volta em 3 segundos...</h5>

                    <!-- redirecionar usuario de volta -->
                    <script>
                        // Redirect after 5 seconds (3000 milliseconds)
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