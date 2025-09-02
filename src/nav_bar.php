<nav class="navbar navbar-expand-lg bg-success border-bottom border-body mb-4 py-2
" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand px-2" href="index.php">Loja Online</a>
        <button class="navbar-toggler " type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <!-- TUDO QUE ESTIVER DENTRO DESSE TAG <ul> aparecera no NAVBAR -->

                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="index.php">Home</a>
                </li>

                <li class="nav-item dropdown">
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

                <!-- 
                <li class="nav-item">
                    <a class="nav-link" href="">Botao Simples Aqui</a>
                </li> 
                -->

                <!-- FIM DOS BOTões  DO NAVBAR -->
            </ul>
        </div>
    </div>
</nav>