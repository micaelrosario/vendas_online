<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Vendas Online</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    
</head>

<body>
    <?php
    // carrega arquivos PHP (semelhante ao 'import' do Python)
    require_once './src/bootstrap_components.php';
    require_once './src/nav_bar.php';
    ?>

    <!-- criar container bootstrap -->
    <div class="container">
        <!-- Carrega o conteudo do arquivo Markdown README.md dentro do HTML -->
        <zero-md src="README.md"></zero-md>
        <!-- coloca uma margem inferior no final do arquivo -->
        <div class="mb-5"></div>
    </div>

    <!-- CARREGA javascript do BOOTSTRAP -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <!-- CARREGA javascript do ZEROMD (para ler arquivos Markdown em paginas HTML) -->
    <script type="module" src="js/zero-md.min.js"></script>
</body>

</html>