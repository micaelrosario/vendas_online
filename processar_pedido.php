<?php
session_start();
require_once './src/nav_bar.php';
require_once './src/db_connection_pdo.php';

$produtos_selecionados = $_POST['produtos_selecionados'] ?? [];
$quantidades = $_POST['quantidade'] ?? [];
$mensagem = '';
$sucesso = false;

if (!empty($produtos_selecionados)) {
    try {
        $conn->beginTransaction();
        
        // 1. Inserir um novo pedido na tabela 'pedidos'
        // Simulação de um usuário logado
        $id_cliente = 1; 
        $sql_pedido = "INSERT INTO pedidos (id_cliente, data_pedido, status_envio) VALUES (?, NOW(), 'Pendente')";
        $stmt_pedido = $conn->prepare($sql_pedido);
        $stmt_pedido->execute([$id_cliente]);
        $id_pedido = $conn->lastInsertId();
        
        // 2. Inserir os itens do pedido na tabela 'itens_pedido'
        $sql_item = "INSERT INTO itens_pedido (id_pedido, id_produto, quantidade, preco_unitario_na_venda) VALUES (?, ?, ?, ?)";
        $stmt_item = $conn->prepare($sql_item);

        foreach ($produtos_selecionados as $id_produto) {
            // Busque o preço do produto no banco de dados para evitar manipulação de dados
            $sql_preco = "SELECT preco_venda FROM produtos WHERE id_produto = ?";
            $stmt_preco = $conn->prepare($sql_preco);
            $stmt_preco->execute([$id_produto]);
            $preco_unitario_na_venda = $stmt_preco->fetchColumn();
            
            // Obter a quantidade do array POST
            $quantidade_item = $quantidades[$id_produto] ?? 1;

            if ($preco_unitario_na_venda) {
                // Ajustado o nome da variável e o nome da coluna no execute
                $stmt_item->execute([$id_pedido, $id_produto, $quantidade_item, $preco_unitario_na_venda]);
            }
        }
        
        $conn->commit();
        $mensagem = "Pedido realizado com sucesso! ID do pedido: " . $id_pedido;
        $sucesso = true;
        
        // Limpar o carrinho após a finalização do pedido
        $_SESSION['carrinho'] = [];

    } catch (PDOException $e) {
        $conn->rollBack();
        $mensagem = "Erro ao processar o pedido: " . $e->getMessage();
    }

} else {
    $mensagem = "Nenhum produto foi selecionado para o pedido.";
}

require './src/db_disconnect_pdo.php';
?>
<!DOCTYPE html>
<html lang="pt_br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processando Pedido</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include './src/nav_bar.php'; ?>

    <div class="container my-5">
        <div class="text-center">
            <?php if ($sucesso): ?>
                <div class="alert alert-success">
                    <h4><?php echo htmlspecialchars($mensagem); ?></h4>
                    <p>Obrigado por sua compra! Você receberá mais informações por e-mail.</p>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <h4>Erro ao processar o pedido</h4>
                    <p><?php echo htmlspecialchars($mensagem); ?></p>
                </div>
            <?php endif; ?>
            <a href="index.php" class="btn btn-primary mt-3">Voltar à Página Inicial</a>
        </div>
    </div>
    
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>