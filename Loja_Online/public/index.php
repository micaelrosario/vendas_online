<?php
    // Inclui o arquivo de conexão
    require_once 'conexao.php';

    // Obter todos os produtos
    $stmt_produtos = $pdo->query("SELECT p.nome, p.preco_venda, p.quantidade_estoque, c.nome AS categoria_nome FROM produtos p JOIN categorias c ON p.id_categoria = c.id_categoria");
    $produtos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);

    // Obter todos os clientes
    $stmt_clientes = $pdo->query("SELECT * FROM clientes");
    $clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Vendas - Teste DB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Sistema de Vendas</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4 text-center">Painel de Teste de Consultas</h1>

        <div class="card mb-5">
            <div class="card-header bg-primary text-white">
                <h2>Produtos Cadastrados</h2>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Preço de Venda</th>
                            <th>Estoque</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                                <td><?php echo htmlspecialchars($produto['categoria_nome']); ?></td>
                                <td>R$ <?php echo number_format($produto['preco_venda'], 2, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($produto['quantidade_estoque']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white">
                <h2>Clientes Cadastrados</h2>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF/CNPJ</th>
                            <th>E-mail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['cpf_cnpj']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div> <footer class="mt-5 text-center text-muted">
        <p>&copy; 2025 Sistema de Vendas. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>