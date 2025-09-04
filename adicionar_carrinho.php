<?php
session_start();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_produto = (int)$_GET['id'];

    // Inicializa o carrinho na sessão se ele não existir
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    // Se o produto já estiver no carrinho, incrementa a quantidade
    if (array_key_exists($id_produto, $_SESSION['carrinho'])) {
        $_SESSION['carrinho'][$id_produto]++;
    } else {
        // Se não, adiciona o produto com quantidade 1
        $_SESSION['carrinho'][$id_produto] = 1;
    }

    // Redireciona de volta para a página do produto com uma mensagem de sucesso
    header("Location: detalhes_produto.php?id=$id_produto&status=adicionado");
    exit();
} else {
    // Redireciona para a página inicial se nenhum ID for fornecido
    header("Location: index.php");
    exit();
}