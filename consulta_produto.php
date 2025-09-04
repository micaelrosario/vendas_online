<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Produtos</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php
    // import do PHP
    require_once './src/nav_bar.php';
    require_once './src/db_connection_pdo.php';
    ?>

    <div class="container">
        <h2 class="text-center py-3">Buscar produto por:</h2>

        <form action="consulta_produto.php" method="POST" id="form-busca">
            <div class="mb-3">
                <label for="termo_busca" class="form-label">Nome ou SKU do Produto:</label>
                <input type="text" class="form-control" id="termo_busca" name="termo_busca" placeholder="Digite para buscar..." value="<?php echo isset($_POST['termo_busca']) ? htmlspecialchars($_POST['termo_busca']) : '' ?>">
            </div>
        </form>

        <h2 class="text-center py-3">Produtos Cadastrados</h2>

        <div id="tabela-container">
            <?php
            // Lógica PHP para buscar e exibir a tabela
            $termo_busca = isset($_POST['termo_busca']) ? $_POST["termo_busca"] : '';
            $like_termo_busca = "%" . $termo_busca . "%";
            $where_clause = "";

            if (!empty($termo_busca)) {
                $conditions = ["p.nome LIKE :like_termo", "p.sku LIKE :like_termo"];
                if (is_numeric($termo_busca)) {
                    $conditions[] = "p.id_produto = :termo_id";
                }
                $where_clause = "WHERE " . implode(" OR ", $conditions);
            }

            try {
                $consulta_sql = <<<HEREDOC
                    SELECT 
                        p.id_produto, p.sku, p.nome, p.descricao, 
                        p.preco_venda, p.quantidade_estoque, c.nome AS nome_categoria
                    FROM produtos AS p
                    LEFT JOIN categorias AS c ON p.id_categoria = c.id_categoria
                    {$where_clause}
                    ORDER BY p.nome ASC
                HEREDOC;

                $stmt = $conn->prepare($consulta_sql);

                if (!empty($where_clause)) {
                    $stmt->bindValue(':like_termo', $like_termo_busca);
                    if (is_numeric($termo_busca)) {
                        $stmt->bindValue(':termo_id', $termo_busca, PDO::PARAM_INT);
                    }
                }
                $stmt->execute();

                // **ALTERAÇÃO:** Geração manual da tabela para ter controle total sobre os botões
                if ($stmt->rowCount() > 0) {
                    echo '<div class="table-responsive"><table class="table table-striped table-hover">';
                    echo '<thead class="table-dark"><tr><th>SKU</th><th>Nome</th><th>Categoria</th><th>Preço Custo</th><th>Estoque</th><th>Ações</th></tr></thead><tbody>';

                    while ($produto = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        // Adicionamos um ID na linha (<tr>) para fácil remoção via JavaScript
                        echo '<tr id="produto-' . htmlspecialchars($produto['id_produto']) . '">';
                        echo '<td>' . htmlspecialchars($produto['sku']) . '</td>';
                        echo '<td>' . htmlspecialchars($produto['nome']) . '</td>';
                        echo '<td>' . htmlspecialchars($produto['nome_categoria']) . '</td>';
                        echo '<td>' . htmlspecialchars($produto['preco_venda']) . '</td>';
                        echo '<td>' . htmlspecialchars($produto['quantidade_estoque']) . '</td>';
                        echo '<td>';
                        echo '<a href="atualizar_produto.php?id=' . htmlspecialchars($produto['id_produto']) . '" class="btn btn-sm me-2" title="Editar"><img src="img/edit_icon.png" width="20" alt="Editar"></a>';
                        
                        // **BOTÃO DE DELETAR MODIFICADO**
                        // Trocamos o link por um botão com atributos data-*
                        echo '<button type="button" class="btn btn-write btn-sm btn-deletar" 
                                    data-id="' . htmlspecialchars($produto['id_produto']) . '" 
                                    data-nome="' . htmlspecialchars($produto['nome']) . '" title="Deletar">
                                <img src="img/delete_icon.png" width="20" alt="Deletar">
                              </button>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table></div>';
                } else {
                    echo '<div class="alert alert-info text-center">Nenhum produto encontrado.</div>';
                }
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'><b>Error:</b> " . $e->getMessage() . "</div>";
            }
            ?>
        </div>
    </div>

    <div class="modal fade" id="modalConfirmacao" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja deletar o produto: <strong id="nomeProdutoModal"></strong>?
                    <p class="text-danger small mt-2">Esta ação não poderá ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarDelete">Sim, Deletar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="alert-placeholder" style="position: fixed; top: 80px; right: 20px; z-index: 1055; width: 300px;"></div>

    <script src="js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- LÓGICA DE BUSCA (JÁ EXISTENTE E AJUSTADA) ---
            const inputBusca = document.getElementById('termo_busca');
            const formBusca = document.getElementById('form-busca');
            const tabelaContainer = document.getElementById('tabela-container');

            // Previne o envio padrão do formulário de busca
            formBusca.addEventListener('submit', function(e) {
                e.preventDefault();
            });

            function debounce(func, delay) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), delay);
                };
            }

            const buscarProdutos = async (termo) => {
                const formData = new FormData();
                formData.append('termo_busca', termo);

                try {
                    const response = await fetch('consulta_produto.php', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const htmlResponse = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(htmlResponse, 'text/html');
                    const novaTabela = doc.getElementById('tabela-container');
                    if (novaTabela) {
                        tabelaContainer.innerHTML = novaTabela.innerHTML;
                    }
                } catch (error) {
                    console.error('Erro ao buscar produtos:', error);
                    tabelaContainer.innerHTML = "<div class='alert alert-danger'>Ocorreu um erro ao realizar a busca.</div>";
                }
            };

            inputBusca.addEventListener('input', debounce((e) => {
                buscarProdutos(e.target.value);
            }, 300));

            
            // --- NOVA LÓGICA DE DELEÇÃO COM AJAX ---
            const modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
            const nomeProdutoModal = document.getElementById('nomeProdutoModal');
            const btnConfirmarDelete = document.getElementById('btnConfirmarDelete');
            
            // Usamos o container da tabela para 'ouvir' os cliques (event delegation)
            // Isso garante que os botões funcionem mesmo depois da tabela ser recarregada pela busca
            tabelaContainer.addEventListener('click', function(event) {
                // Verifica se o elemento clicado é um botão de deletar
                const deleteButton = event.target.closest('.btn-deletar');
                if (!deleteButton) {
                    return;
                }

                const idParaDeletar = deleteButton.dataset.id;
                const nomeProduto = deleteButton.dataset.nome;
                
                nomeProdutoModal.textContent = nomeProduto;
                btnConfirmarDelete.dataset.id = idParaDeletar; // Armazena o ID no botão de confirmação
                
                modalConfirmacao.show();
            });

            btnConfirmarDelete.addEventListener('click', function() {
                const id = this.dataset.id;
                
                fetch('deletar_produto_ajax.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id_produto=' + encodeURIComponent(id)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'sucesso') {
                        // Remove a linha da tabela com um efeito suave
                        const linhaParaRemover = document.getElementById('produto-' + id);
                        if(linhaParaRemover){
                            linhaParaRemover.style.transition = 'opacity 0.5s ease';
                            linhaParaRemover.style.opacity = '0';
                            setTimeout(() => linhaParaRemover.remove(), 500);
                        }
                        showAlert(data.mensagem, 'success');
                    } else {
                        showAlert(data.mensagem, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erro na requisição:', error);
                    showAlert('Erro de comunicação com o servidor.', 'danger');
                })
                .finally(() => {
                    modalConfirmacao.hide();
                });
            });

            function showAlert(message, type) {
                const alertPlaceholder = document.getElementById('alert-placeholder');
                const wrapper = document.createElement('div');
                wrapper.innerHTML = [
                    `<div class="alert alert-${type} alert-dismissible fade show" role="alert">`,
                    `   <div>${message}</div>`,
                    '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
                    '</div>'
                ].join('');

                alertPlaceholder.append(wrapper);
                
                setTimeout(() => {
                    wrapper.querySelector('.alert')?.classList.remove('show');
                    setTimeout(() => wrapper.remove(), 150);
                }, 5000);
            }
        });
    </script>
</body>

</html>