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
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <h2 class="text-center my-4">Cadastrar Produto:</h2>
                
                <form class="mb-4" action="cadastrar_produto.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome:</label>
                        <input type="text" name="nome" id="nome" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição:</label>
                        <textarea name="descricao" id="descricao" class="form-control" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="preco_venda" class="form-label">Preço de Venda:</label>
                        <input type="number" name="preco_venda" id="preco_venda" class="form-control" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="preco_venda" class="form-label">Preço de Custo:</label>
                        <input type="number" name="preco_venda" id="preco_venda" class="form-control" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="quantidade_estoque" class="form-label">Quantidade de Estoque:</label>
                        <input type="number" name="quantidade_estoque" id="quantidade_estoque" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="id_categoria" class="form-label">Selecione a Categoria:</label>
                        <select name="id_categoria" id="id_categoria" class="form-control" required>
                            <option value="">Nenhum</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo htmlspecialchars($categoria['id_categoria']); ?>">
                                    <?php echo htmlspecialchars($categoria['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="imagem" class="form-label">Imagem do Produto:</label>
                        <input type="file" name="imagem" id="imagem" class="form-control">
                    </div>

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
                    $preco_venda = isset($_POST['preco_venda']) ? $_POST['preco_venda'] : '';
                    $preco_venda = isset($_POST['preco_venda']) ? $_POST['preco_venda'] : '';
                    $quantidade_estoque = isset($_POST['quantidade_estoque']) ? $_POST['quantidade_estoque'] : '';
                    $id_categoria = isset($_POST['id_categoria']) ? $_POST['id_categoria'] : '';
                    $nome_imagem = null; // Variável para o nome da imagem

                    // Validações
                    $erros = [];
                    if (empty($nome) || empty($descricao) || empty($preco_venda) || empty($preco_venda) || empty($quantidade_estoque) || empty($id_categoria)) {
                        $erros[] = "Por favor, preencha todos os campos obrigatórios.";
                    }
                    if (is_numeric($preco_venda) && is_numeric($preco_venda) && $preco_venda <= $preco_venda) {
                        $erros[] = "O preço de venda deve ser maior que o preço de custo.";
                    }
                    if (is_numeric($quantidade_estoque) && $quantidade_estoque < 0) {
                        $erros[] = "A quantidade em estoque não pode ser negativa.";
                    }

                    // Processamento do upload da imagem
                    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
                        $diretorio_destino = 'img/';
                        // Gera um nome único para o arquivo para evitar colisões
                        $nome_original = basename($_FILES['imagem']['name']);
                        $extensao = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));
                        $nome_unico = uniqid('prod_', true) . '.' . $extensao;
                        $caminho_destino = $diretorio_destino . $nome_unico;

                        // Verifica a extensão do arquivo
                        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
                        if (!in_array($extensao, $extensoes_permitidas)) {
                            $erros[] = "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
                        } elseif ($_FILES['imagem']['size'] > 5000000) { // 5MB
                            $erros[] = "O arquivo é muito grande. Tamanho máximo é 5MB.";
                        } elseif (!move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_destino)) {
                            $erros[] = "Houve um erro ao mover o arquivo para o diretório de destino.";
                        } else {
                            $nome_imagem = $nome_unico;
                        }
                    }

                    if (empty($erros)) {
                        // Gera o SKU automaticamente
                        $prefixo = isset($prefixos_categoria[$id_categoria]) ? $prefixos_categoria[$id_categoria] : 'PRD';
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM produtos WHERE id_categoria = ?");
                        $stmt->execute([$id_categoria]);
                        $total = $stmt->fetchColumn();
                        $numero = str_pad($total + 1, 3, '0', STR_PAD_LEFT);
                        $sku = $prefixo . $numero;

                        try {
                            $consulta_sql = <<<HEREDOC
                            INSERT INTO produtos (sku, nome, descricao, preco_venda, preco_venda, quantidade_estoque, imagem, data_registro, id_categoria)
                            VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )
                            HEREDOC;
                            
                            $resultados = $conn->prepare($consulta_sql);
                            
                            $resultados->execute(array(
                                $sku,
                                $nome,
                                $descricao,
                                $preco_venda,
                                $preco_venda,
                                $quantidade_estoque,
                                $nome_imagem, // Salva o nome do arquivo no banco de dados
                                date('Y-m-d'),
                                $id_categoria
                            ));

                            echo <<<HEREDOC
                            <div class="alert alert-success text-center mt-3" role="alert">
                                Produto cadastrado com sucesso! SKU gerado: <b>{$sku}</b>
                            </div>
                            HEREDOC;
                        } catch (PDOException $e) {
                            echo "<div class='alert alert-danger mt-3' role='alert'><b>Erro:</b> " . $e->getMessage() . "</div>";
                        }
                    } else {
                        // Exibe os erros
                        echo '<div class="alert alert-danger text-center mt-3" role="alert">';
                        foreach ($erros as $erro) {
                            echo $erro . '<br>';
                        }
                        echo '</div>';
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