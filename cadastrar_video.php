<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Video</title>
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
        <h2 class="text-center">Cadastrar video:</h2>
        <!-- estamos fazendo um POST para esta pagina (exemplo_db.php)  -->
        <!-- O PHP consegue detectar isso usando a variavel $_POST[id_do_campo] -->
        <!-- No nosso caso é $_POST["precoId"] -->
        <form class="col mb-4" action="cadastrar_video.php" method="post">
            <?php
            $vetor_classificacao = array();
            try {
                // faz a consulta SELECT
                $consulta_sql = <<<HEREDOC
                    SELECT * 
                    FROM classificacao                    
                HEREDOC;
                $resultados = $conn->prepare($consulta_sql);
                $resultados->execute();
                $tabela_dados = $resultados->fetchAll(PDO::FETCH_ASSOC);

                // pegar cada linha obtida da consulta SQL
                foreach ($tabela_dados as $linha) {
                    $id = $linha["id"];
                    $nome = $linha["nome"];
                    $vetor_classificacao[$id] = $nome;
                }
            } catch (PDOException $e) {
                // Handle the error
                echo "<b>Error:</b> " . $e->getMessage();
            }

            // criar formulario de registro de dados
            createInput("titulo",    "text",   "Titulo do filme:");
            createInput("ano",       "number", "Ano de exibição:");
            createInput("sinopse",   "text",   "Sinopse:");
            createInput("duracao",   "time",   "Duração:");
            createSelect("classificacao", "Classificação", $vetor_classificacao);
            ?>
            <button type="submit" class="btn btn-primary">Enviar consulta</button>
        </form>

        <?php
        // Verifica se o usuario enviou dados atraves do metodo POST do HTTP        
        if (
            isset($_POST['titulo']) &&
            isset($_POST['ano']) &&
            isset($_POST['sinopse']) &&
            isset($_POST['duracao']) &&
            isset($_POST['classificacao'])
        ) {
            try {
                // faz a consulta SELECT
                $consulta_sql = <<<HEREDOC
                    INSERT INTO video (titulo, ano, sinopse, duracao, id_classificacao)
                    VALUES            (   ?  ,  ? ,    ?   ,    ?   ,       ?      )
                HEREDOC;
                $resultados = $conn->prepare($consulta_sql);
                $resultados->execute(array(
                    $_POST['titulo'],
                    $_POST['ano'],
                    $_POST['sinopse'],
                    $_POST['duracao'],
                    $_POST['classificacao']
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

        // desconecte o PHP do DB (importante fazer isso sempre que terminarmos de acessar o DB)
        require './src/db_disconnect_pdo.php';
        ?>

    </div>

    <!-- carrega javascript do Bootstrap -->
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>