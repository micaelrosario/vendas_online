<?php
// Define o tipo de resposta como JSON para ser interpretado pelo JavaScript
header('Content-Type: application/json');

require_once './src/db_connection_pdo.php';

$response = ['status' => 'erro', 'mensagem' => 'ID do cliente não fornecido.'];

// A requisição AJAX envia os dados via POST
if (isset($_POST['id_cliente'])) {
    // Filtra e valida o ID recebido
    $id_para_deletar = filter_input(INPUT_POST, 'id_cliente', FILTER_VALIDATE_INT);

    if ($id_para_deletar) {
        try {
            $sql = "DELETE FROM clientes WHERE id_cliente = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_para_deletar]);

            if ($stmt->rowCount() > 0) {
                // Se a exclusão funcionou
                $response = ['status' => 'sucesso', 'mensagem' => 'Cliente deletado com sucesso!'];
            } else {
                // Se o ID não foi encontrado no banco
                $response = ['status' => 'erro', 'mensagem' => 'Nenhum cliente encontrado com o ID fornecido.'];
            }
        } catch (PDOException $e) {
            // Captura um erro comum: tentar deletar um cliente que já está em um pedido
            if ($e->getCode() == '23000') {
                $response = ['status' => 'erro', 'mensagem' => 'Este cliente não pode ser excluído pois está associado a um ou mais pedidos.'];
            } else {
                // Outros erros de banco de dados
                $response = ['status' => 'erro', 'mensagem' => 'Erro de banco de dados: ' . $e->getMessage()];
            }
        }
    } else {
        $response = ['status' => 'erro', 'mensagem' => 'ID do cliente inválido.'];
    }
}

// Envia a resposta de volta para o JavaScript no formato JSON
echo json_encode($response);