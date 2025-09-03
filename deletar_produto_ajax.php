<?php
// Define o tipo de resposta como JSON
header('Content-Type: application/json');

require_once './src/db_connection_pdo.php';

$response = ['status' => 'erro', 'mensagem' => 'ID do produto não fornecido.'];

if (isset($_POST['id_produto'])) {
    $id_para_deletar = filter_input(INPUT_POST, 'id_produto', FILTER_VALIDATE_INT);

    if ($id_para_deletar) {
        try {
            $sql = "DELETE FROM produtos WHERE id_produto = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_para_deletar]);

            if ($stmt->rowCount() > 0) {
                $response = ['status' => 'sucesso', 'mensagem' => 'Produto deletado com sucesso!'];
            } else {
                $response = ['status' => 'erro', 'mensagem' => 'Nenhum produto encontrado com o ID fornecido.'];
            }
        } catch (PDOException $e) {
            // Erro de integridade (produto está em um pedido)
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

// Envia a resposta em formato JSON
echo json_encode($response);

// Não é necessário desconectar aqui, o script termina e a conexão fecha.
?>