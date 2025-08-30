<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- carrega o BOOTSTRAP (para melhorar o design do site) -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php

    // faz a conexao com o DB
    require_once './src/db_connection_pdo.php';

    // faz a consulta SELECT ( o <<<HEREDOC marca o inicio de uma string
    //                        que se inicia em uma linha e termina
    //                        somente quando a palavra HEREDOC aparece
    //                        novamente)
    $consulta_sql = <<<HEREDOC
    SELECT * 
    FROM classificacao                    
HEREDOC;
    $resultados = $conn->prepare($consulta_sql);
    $resultados->execute();
    $tabela_dados = $resultados->fetchAll(PDO::FETCH_ASSOC);

    echo "A variavel tabela_dados é uma matriz (tabela), com o seguinte formato: <br>";
    echo <<<HEREDOC
array(<br>
    linha1,<br>
    linha2,<br>
    linha3,<br>
    ...<br>
)<br>
<br>
onde linha1 = array( "nome_da_coluna" => valor )<br><br>
HEREDOC;

    echo "Mostrando tabela_dados: <br><br>";
    print_r($tabela_dados);
    echo "<br><br>";

    echo "Acessando dados de cada linha: <br><br>";
    foreach ($tabela_dados as $linha) {
        $id = $linha["id"];
        $nome = $linha["nome"];
        $idade_minima = $linha["idade_min"];
        echo "ID: $id - Nome: $nome - Idade minima: $idade_minima";
        echo "<br>";
    }

    echo "<br><br>";
    echo "<b>Desafio</b>:";
    echo "Alterem o codigo PHP acima para, ao inves de mostrar o conteudo de \$tabela_dados como um texto simples, mostrar esse mesmo conteudo como uma tabela do HTML.<br>";

    echo "<br><br>";
    echo "<b>DICAS</b>: <br>";
    echo "O comando 'echo' é o equivalente do print() no Python. Ele imprime strings ou variaveis.<br>";
    echo "O PHP é geralmente usado para criar paginas HTML usando o comando 'echo'. Isto é, o echo gera texto que é interpretado pelo navegador como codigo HTML. Um exemplo disso é a tag 'br', que no HTML serve para quebra de linha.<br>";
    echo "Tudo que começa com \$ é uma variavel do PHP. Uma lista (ou vetor) pode ser construida usando o comando 'array()'. Um elemento pode ser adicionado no vetor usando o comando array_push(). O vetor pode ser visualizado usando o comando print_r() ao inves do 'echo'.<br>";
    echo "Uma tabela HTML é criada usando as tags 'table', 'thead', 'tbody', 'tr', 'th', e 'td'. 'thead' define o cabeçalho da tabela, enquanto 'tbody' define o corpo (conteudo). 'tr' define as linhas, enquanto 'th' e 'td' definem as celulas de cada linha. As classes CSS usadas na tabela pertencem ao Boostrap (usamos elas somente para melhorar a estetica das tabelas). A tag div tambem é usada pelo Boostrap para deixar a tabela responsiva (se ajustar dinamicamente ao tamanho da tela). Veja o exemplo abaixo.<br><br>";

    echo <<<HEREDOC
Exemplo de tabela:<br>
<div class="container table-responsive">
    <table class="table table-hover align-middle border ">
        <thead>        
            <tr>
                <th scope="col"> Coluna 1 </th> 
                <th scope="col"> Coluna 2 </th>
                <th scope="col"> Coluna 3 </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td scope="row"> Valor 1 </td>
                <td scope="row"> Valor 2 </td>
                <td scope="row"> Valor 3 </td>
            </tr>
            <tr>
                <td scope="row"> Valor 4 </td>
                <td scope="row"> Valor 5 </td>
                <td scope="row" > Valor 6 </td>
            </tr>
        </tbody>
    </table>
</div>
HEREDOC;

    // desconecte o PHP do DB (importante fazer isso sempre que terminarmos de acessar o DB)
    require './src/db_disconnect_pdo.php';

    // o ? > marca o fim do PHP, tudo que vira depois desse comando é interpretado como HTML SIMPLES    
    ?>

    <!-- note que, os comentarios no PHP sao escritos com //, enquanto que no HTML eles sao escritos de outra forma -->

    <!-- carrega javascript do Bootstrap -->
    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>