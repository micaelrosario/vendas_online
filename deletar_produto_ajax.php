<?php
// Define o tipo de resposta como JSON para ser usado com JavaScript
header('Content-Type: application/json');

require_once './src/db_connection_pdo.php';

// Inicializa a resposta padrão para o caso de o ID não ser fornecido
$response = ['status' => 'erro', 'mensagem' => 'ID do produto não fornecido.'];

// Verifica se o ID do produto foi enviado via POST
if (isset($_POST['id_produto'])) {
    // Filtra e valida o ID para garantir que é um número inteiro
    $id_para_deletar = filter_input(INPUT_POST, 'id_produto', FILTER_VALIDATE_INT);

    if ($id_para_deletar) {
        try {
            // Inicia uma transação para garantir que as operações de banco de dados
            // sejam executadas atomicamente (ou todas, ou nenhuma)
            $conn->beginTransaction();

            // Busca o nome da imagem associada ao produto ANTES de deletá-lo.
            $sql_img = "SELECT imagem FROM produtos WHERE id_produto = ?";
            $stmt_img = $conn->prepare($sql_img);
            $stmt_img->execute([$id_para_deletar]);
            $imagem_produto = $stmt_img->fetchColumn();

            // Tenta deletar o registro do produto no banco de dados
            $sql_delete = "DELETE FROM produtos WHERE id_produto = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->execute([$id_para_deletar]);

            // Verifica se alguma linha foi afetada (se o produto realmente existia)
            if ($stmt_delete->rowCount() > 0) {
                // Se a exclusão do registro foi bem-sucedida, remove a imagem do servidor.
                // A condição evita que a imagem padrão ('default_product.png') seja deletada
                // e verifica se o arquivo realmente existe no diretório.
                if ($imagem_produto && $imagem_produto != 'default_product.png' && file_exists('img/' . $imagem_produto)) {
                    unlink('img/' . $imagem_produto);
                }
                
                $conn->commit(); // Confirma a transação se tudo deu certo
                $response = ['status' => 'sucesso', 'mensagem' => 'Produto deletado com sucesso!'];
            } else {
                $conn->rollBack(); // Reverte a transação
                $response = ['status' => 'erro', 'mensagem' => 'Nenhum produto encontrado com o ID fornecido.'];
            }
        } catch (PDOException $e) {
            $conn->rollBack(); // Reverte a transação em caso de qualquer erro
            
            // Trata o erro de integridade referencial (código '23000'), que ocorre quando
            // um produto está em um pedido e não pode ser excluído.
            if ($e->getCode() == '23000') {
                $response = ['status' => 'erro', 'mensagem' => 'Este produto não pode ser excluído pois está associado a um pedido.'];
            } else {
                $response = ['status' => 'erro', 'mensagem' => 'Erro de banco de dados: ' . $e->getMessage()];
            }
        }
    } else {
        $response = ['status' => 'erro', 'mensagem' => 'ID do produto inválido.'];
    }
}

// Envia a resposta em formato JSON para o cliente
echo json_encode($response);

// O script termina e a conexão com o banco de dados é fechada automaticamente.
?>