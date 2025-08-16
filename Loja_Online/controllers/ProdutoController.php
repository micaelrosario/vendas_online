<?php
// import do PHP
require_once '../core/db_connection_pdo.php';

// Inicializa as variáveis
$sku = "";
$nome = "";
$descricao = "";
$preco_custo = "";
$preco_venda = "";
$qtd_estoque = "";
$id_categoria = "";

// Verifica e sanitiza os dados do POST
if (isset($_POST['sku'])) {
    $sku = trim($_POST['sku']);
}
if (isset($_POST['nome'])) {
    $nome = trim($_POST['nome']);
}
if (isset($_POST['descricao'])) {
    $descricao = trim($_POST['descricao']);
}
if (isset($_POST['preco_custo'])) {
    $preco_custo = $_POST['preco_custo'];
}
if (isset($_POST['preco_venda'])) {
    $preco_venda = $_POST['preco_venda'];
}
if (isset($_POST['qtd_estoque'])) {
    $qtd_estoque = $_POST['qtd_estoque'];
}
if (isset($_POST['id_categoria'])) {
    $id_categoria = $_POST['id_categoria'];
}

// ---
## Validação dos Dados


// Verificação de campos obrigatórios e formato numérico
if (
    !empty($sku) &&
    !empty($nome) &&
    !empty($descricao) &&
    is_numeric($preco_custo) &&
    is_numeric($preco_venda) &&
    is_numeric($qtd_estoque) &&
    !empty($id_categoria)
) {
    // Validação de regra de negócio: preço de venda deve ser maior que o preço de custo
    if ($preco_venda > $preco_custo) {
        try {
            // Faz a consulta INSERT
            $consulta_sql = <<<HEREDOC
                INSERT INTO produtos (sku, nome, descricao, preco_custo, preco_venda, qtd_estoque, data_registro, id_categoria)
                VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )
            HEREDOC;
            $resultados = $pdo->prepare($consulta_sql);
            $resultados->execute(array(
                $sku,
                $nome,
                $descricao,
                $preco_custo,
                $preco_venda,
                $qtd_estoque,
                date('Y-m-d'), // Formato Y-m-d
                $id_categoria
            ));

            echo <<<HEREDOC
                <h4 class="text-center">Produto cadastrado com sucesso</h4>
            HEREDOC;
        } catch (PDOException $e) {
            // Lida com o erro
            echo "<b>Error:</b> " . $e->getMessage();
        }
    } else {
        echo <<<HEREDOC
            <h4 class="text-center">Erro: O preço de venda deve ser maior que o preço de custo.</h4>
        HEREDOC;
    }
} else {
    echo <<<HEREDOC
        <h4 class="text-center">Erro: Por favor, preencha todos os campos corretamente.</h4>
    HEREDOC;
}

// desconecte o PHP do DB teste (importante fazer isso sempre que terminarmos de acessar o DB)
require './src/db_disconnect_pdo.php';
?>