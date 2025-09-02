<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Categoria</title>
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
        <h2 class="text-center my-2">Buscar Categoria por:</h2>
        
        <form action="consulta_categoria.php" method="POST">
            <div class="mb-3">
                <label for="termo_busca" class="form-label">Nome ou Descrição da Categoria:</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="termo_busca" 
                    name="termo_busca" 
                    placeholder="Digite para buscar..."
                    value="<?php echo isset($_POST['termo_busca']) ? htmlspecialchars($_POST['termo_busca']) : '' ?>"
                >
            </div>
            
            <div class="mb-3 text-center">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>

        <div class="mb-3">
            <label for="termo_busca" class="form-label">Nome ou Descrição da Categoria:</label>
            <input type="text" class="form-control" id="termo_busca" name="termo_busca" placeholder="Digite para buscar...">
        </div>

        <h2 class="text-center">Categorias Cadastradas</h2>

        <!-- Este container será atualizado dinamicamente pelo JavaScript -->
        <div id="tabela-container">
            <?php
                // Lógica para preparar os termos de busca
                $termo_busca = "";
                $like_termo_busca = "%";
                $where_clause = "";

                // Verifica se um termo de busca foi enviado via POST
                if (isset($_POST['termo_busca']) && !empty($_POST['termo_busca'])) {
                    $termo_busca = $_POST["termo_busca"];
                    $like_termo_busca = "%" . $termo_busca . "%";
                    
                    // Define em quais colunas da tabela "categoria" a busca será feita
                    $conditions = [
                        "cat.nome LIKE :like_termo", 
                        "cat.descricao LIKE :like_termo"
                    ];

                    // Adiciona a busca por ID apenas se o termo for numérico
                    if (is_numeric($termo_busca)) {
                        $conditions[] = "cat.id_categoria = :termo_id";
                    }
                    $where_clause = "WHERE " . implode(" OR ", $conditions);
                }

                try {
                    // A consulta SQL foi adaptada para a tabela "categoria"
                    $consulta_sql = <<<HEREDOC
                        SELECT 
                            cat.id_categoria,
                            cat.nome,
                            cat.descricao
                        FROM 
                            categorias AS cat
                        {$where_clause}
                    HEREDOC;

                    $resultados = $conn->prepare($consulta_sql);

                    // Binda os parâmetros de forma condicional
                    if (!empty($where_clause)) {
                        $resultados->bindValue(':like_termo', $like_termo_busca);
                        if (is_numeric($termo_busca)) {
                            $resultados->bindValue(':termo_id', $termo_busca, PDO::PARAM_INT);
                        }
                    }
                    
                    $resultados->execute();
                    $tabela_dados = $resultados->fetchAll(PDO::FETCH_ASSOC);

                    // A partir daqui, você usaria a variável $tabela_dados para mostrar os resultados na sua tabela HTML.
                    // Exemplo: createTable(["nome" => "Nome da Categoria", "descricao" => "Descrição"], $tabela_dados, [...]);

                } catch (PDOException $e) {
                    // Trata o erro
                    echo "<div class='alert alert-danger'><b>Error:</b> " . $e->getMessage() . "</div>";
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
        const buscarCategorias = async (termo) => {
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
                console.error('Erro ao buscar categorias:', error);
                tabelaContainer.innerHTML = "<div class='alert alert-danger'>Ocorreu um erro ao realizar a busca.</div>";
            }
        };

        // Adiciona o evento 'input' ao campo de busca, com o debounce de 300ms
        inputBusca.addEventListener('input', debounce((e) => {
            buscarCategorias(e.target.value);
        }, 300));
    </script>
</body>

</html>