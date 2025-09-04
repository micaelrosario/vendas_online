<?php
session_start();
// Imports do PHP
require_once './src/nav_bar.php';
require_once './src/db_connection_pdo.php';

$itens_carrinho_data = [];
$produtos_carrinho = [];
$mensagem_erro = '';

if (!empty($_SESSION['carrinho'])) {
    $ids_carrinho = array_keys($_SESSION['carrinho']);
    try {
        $placeholders = implode(',', array_fill(0, count($ids_carrinho), '?'));
        $sql = "SELECT p.id_produto, p.nome, p.preco_venda, p.imagem, c.nome AS nome_categoria FROM produtos AS p INNER JOIN categorias AS c ON p.id_categoria = c.id_categoria WHERE p.id_produto IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->execute($ids_carrinho);
        $produtos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Combina os dados do banco com a quantidade da sessão
        foreach ($produtos_db as $produto_db) {
            $id = $produto_db['id_produto'];
            $produto_db['quantidade'] = $_SESSION['carrinho'][$id];
            $itens_carrinho_data[] = $produto_db;
        }
    } catch (PDOException $e) {
        $mensagem_erro = "Erro ao carregar itens do carrinho: " . $e->getMessage();
        $itens_carrinho_data = [];
    }
}

require './src/db_disconnect_pdo.php';
?>
<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .carrinho-item {
            display: flex;
            align-items: center;
            border: 1px solid #dee2e6;
            border-radius: .25rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background-color: #fff;
        }

        .carrinho-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: .25rem;
        }

        .carrinho-item .item-details {
            flex-grow: 1;
            margin-left: 1rem;
        }

        .quantidade-control {
            display: flex;
            align-items: center;
        }

        .quantidade-control .form-control {
            width: 60px;
            text-align: center;
            margin: 0 5px;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Meu Carrinho</h2>
        <form id="form-carrinho" action="processar_pedido.php" method="POST">
            <?php if (!empty($itens_carrinho_data)): ?>
                <?php foreach ($itens_carrinho_data as $item): ?>
                    <div class="carrinho-item shadow-sm" data-preco="<?php echo htmlspecialchars($item['preco_venda']); ?>">
                        <input type="checkbox" name="produtos_selecionados[]" value="<?php echo htmlspecialchars($item['id_produto']); ?>" class="form-check-input me-3">

                        <?php
                        $caminho_imagem = !empty($item['imagem']) ? 'img/' . $item['imagem'] : 'img/default_product.jpg';
                        ?>
                        <img src="<?php echo htmlspecialchars($caminho_imagem); ?>" alt="Imagem do produto <?php echo htmlspecialchars($item['nome']); ?>">

                        <div class="item-details">
                            <h5><?php echo htmlspecialchars($item['nome']); ?></h5>
                            <p class="text-muted m-0"><?php echo htmlspecialchars($item['nome_categoria']); ?></p>
                            <p class="h5 text-primary mt-2">R$ <span class="preco-unitario"><?php echo number_format($item['preco_venda'], 2, ',', '.'); ?></span></p>
                        </div>

                        <div class="quantidade-control ms-auto">
                            <button type="button" class="btn btn-outline-secondary btn-sm diminuir-quantidade" data-id="<?php echo htmlspecialchars($item['id_produto']); ?>">-</button>
                            <input type="number" name="quantidade[<?php echo htmlspecialchars($item['id_produto']); ?>]" value="<?php echo htmlspecialchars($item['quantidade']); ?>" class="form-control quantidade-input" data-id="<?php echo htmlspecialchars($item['id_produto']); ?>" min="1">
                            <button type="button" class="btn btn-outline-secondary btn-sm aumentar-quantidade" data-id="<?php echo htmlspecialchars($item['id_produto']); ?>">+</button>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="text-end mt-4">
                    <div id="erro-selecao" class="alert alert-danger d-none" role="alert">
                        Selecione pelo menos um item para continuar.
                    </div>
                    <h4 class="mb-3">Valor Total: <span id="valor-total">R$ 0,00</span></h4>
                    <button type="submit" class="btn btn-success btn-lg">Finalizar Pedido</button>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    Seu carrinho está vazio. <a href="index.php" class="alert-link">Explore nossos produtos!</a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="produtos_selecionados[]"]');
            const valorTotalSpan = document.getElementById('valor-total');
            const diminuirBotoes = document.querySelectorAll('.diminuir-quantidade');
            const aumentarBotoes = document.querySelectorAll('.aumentar-quantidade');
            const quantidadeInputs = document.querySelectorAll('.quantidade-input');
            const formCarrinho = document.getElementById('form-carrinho');
            const erroSelecao = document.getElementById('erro-selecao');

            function atualizarValorTotal() {
                let total = 0;
                quantidadeInputs.forEach(function(input) {
                    const id = input.dataset.id;
                    const checkbox = document.querySelector(`input[type="checkbox"][value="${id}"]`);

                    if (checkbox && checkbox.checked) {
                        const preco = parseFloat(checkbox.closest('.carrinho-item').dataset.preco);
                        const quantidade = parseInt(input.value);
                        total += preco * quantidade;
                    }
                });
                valorTotalSpan.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
            }

            // Lógica para os botões de +/-
            diminuirBotoes.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const input = document.querySelector(`.quantidade-input[data-id="${id}"]`);
                    let quantidade = parseInt(input.value);
                    if (quantidade > 1) {
                        input.value = quantidade - 1;
                        atualizarValorTotal();
                    }
                });
            });

            aumentarBotoes.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const input = document.querySelector(`.quantidade-input[data-id="${id}"]`);
                    let quantidade = parseInt(input.value);
                    input.value = quantidade + 1;
                    atualizarValorTotal();
                });
            });

            quantidadeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (parseInt(this.value) < 1) {
                        this.value = 1;
                    }
                    atualizarValorTotal();
                });
            });


            formCarrinho.addEventListener('submit', function(event) {
                const checkboxesSelecionados = document.querySelectorAll('input[name="produtos_selecionados[]"]:checked');

                if (checkboxesSelecionados.length === 0) {
                    // Impede o envio do formulário
                    event.preventDefault();
                    // Exibe a mensagem de erro
                    erroSelecao.classList.remove('d-none');
                } else {
                    // Esconde a mensagem de erro se tudo estiver OK
                    erroSelecao.classList.add('d-none');
                }
            });

            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', atualizarValorTotal);
            });

            atualizarValorTotal();
        });
    </script>
</body>

</html>