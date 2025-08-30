<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Video</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
</head>

<body>
    <?php
    // iremos incluir a biblioteca do bootstrap para PHP
    // o "require_once" garante que o arquivo bootstrap_components.php so sera incluido uma vez na pagina
    // ja o "require" simples permite que façamos varios requires do mesmo arquivo varias vezes
    require_once './src/bootstrap_components.php';

    // cria o menu (navbar) para que possamos navegar com mais facilidade pelo site
    // para alterar o menu, acesse o arquivo nav_bar.php e faca os ajustes necessarios
    require_once './src/nav_bar.php';

    // cria a conexão com o DB "streaming" 
    // (a informacão do nome do DB esta dentro do arquivo './db_connection_pdo.php'.
    // Caso voce precisse usar outro nome de DB, é so alterar o nome la no arquivo 
    // para o nome do novo DB.)
    require_once './src/db_connection_pdo.php';

    ?>

    <div class="container">
        <h2 class="text-center">Buscar vídeo por:</h2>
        <!-- estamos fazendo um POST para esta pagina (exemplo_db.php)  -->
        <!-- O PHP consegue detectar isso usando a variavel $_POST[id_do_campo] -->
        <!-- No nosso caso é $_POST["precoId"] -->
        <form class="col mb-4" action="consulta_video.php" method="post">
            <?php
            createInput("titulo", "text", "Titulo do Vídeo:");
            ?>
            <button type="submit" class="btn btn-primary">Enviar consulta</button>
        </form>

        <h2 class="text-center">Videos disponíveis para assistir</h2>
        <?php
        // Verifica se o usuario enviou alguma coisa usando o campo "titulo" atraves do metodo POST do HTTP
        if (isset($_POST['titulo'])) {
            // pega o preco que o usuario envio usando o botão "Enviar consulta", 
            // atraves do metodo HTTP POST e campo "precoId" do HTML.
            $titulo = "%" . $_POST["titulo"] . "%";
        } else {
            // O usuario não enviou consulta nenhuma usando o metodo POST, 
            // então definir titulo = "%" (pra que a consulta SQL busque por todos os videos)
            $titulo = "%";
        }

        try {
            // faz a consulta SELECT na tabela Produtos
            // "Pesquise pelos produtos cujo preco seja maior ou igual que um determinado valor, definido no comando execute"
            $consulta_sql = <<<HEREDOC
                SELECT * 
                FROM Video 
                WHERE titulo LIKE ? 
            HEREDOC;
            $resultados = $conn->prepare($consulta_sql);
            // vincula o valor $titulo ao primeiro ? da consulta SQL
            // Logo, a consulta se torna:
            //      SELECT * FROM Video WHERE titulo LIKE $titulo ;    
            // Este recurso é importante pra evitar SQL Injections.    
            $resultados->execute(array(
                $titulo
            ));
            // Se nenhuma ? existir na consulta, podemos simplesmente fazer:
            //     $resultados->execute();
            // Se mais de um ? existir, basta passar os valores pelo vetor abaixo na ordem das ?
            // Suponha a seguinte consulta SQL:
            //      SELECT * FROM Video WHERE titulo LIKE ? AND ano >= ? ; 
            // Nesse caso teriamos que fazer:
            //      $resultados->execute( array($titulo, $ano) );
            // onde $ano é uma variavel com um numero inteiro

            // pegue todos os dados da consulta como uma tabela
            // formato: array( "nome_atributo" => valor )
            $tabela_dados = $resultados->fetchAll(PDO::FETCH_ASSOC);

            // mostrar a tabela (primeiro parametro é o cabecalho da tabela, o segundo é matriz de dados)
            // no cabecalho precisamos dizer qual o nome do atributo (no DB) e nome que queremos mostrar para o usuario
            // formato: array( "atributo" => "nome_mostrado" )
            createTable(array(
                "id" => "ID",
                "ano" => "Ano",
                "titulo" => "Título",
                "duracao" => "Duração",
                "id_classificacao" => "Classificação",
                "sinopse" => "Sinopse",
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