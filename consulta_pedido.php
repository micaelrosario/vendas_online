<?php
session_start();
// Imports do PHP
require_once './src/nav_bar.php';
require_once './src/db_connection_pdo.php';

$pedidos = [];
$mensagem_erro = '';

try {
    // 1. Consultar todos os pedidos
    $sql_pedidos = "SELECT id_pedido, id_cliente, data_pedido, status_envio, codigo_rastreio FROM pedidos ORDER BY data_pedido DESC";
    $stmt_pedidos = $conn->prepare($sql_pedidos);
    $stmt_pedidos->execute();
    $pedidos_db = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);

    // 2. Para cada pedido, buscar seus itens correspondentes
    if (!empty($pedidos_db)) {
        foreach ($pedidos_db as $pedido) {
            $id_pedido = $pedido['id_pedido'];
            
            $sql_itens = "
                SELECT 
                    ip.quantidade, 
                    ip.preco_unitario_na_venda, 
                    p.nome AS nome_produto, 
                    p.imagem
                FROM itens_pedido AS ip
                INNER JOIN produtos AS p ON ip.id_produto = p.id_produto
                WHERE ip.id_pedido = ?
            ";
            $stmt_itens = $conn->prepare($sql_itens);
            $stmt_itens->execute([$id_pedido]);
            $itens_do_pedido = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);

            // Adicionar os itens ao array do pedido
            $pedido['itens'] = $itens_do_pedido;
            $pedidos[] = $pedido;
        }
    }

} catch (PDOException $e) {
    $mensagem_erro = "Erro ao carregar os pedidos: " . $e->getMessage();
}

require './src/db_disconnect_pdo.php';
?>
<!DOCTYPE html>
<html lang="pt_br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Pedidos</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .pedido-card {
            border: 1px solid #e9ecef;
            border-radius: .5rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: #f8f9fa;
        }
        .item-pedido-card {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #dee2e6;
            padding: .5rem 0;
        }
        .item-pedido-card:last-child {
            border-bottom: none;
        }
        .item-pedido-card img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: .25rem;
            margin-right: 1rem;
        }
    </style>
</head>
<body>

    <div class="container my-5">
        <h2 class="text-center mb-4">Meus Pedidos</h2>
        
        <?php if (!empty($pedidos)): ?>
            <?php foreach ($pedidos as $pedido): ?>
                <div class="pedido-card shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="mb-0">Pedido #<?php echo htmlspecialchars($pedido['id_pedido']); ?></h4>
                            <p class="text-muted mb-0">Data: <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-<?php echo ($pedido['status_envio'] == 'Pendente' ? 'warning' : 'success'); ?>"><?php echo htmlspecialchars($pedido['status_envio']); ?></span>
                            <?php if ($pedido['codigo_rastreio']): ?>
                                <p class="mb-0 mt-1">Rastreio: <strong><?php echo htmlspecialchars($pedido['codigo_rastreio']); ?></strong></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr>
                    <h5>Itens do Pedido:</h5>
                    <?php 
                    $total_pedido = 0;
                    foreach ($pedido['itens'] as $item): 
                        $subtotal_item = $item['quantidade'] * $item['preco_unitario_na_venda'];
                        $total_pedido += $subtotal_item;
                    ?>
                        <div class="item-pedido-card">
                            <?php $caminho_imagem = !empty($item['imagem']) ? 'img/' . $item['imagem'] : 'img/default_product.jpg'; ?>
                            <img src="<?php echo htmlspecialchars($caminho_imagem); ?>" alt="Imagem do produto">
                            <div class="item-details flex-grow-1">
                                <p class="m-0"><strong><?php echo htmlspecialchars($item['nome_produto']); ?></strong></p>
                                <p class="m-0 text-muted">Qtd: <?php echo htmlspecialchars($item['quantidade']); ?> x R$ <?php echo number_format($item['preco_unitario_na_venda'], 2, ',', '.'); ?></p>
                            </div>
                            <p class="m-0 fw-bold">R$ <?php echo number_format($subtotal_item, 2, ',', '.'); ?></p>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="text-end">
                        <h4>Total do Pedido: R$ <?php echo number_format($total_pedido, 2, ',', '.'); ?></h4>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif (!empty($mensagem_erro)): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($mensagem_erro); ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Você ainda não fez nenhum pedido.
            </div>
        <?php endif; ?>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>