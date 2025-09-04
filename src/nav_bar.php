
<nav class="navbar navbar-expand-lg bg-primary border-bottom border-body mb-5 py-3" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand me-5" href="index.php">Loja Online</a>
        <button class="navbar-toggler " type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ">
                <li class="nav-item me-3">
                    <a class="nav-link" aria-current="page" href="index.php">Home</a>
                </li>
                <li class="nav-item dropdown me-3">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Consultas
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="consulta_produto.php">Produtos</a></li>
                        <li><a class="dropdown-item" href="consulta_categoria.php">Categorias</a></li>
                        <li><a class="dropdown-item" href="consulta_cliente.php">Clientes</a></li>
                        <li><a class="dropdown-item" href="consulta_pedido.php">Pedidos</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Cadastrar
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="cadastrar_produto.php">Produtos</a></li>
                        <li><a class="dropdown-item" href="cadastrar_categoria.php">Categorias</a></li>
                        <li><a class="dropdown-item" href="cadastrar_cliente.php">Clientes</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="carrinho.php">
                        <i class="bi bi-cart3" style="font-size: 1.5rem;"></i>
                        <strong class="mx-2">Carrinho</strong>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>