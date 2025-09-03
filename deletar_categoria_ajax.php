<?php
// Define o tipo de resposta como JSON para ser interpretado pelo JavaScript
header('Content-Type: application/json');

require_once './src/db_connection_pdo.php';

$response = ['status' => 'erro', 'mensagem' => 'ID da categoria não fornecido.'];

// A requisição AJAX envia os dados via POST
if (isset($_POST['id_categoria'])) {
    // Filtra e valida o ID recebido
    $id_para_deletar = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);

    if ($id_para_deletar) {
        try {
            $sql = "DELETE FROM categorias WHERE id_categoria = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_para_deletar]);

            if ($stmt->rowCount() > 0) {
                // Se a exclusão funcionou
                $response = ['status' => 'sucesso', 'mensagem' => 'Categoria deletada com sucesso!'];
            } else {
                // Se o ID não foi encontrado no banco
                $response = ['status' => 'erro', 'mensagem' => 'Nenhuma categoria encontrada com o ID fornecido.'];
            }
        } catch (PDOException $e) {
            // Captura um erro comum: tentar deletar uma categoria que já está em uso por produtos
            if ($e->getCode() == '23000') {
                $response = ['status' => 'erro', 'mensagem' => 'Esta categoria não pode ser excluída pois está associada a um ou mais produtos.'];
            } else {
                // Outros erros de banco de dados
                $response = ['status' => 'erro', 'mensagem' => 'Erro de banco de dados: ' . $e->getMessage()];
            }
        }
    } else {
        $response = ['status' => 'erro', 'mensagem' => 'ID da categoria inválido.'];
    }
}

// Envia a resposta de volta para o JavaScript no formato JSON
echo json_encode($response);