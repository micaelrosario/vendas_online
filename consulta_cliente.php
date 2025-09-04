<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Clientes</title>
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
        <h2 class="text-center my-4">Consultar Clientes</h2>
        
        <form action="consulta_cliente.php" method="POST" id="form-busca" class="mb-4">
            <div class="mb-3">
                <label for="termo_busca" class="form-label">Buscar por Nome ou CPF/CNPJ:</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="termo_busca" 
                    name="termo_busca" 
                    placeholder="Digite para buscar..."
                    value="<?php echo isset($_POST['termo_busca']) ? htmlspecialchars($_POST['termo_busca']) : '' ?>"
                >
            </div>
        </form>

        <h2 class="text-center">Clientes Cadastrados</h2>

        <div id="tabela-container">
            <?php
                // Lógica PHP para buscar e exibir a tabela de clientes
                $termo_busca = isset($_POST['termo_busca']) ? $_POST["termo_busca"] : '';
                $like_termo_busca = "%" . $termo_busca . "%";
                $where_clause = "";

                if (!empty($termo_busca)) {
                    // Busca por nome ou cpf_cnpj
                    $conditions = ["nome LIKE :like_termo", "cpf_cnpj LIKE :like_termo"];
                    if (is_numeric($termo_busca)) {
                        $conditions[] = "id_cliente = :termo_id";
                    }
                    $where_clause = "WHERE " . implode(" OR ", $conditions);
                }

                try {
                    $consulta_sql = <<<HEREDOC
                        SELECT id_cliente, nome, cpf_cnpj, email, telefone
                        FROM clientes
                        {$where_clause}
                        ORDER BY nome ASC
                    HEREDOC;

                    $stmt = $conn->prepare($consulta_sql);

                    if (!empty($where_clause)) {
                        $stmt->bindValue(':like_termo', $like_termo_busca);
                        if (is_numeric($termo_busca)) {
                            $stmt->bindValue(':termo_id', $termo_busca, PDO::PARAM_INT);
                        }
                    }
                    $stmt->execute();
                    
                    // Geração da tabela HTML
                    if ($stmt->rowCount() > 0) {
                        echo '<div class="table-responsive"><table class="table table-striped table-hover">';
                        echo '<thead class="table-dark"><tr><th>Nome</th><th>CPF/CNPJ</th><th>Email</th><th>Telefone</th><th style="width: 120px;">Ações</th></tr></thead><tbody>';

                        while ($cliente = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // Adicionamos um ID na linha (<tr>) para fácil remoção via JavaScript
                            echo '<tr id="cliente-' . htmlspecialchars($cliente['id_cliente']) . '">';
                            echo '<td>' . htmlspecialchars($cliente['nome']) . '</td>';
                            echo '<td>' . htmlspecialchars($cliente['cpf_cnpj']) . '</td>';
                            echo '<td>' . htmlspecialchars($cliente['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($cliente['telefone']) . '</td>';
                            echo '<td>';
                            echo '<a href="atualizar_cliente.php?id=' . htmlspecialchars($cliente['id_cliente']) . '" class="btn btn-sm me-2" title="Editar"><img src="img/edit_icon.png" width="20" alt="Editar"></a>';
                            
                            // BOTÃO DE DELETAR com atributos data-* para o JavaScript
                            echo '<button type="button" class="btn btn-write btn-sm btn-deletar" 
                                        data-id="' . htmlspecialchars($cliente['id_cliente']) . '" 
                                        data-nome="' . htmlspecialchars($cliente['nome']) . '" title="Deletar">
                                    <img src="img/delete_icon.png" width="20" alt="Deletar">
                                  </button>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table></div>';
                    } else {
                        echo '<div class="alert alert-info text-center">Nenhum cliente encontrado.</div>';
                    }

                } catch (PDOException $e) {
                    echo "<div class='alert alert-danger'><b>Error:</b> " . $e->getMessage() . "</div>";
                }

                // Desconexão do banco (apenas em cargas de página completas)
                if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                    require './src/db_disconnect_pdo.php';
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
                    Tem certeza que deseja deletar o cliente: <strong id="nomeItemModal"></strong>?
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
            // --- LÓGICA DE BUSCA ---
            const inputBusca = document.getElementById('termo_busca');
            const formBusca = document.getElementById('form-busca');
            const tabelaContainer = document.getElementById('tabela-container');

            formBusca.addEventListener('submit', e => e.preventDefault());

            function debounce(func, delay) {
                let timeout;
                return (...args) => {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), delay);
                };
            }

            const buscarItens = async (termo) => {
                const formData = new FormData();
                formData.append('termo_busca', termo);

                try {
                    const response = await fetch('consulta_cliente.php', { // URL da própria página
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const htmlResponse = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(htmlResponse, 'text/html');
                    tabelaContainer.innerHTML = doc.getElementById('tabela-container').innerHTML;
                } catch (error) {
                    console.error('Erro ao buscar:', error);
                    tabelaContainer.innerHTML = "<div class='alert alert-danger'>Ocorreu um erro ao realizar a busca.</div>";
                }
            };

            inputBusca.addEventListener('input', debounce(e => buscarItens(e.target.value), 300));

            // --- LÓGICA DE DELEÇÃO COM AJAX ---
            const modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
            const nomeItemModal = document.getElementById('nomeItemModal');
            const btnConfirmarDelete = document.getElementById('btnConfirmarDelete');
            
            tabelaContainer.addEventListener('click', function(event) {
                const deleteButton = event.target.closest('.btn-deletar');
                if (!deleteButton) return;

                const id = deleteButton.dataset.id;
                const nome = deleteButton.dataset.nome;
                
                nomeItemModal.textContent = nome;
                btnConfirmarDelete.dataset.id = id;
                modalConfirmacao.show();
            });

            btnConfirmarDelete.addEventListener('click', function() {
                const id = this.dataset.id;
                
                fetch('deletar_cliente_ajax.php', { // URL para o script de deleção de cliente
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id_cliente=' + encodeURIComponent(id) // Parâmetro correto
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'sucesso') {
                        const linhaParaRemover = document.getElementById('cliente-' + id);
                        if(linhaParaRemover) {
                            linhaParaRemover.style.transition = 'opacity 0.5s ease';
                            linhaParaRemover.style.opacity = '0';
                            setTimeout(() => linhaParaRemover.remove(), 500);
                        }
                        showAlert(data.mensagem, 'success');
                    } else {
                        showAlert(data.mensagem, 'danger');
                    }
                })
                .catch(error => showAlert('Erro de comunicação com o servidor.', 'danger'))
                .finally(() => modalConfirmacao.hide());
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
                setTimeout(() => wrapper.querySelector('.alert')?.remove(), 5000);
            }
        });
    </script>
</body>
</html>