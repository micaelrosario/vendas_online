<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Visualização</title>
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
        <h2 class="text-center">Buscar Visualização por:</h2>
        <!-- estamos fazendo um POST para esta pagina (exemplo_db.php)  -->
        <!-- O PHP consegue detectar isso usando a variavel $_POST[id_do_campo] -->
        <!-- No nosso caso é $_POST["precoId"] -->
        <form class="col mb-4" action="consulta_visualizacao.php" method="post">
            <?php
            createInput("id_usuario", "text", "Usuario:");
            createInput("id_video",   "text", "Video:");
            ?>
            <button type="submit" class="btn btn-primary">Enviar consulta</button>
        </form>

        <h2 class="text-center">Visualizações</h2>

        <?php
        $id_usuario = "";
        if (isset($_POST['id_usuario'])) {
            $id_usuario = $_POST['id_usuario'];
        }

        $id_video = "";
        if (isset($_POST['id_video'])) {
            $id_video = $_POST['id_video'];
        }

        try {
            // cria a consulta SELECT
            $consulta_params = array();
            $consulta_sql = <<<HEREDOC
                SELECT * 
                FROM Visualizacao
            HEREDOC;
            // usuario enviou alguma condicao ? Crie uma condicao WHERE no SQL
            if ($id_usuario !== "" && $id_video !== "") {
                //  podemos concatenar uma string no PHP da seguinte forma:
                //     $consulta_sql = $consulta_sql . "string aqui"
                //  tambem podemos fazer essa concatenacao assim:
                //     $consulta_sql .= "string aqui"
                $consulta_sql .= " WHERE id_usuario = ? AND id_video = ? ";
                // salve o valor a ser inserido no lugar de ?
                // no array $consulta_params
                array_push($consulta_params, $id_usuario);
                array_push($consulta_params, $id_video);
            } else if ($id_usuario !== "") {
                // temos somente a condicao do id_usuario
                $consulta_sql .= " WHERE id_usuario = ? ";
                array_push($consulta_params, $id_usuario);
            } else if ($id_video !== "") {
                // temos somente a condicao do id_video
                $consulta_sql .= " WHERE id_video = ? ";
                array_push($consulta_params, $id_video);
            }

            // executa a consulta SQL
            $resultados = $conn->prepare($consulta_sql);
            if (count($consulta_params) > 0)
                $resultados->execute($consulta_params);
            else
                $resultados->execute();
            $tabela_dados = $resultados->fetchAll(PDO::FETCH_ASSOC);

            // mostrar a tabela (primeiro parametro é o cabecalho da tabela, o segundo é tabela de dados)
            createTable(array(
                "id_usuario" => "ID Usuario",
                "id_video"   => "ID Video",
                "data_hora"  => "Data e Hora",
                "duracao_assistida" => "Duração Assistida",
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