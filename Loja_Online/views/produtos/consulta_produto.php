<?php
require_once '../core/db_connection_pdo.php';

// Busca todos os produtos com JOIN da categoria
$stmt = $pdo->prepare("
    SELECT p.*, c.nome AS nome_categoria
    FROM Produto p
    LEFT JOIN Categoria c ON p.id_categoria = c.id_categoria
    ORDER BY p.data_registro DESC
");
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Captura mensagem da URL (ex: ?msg=sucesso)
$msg = $_GET['msg'] ?? null;

require '../core/db_disconnect_pdo.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2 class="mb-4 text-center">Consulta de Produtos</h2>

    <!-- Mensagem de alerta -->
    <?php if ($msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <!-- Tabela de produtos -->
    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (count($produtos) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>SKU</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Preço Custo</th>
                                <th>Preço Venda</th>
                                <th>Qtd Estoque</th>
                                <th>Categoria</th>
                                <th>Data Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtos as $produto): ?>
                                <tr>
                                    <td><?= htmlspecialchars($produto['sku']) ?></td>
                                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                                    <td><?= htmlspecialchars($produto['descricao']) ?></td>
                                    <td>R$ <?= number_format($produto['preco_custo'], 2, ',', '.') ?></td>
                                    <td>R$ <?= number_format($produto['preco_venda'], 2, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($produto['qtd_estoque']) ?></td>
                                    <td><?= $produto['nome_categoria'] ? htmlspecialchars($produto['nome_categoria']) : 'Nenhum' ?></td>
                                    <td><?= date('d/m/Y', strtotime($produto['data_registro'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    Nenhum produto cadastrado até o momento.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
