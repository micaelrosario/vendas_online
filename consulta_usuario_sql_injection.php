<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Usuário</title>
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
        <h2 class="text-center">Buscar usuário por:</h2>
        <!-- estamos fazendo um POST para esta pagina (exemplo_db.php)  -->
        <!-- O PHP consegue detectar isso usando a variavel $_POST[id_do_campo] -->
        <!-- No nosso caso é $_POST["precoId"] -->
        <form class="col mb-4" action="consulta_usuario_sql_injection.php" method="post">
            <?php
            createInput("email", "text", "Email:");
            ?>
            <button type="submit" class="btn btn-primary">Enviar consulta</button>
        </form>

        <h2 class="text-center">Usuários cadastrados</h2>

        <?php
        // Verifica se o usuario enviou dados atraves do metodo POST do HTTP
        $email = "%";
        if (isset($_POST['email'])) {
            /*
            VULNERABLE TO THE FOLLOWING INPUTS:
                " AND primeiro_nome LIKE "João%
                "; DROP TABLE visualizacao ; SELECT "
                "; DROP TABLE video_genero ; DROP TABLE genero; SELECT "
            */
            /* OUTPUTS:
                $email = %" AND primeiro_nome LIKE "João%%
            */
            $email = "%" . $_POST["email"] . "%";
        }

        try {
            // faz a consulta SELECT
            $consulta_sql = <<<HEREDOC
                SELECT * 
                FROM Usuario 
                WHERE email LIKE "$email"
            HEREDOC;
            echo $consulta_sql;
            $resultados = $conn->query($consulta_sql);  // Direct execution of raw SQL

            $tabela_dados = $resultados->fetchAll(PDO::FETCH_ASSOC);

            // mostrar a tabela (primeiro parametro é o cabecalho da tabela, o segundo é tabela de dados)
            createTable(array(
                "id" => "ID",
                "email" => "Email",
                "primeiro_nome" => "Nome",
                "sobrenome"     => "Sobrenome",
                "dt_nascimento" => "Data de Nascimento",
                "dt_registro"   => "Data de Registro",
            ), $tabela_dados, array(
                array("id" => '<a href="atualizar_usuario.php?id=:id"><img src="img/edit_icon.png" width="32pt" height="32pt"></a>'),
                array("id" => '<a href="deletar_usuario.php?id=:id"><img src="img/delete_icon.png" width="32pt" height="32pt"></a>'),
            ));
        } catch (PDOException $e) {
            // Handle the error
            echo "<b>Error:</b> " . $e->getMessage();
        }

        // desconecte o PHP do DB teste (importante fazer isso sempre que terminarmos de acessar o DB)
        require './src/db_disconnect_pdo.php';
        ?>

    </div>

    <!-- carrega javascript do Bootstrap -->
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>