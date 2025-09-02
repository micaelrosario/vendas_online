<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
</head>

<body>
    <?php
    require_once './src/bootstrap_components.php';
    require_once './src/nav_bar.php';
    require_once './src/db_connection_pdo.php';

    // Array de categorias e prefixos para SKU (exemplo, ajuste conforme seu banco)
    $prefixos_categoria = [
        1 => 'ELT', // Eletrônico
        2 => 'LIV', // Livro
        3 => 'VES', // Vestuário
        // Adicione outros conforme necessário
    ];

    // Buscar categorias do banco
    $categorias = [];
    $stmt = $conn->query("SELECT id_categoria, nome FROM categorias");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="container">
        <!-- Adicionado row e colunas para centralizar e limitar a largura do formulário -->
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <h2 class="text-center my-4">Cadastrar Produto:</h2>
                <!-- Removida a classe 'row' do form e o 'col-md-12' interno -->
                <form class="mb-4" action="cadastrar_produto.php" method="post">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome:</label>
                        <input type="text" name="nome" id="nome" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição:</label>
                        <!-- Alterado de input para textarea para melhor usabilidade -->
                        <textarea name="descricao" id="descricao" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="preco_custo" class="form-label">Preço de Custo:</label>
                        <input type="number" name="preco_custo" id="preco_custo" class="form-control" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="quantidade_estoque" class="form-label">Quantidade de Estoque:</label>
                        <input type="number" name="quantidade_estoque" id="quantidade_estoque" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="categorias" class="form-label">Selecione a Categoria:</label>
                        <select name="id_categoria" id="categorias" class="form-control" required>
                            <option value="">Nenhum</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo htmlspecialchars($categoria['id_categoria']); ?>">
                                    <?php echo htmlspecialchars($categoria['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Botão dentro de um d-grid para ocupar 100% da largura da coluna -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Salvar Produto</button>
                    </div>
                </form>

                <?php
                // Só processa se o formulário foi enviado
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Inicializa as variáveis
                    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
                    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
                    $preco_custo = isset($_POST['preco_custo']) ? $_POST['preco_custo'] : '';
                    $quantidade_estoque = isset($_POST['quantidade_estoque']) ? $_POST['quantidade_estoque'] : '';
                    $id_categoria = isset($_POST['id_categoria']) ? $_POST['id_categoria'] : '';

                    if (
                        !empty($nome) &&
                        !empty($descricao) &&
                        is_numeric($preco_custo) &&
                        is_numeric($quantidade_estoque) &&
                        !empty($id_categoria)
                    ) {
                        // Gera o SKU automaticamente
                        $prefixo = isset($prefixos_categoria[$id_categoria]) ? $prefixos_categoria[$id_categoria] : 'PRD';
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM produtos WHERE id_categoria = ?");
                        $stmt->execute([$id_categoria]);
                        $total = $stmt->fetchColumn();
                        $numero = str_pad($total + 1, 3, '0', STR_PAD_LEFT);
                        $sku = $prefixo . $numero;

                        try {
                            $consulta_sql = <<<HEREDOC
                            INSERT INTO produtos (sku, nome, descricao, preco_custo, quantidade_estoque, data_registro, id_categoria)
                            VALUES ( ?, ?, ?, ?, ?, ?, ? )
                            HEREDOC;
                            
                            $resultados = $conn->prepare($consulta_sql);
                            
                            $resultados->execute(array(
                                $sku,
                                $nome,
                                $descricao,
                                $preco_custo,
                                $quantidade_estoque,
                                date('Y-m-d'),
                                $id_categoria
                            ));

                            echo <<<HEREDOC
                            <div class="alert alert-success text-center mt-3" role="alert">
                                Produto cadastrado com sucesso! SKU gerado: <b>{$sku}</b>
                            </div>
                            HEREDOC;
                        } catch (PDOException $e) {
                            echo "<div class='alert alert-danger mt-3' role='alert'><b>Error:</b> " . $e->getMessage() . "</div>";
                        }
                    } else {
                        echo <<<HEREDOC
                        <div class="alert alert-danger text-center mt-3" role="alert">
                            Por favor, preencha todos os campos corretamente.
                        </div>
                        HEREDOC;
                    }
                }

                require './src/db_disconnect_pdo.php';
                ?>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
