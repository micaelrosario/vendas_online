<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Visualização com JOIN</title>
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
        <form class="col mb-4" action="consulta_visualizacao_join.php" method="post">
            <?php
            createInput("email", "text", "Email do Usuario:");
            createInput("titulo",   "text", "Título do Video:");
            ?>
            <button type="submit" class="btn btn-primary">Enviar consulta</button>
        </form>

        <h2 class="text-center">Visualizações (com Juncão / JOIN)</h2>

        <?php
        $email = "%";
        if (isset($_POST['email'])) {
            $email = "%" . $_POST['email'] . "%";
        }

        $titulo = "%";
        if (isset($_POST['titulo'])) {
            $titulo = "%" . $_POST['titulo'] . "%";
        }

        try {
            // cria a consulta SELECT
            $consulta_params = array();
            $consulta_sql = <<<HEREDOC
                SELECT vid.titulo, vid.ano, vid.duracao, 
                    u.email, u.primeiro_nome, u.sobrenome, 
                    vis.data_hora, vis.duracao_assistida
                FROM Video AS vid, Usuario AS u, Visualizacao AS vis
                WHERE vid.id = vis.id_video AND u.id = vis.id_usuario
            HEREDOC;
            // usuario enviou alguma condicao ? Crie uma condicao WHERE no SQL
            if ($email !== "" && $titulo !== "") {
                //  podemos concatenar uma string no PHP da seguinte forma:
                //     $consulta_sql = $consulta_sql . "string aqui"
                //  tambem podemos fazer essa concatenacao assim:
                //     $consulta_sql .= "string aqui"
                $consulta_sql .= " AND u.email LIKE ? AND vid.titulo LIKE ? ";
                // salve o valor a ser inserido no lugar de ?
                // no array $consulta_params
                array_push($consulta_params, $email);
                array_push($consulta_params, $titulo);
            } else if ($email !== "") {
                // temos somente a condicao do email
                $consulta_sql .= " AND u.email LIKE ? ";
                array_push($consulta_params, $email);
            } else if ($titulo !== "") {
                // temos somente a condicao do titulo
                $consulta_sql .= " AND vid.titulo LIKE ? ";
                array_push($consulta_params, $titulo);
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
                "ano" => "Ano",
                "titulo" => "Título do Vídeo",
                "duracao" => "Duração Total",

                "email" => "Email do Usuário",
                "primeiro_nome" => "Nome",
                "sobrenome" => "Sobrenome",

                "data_hora"  => "Assistido em",
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