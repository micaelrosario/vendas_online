<nav class="navbar navbar-expand-lg bg-dark border-bottom border-body mb-4" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">STREAMING VIDEO</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
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
                        <li><a class="dropdown-item" href="consulta_video.php">Vídeos</a></li>
                        <li><a class="dropdown-item" href="consulta_video_join.php">Vídeos com JOIN</a></li>
                        <li><a class="dropdown-item" href="consulta_genero.php">Gêneros</a></li>
                        <li><a class="dropdown-item" href="consulta_avaliacao.php">Avaliação</a></li>
                        <li><a class="dropdown-item" href="consulta_visualizacao.php">Visualização</a></li>
                        <li><a class="dropdown-item" href="consulta_visualizacao_join.php">Visualização com JOIN</a></li>
                        <li><a class="dropdown-item" href="consulta_usuario.php">Usuários</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Cadastrar
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="cadastrar_usuario.php">Usuários</a></li>
                        <li><a class="dropdown-item" href="cadastrar_video.php">Videos</a></li>
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