<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Categorias</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
</head>

<body>
    <?php
    // =================================================================
    // ARQUIVOS DE CONFIGURAÇÃO E FUNÇÃO DE BUSCA
    // =================================================================
    require_once './src/bootstrap_components.php';
    require_once './src/nav_bar.php';
    require_once './src/db_connection_pdo.php';

    /**
     * Função que busca as categorias no banco de dados e renderiza a tabela HTML.
     * @param PDO $conn A conexão com o banco de dados.
     */
    function renderizar_tabela_categorias($conn)
    {
        $termo_busca = "";
        $where_clause = "";

        // Prepara a cláusula WHERE se um termo de busca for enviado
        if (isset($_POST['termo_busca']) && !empty($_POST['termo_busca'])) {
            $termo_busca = "%" . $_POST["termo_busca"] . "%";
            $where_clause = "WHERE nome LIKE :termo_busca OR descricao LIKE :termo_busca";
        }

        try {
            $consulta_sql = "SELECT id_categoria, nome, descricao FROM categorias {$where_clause}";
            $resultados = $conn->prepare($consulta_sql);

            if (!empty($where_clause)) {
                $resultados->bindValue(':termo_busca', $termo_busca);
            }

            $resultados->execute();
            $tabela_dados = $resultados->fetchAll(PDO::FETCH_ASSOC);

            // Usa a função createTable para exibir os dados
            createTable(
                ["nome" => "Nome", "descricao" => "Descrição"],
                $tabela_dados,
                [
                    ["id_categoria" => '<a href="atualizar_categoria.php?id=:id_categoria"><img src="img/edit_icon.png" width="32pt" height="32pt" alt="Editar"></a>'],
                    ["id_categoria" => '<a href="deletar_categoria.php?id=:id_categoria"><img src="img/delete_icon.png" width="32pt" height="32pt" alt="Deletar"></a>'],
                ]
            );
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'><b>Error:</b> " . $e->getMessage() . "</div>";
        }
    }

    // =================================================================
    // VERIFICA SE É UMA REQUISIÇÃO AJAX (PARA BUSCA DINÂMICA)
    // =================================================================
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        renderizar_tabela_categorias($conn);
        exit(); // Encerra o script para não enviar o HTML completo
    }
    ?>

    <!-- ================================================================= -->
    <!-- ESTRUTURA HTML DA PÁGINA COMPLETA -->
    <!-- ================================================================= -->
    <div class="container">
        <h2 class="text-center my-4">Buscar Categoria:</h2>
        
        <div class="mb-3">
            <label for="termo_busca" class="form-label">Nome ou Descrição da Categoria:</label>
            <input type="text" class="form-control" id="termo_busca" name="termo_busca" placeholder="Digite para buscar...">
        </div>

        <h2 class="text-center">Categorias Cadastradas</h2>

        <!-- Container que será atualizado dinamicamente pelo JavaScript -->
        <div id="tabela-container">
            <?php
            // Na carga inicial da página, renderiza a tabela com todas as categorias
            renderizar_tabela_categorias($conn);
            ?>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript para a busca dinâmica (AJAX) -->
    <script>
        // Função 'debounce' para evitar múltiplas requisições enquanto o usuário digita
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

        // Função assíncrona que busca os dados no servidor
        const buscarCategorias = async (termo) => {
            const formData = new FormData();
            formData.append('termo_busca', termo);

            try {
                const response = await fetch('consultar_categorias.php', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' } // Cabeçalho que identifica a requisição como AJAX
                });
                
                // A resposta do servidor (apenas a tabela) é inserida no container
                tabelaContainer.innerHTML = await response.text();

            } catch (error) {
                console.error('Erro ao buscar categorias:', error);
                tabelaContainer.innerHTML = "<div class='alert alert-danger'>Ocorreu um erro ao realizar a busca.</div>";
            }
        };

        // Adiciona o evento 'input' ao campo de busca, com o debounce
        inputBusca.addEventListener('input', debounce((e) => {
            buscarCategorias(e.target.value);
        }, 300)); // Aguarda 300ms após o usuário parar de digitar
    </script>
</body>

</html>
