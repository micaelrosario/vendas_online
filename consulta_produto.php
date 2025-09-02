<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Produtos</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
</head>

<body>
    <?php
    // import do PHP
    require_once './src/bootstrap_components.php';
    require_once './src/nav_bar.php';
    require_once './src/db_connection_pdo.php';
    ?>

    <div class="container">
        <h2 class="text-center my-2">Buscar produto por:</h2>
        
        <form action="consulta_produto.php" method="POST">
            <div class="mb-3">
                <label for="termo_busca" class="form-label">Nome ou SKU do Produto:</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="termo_busca" 
                    name="termo_busca" 
                    placeholder="Digite o termo e clique em Buscar" 
                    value="<?php echo isset($_POST['termo_busca']) ? htmlspecialchars($_POST['termo_busca']) : '' ?>"
                >
            </div>
            
            <div class="mb-3 text-center">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>

        <h2 class="text-center">Produtos Cadastrados</h2>

        <!-- Este container será atualizado dinamicamente pelo JavaScript -->
        <div id="tabela-container">
            <?php
            // Esta lógica agora serve tanto para a carga inicial quanto para as buscas AJAX.
            $termo_busca = "";
            $like_termo_busca = "%";
            $where_clause = "";

            // Se um termo de busca for enviado (via POST do nosso JavaScript)
            if (isset($_POST['termo_busca']) && !empty($_POST['termo_busca'])) {
                $termo_busca = $_POST["termo_busca"];
                $like_termo_busca = "%" . $termo_busca . "%";
                
                // CORREÇÃO: Constrói a cláusula WHERE de forma segura e dinâmica.
                $conditions = ["p.nome LIKE :like_termo", "p.sku LIKE :like_termo"];
                // Adiciona a busca por ID apenas se o termo for numérico.
                if (is_numeric($termo_busca)) {
                    $conditions[] = "p.id_produto = :termo_id";
                }
                $where_clause = "WHERE " . implode(" OR ", $conditions);
            }

            try {
                // A consulta SQL é montada dinamicamente
                $consulta_sql = <<<HEREDOC
                    SELECT 
                        p.id_produto,
                        p.sku,
                        p.nome,
                        p.descricao,
                        p.preco_custo,
                        p.quantidade_estoque,
                        c.nome AS nome_categoria
                    FROM 
                        produtos AS p
                    LEFT JOIN 
                        categorias AS c ON p.id_categoria = c.id_categoria
                    {$where_clause}
                HEREDOC;

                $resultados = $conn->prepare($consulta_sql);

                // CORREÇÃO: Binda os parâmetros de forma condicional e com o tipo correto.
                if (!empty($where_clause)) {
                    $resultados->bindValue(':like_termo', $like_termo_busca);
                    if (is_numeric($termo_busca)) {
                        // Usa um placeholder diferente (:termo_id) e define o tipo como inteiro.
                        $resultados->bindValue(':termo_id', $termo_busca, PDO::PARAM_INT);
                    }
                }
                
                $resultados->execute();
                $tabela_dados = $resultados->fetchAll(PDO::FETCH_ASSOC);

                // Mostra a tabela de produtos
                createTable(
                    array(
                        "sku" => "SKU",
                        "nome" => "Nome",
                        "nome_categoria" => "Categoria",
                        "preco_custo" => "Preço de Custo",
                        "quantidade_estoque" => "Estoque"
                    ),
                    $tabela_dados,
                    array(
                        // Links de ação para atualizar e deletar produtos
                        array("id_produto" => '<a href="atualizar_produto.php?id=:id_produto"><img src="img/edit_icon.png" width="32pt" height="32pt" alt="Editar"></a>'),
                        array("id_produto" => '<a href="deletar_produto.php?id=:id_produto"><img src="img/delete_icon.png" width="32pt" height="32pt" alt="Deletar"></a>'),
                    )
                );
            } catch (PDOException $e) {
                // Trata o erro
                echo "<div class='alert alert-danger'><b>Error:</b> " . $e->getMessage() . "</div>";
            }

            // A desconexão só deve acontecer em uma carga de página completa
            if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                require './src/db_disconnect_pdo.php';
            }
            ?>
        </div>
    </div>

    <!-- Carrega javascript do Bootstrap -->
    <script src="js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript para busca dinâmica (AJAX) -->
    <script>
        // Função para atrasar a execução da busca (evita sobrecarregar o servidor)
        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                const context = this;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), delay);
            };
        }

        const inputBusca = document.getElementById('termo_busca');
        const tabelaContainer = document.getElementById('tabela-container');

        // Função que realiza a busca
        const buscarProdutos = async (termo) => {
            // Prepara os dados para enviar
            const formData = new FormData();
            formData.append('termo_busca', termo);

            try {
                // Faz a requisição para a própria página
                const response = await fetch('consultar_produtos.php', {
                    method: 'POST',
                    body: formData,
                    headers: { // Cabeçalho para identificar a requisição como AJAX
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const htmlResponse = await response.text();

                // Usa DOMParser para extrair apenas o conteúdo da tabela da resposta
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlResponse, 'text/html');
                const novaTabela = doc.getElementById('tabela-container');
                
                // Atualiza o container da tabela com o novo conteúdo
                if (novaTabela) {
                    tabelaContainer.innerHTML = novaTabela.innerHTML;
                }

            } catch (error) {
                console.error('Erro ao buscar produtos:', error);
                tabelaContainer.innerHTML = "<div class='alert alert-danger'>Ocorreu um erro ao realizar a busca.</div>";
            }
        };

        // Adiciona o evento 'input' ao campo de busca, com o debounce de 300ms
        inputBusca.addEventListener('input', debounce((e) => {
            buscarProdutos(e.target.value);
        }, 300));
    </script>
</body>

</html>