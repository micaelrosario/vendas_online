<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Avaliação</title>
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
        <h2 class="text-center">Buscar avaliação por:</h2>
        <!-- estamos fazendo um POST para esta pagina (exemplo_db.php)  -->
        <!-- O PHP consegue detectar isso usando a variavel $_POST[id_do_campo] -->
        <!-- No nosso caso é $_POST["precoId"] -->
        <form class="col mb-4" action="consulta_avaliacao.php" method="post">
            <?php
            createInput("nota_min", "text", "Nota mínima:");
            ?>
            <button type="submit" class="btn btn-primary">Enviar consulta</button>
        </form>

        <h2 class="text-center">Gêneros disponíveis</h2>

        <?php
        // Verifica se o usuario enviou dados atraves do metodo POST do HTTP
        $nota_min = 0;
        if (isset($_POST['nota_min'])) {
            $nota_min = $_POST["nota_min"];
        }

        try {
            // faz a consulta SELECT
            $consulta_sql = <<<HEREDOC
                SELECT * 
                FROM Avaliacao 
                WHERE nota >= ? 
            HEREDOC;
            $resultados = $conn->prepare($consulta_sql);
            $resultados->execute(array(
                $nota_min
            ));
            $tabela_dados = $resultados->fetchAll(PDO::FETCH_ASSOC);

            // mostrar a tabela (primeiro parametro é o cabecalho da tabela, o segundo é tabela de dados)
            createTable(array(
                "id_video"   => "ID Vídeo",
                "id_usuario" => "ID Usuário",
                "nota"         => "Nota",
                "dt_avaliacao" => "Data da Avaliação",
            ), $tabela_dados);
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