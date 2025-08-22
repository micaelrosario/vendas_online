<?php

// definir a codificacao como UTF-8 (no PHP)
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

$db_host = 'localhost';
$db_name = 'sistema_vendas_online'; 
$db_user = 'root'; 
$db_pass = ''; 

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET CHARACTER SET utf8");
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

?>