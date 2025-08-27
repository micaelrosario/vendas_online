<?php
// import do PHP
require_once '../src/db_connection_pdo.php';

// Inicializa as variáveis

$sku = trim($_POST['sku'] ?? "");
$nome = trim($_POST['nome'] ?? "");
$descricao = trim($_POST['descricao'] ?? "");
$preco_custo = $_POST['preco_custo'] ?? "";
$preco_venda = $_POST['preco_venda'] ?? "";
$qtd_estoque = $_POST['qtd_estoque'] ?? "";
$id_categoria = !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null;

// ---
## Validação dos Dados


// Verificação de campos obrigatórios e formato numérico
if (
    !empty($sku) &&
    !empty($nome) &&
    !empty($descricao) &&
    is_numeric($preco_custo) &&
    is_numeric($preco_venda) &&
    is_numeric($qtd_estoque)
) {
    if ($preco_venda > $preco_custo) {
        try {
            $sql = <<<SQL
                INSERT INTO produtos (sku, nome, descricao, preco_custo, preco_venda, qtd_estoque, data_registro, id_categoria)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            SQL;
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $sku,
                $nome,
                $descricao,
                $preco_custo,
                $preco_venda,
                $qtd_estoque,
                date('Y-m-d'),
                $id_categoria
            ]);

            header("Location: consulta_produto.php?msg=sucesso");
            exit;
        } catch (PDOException $e) {
            echo "<b>Error:</b> " . $e->getMessage();
        }
    } else {
        echo "<h4 class='text-center'>Erro: O preço de venda deve ser maior que o preço de custo.</h4>";
    }
} else {
    echo "<h4 class='text-center'>Erro: Por favor, preencha todos os campos corretamente.</h4>";
}

require '../src/db_disconnect_pdo.php';
?>