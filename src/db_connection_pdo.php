<?php

// definir a codificacao como UTF-8 (no PHP)
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// faz conexao com DB
$servername = "localhost";
$username = "root";
$password = "";
$db = "sistema_vendas_online";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$db;charset=utf8", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
