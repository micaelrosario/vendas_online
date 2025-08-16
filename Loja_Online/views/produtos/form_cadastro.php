<?php
    $query_categorias = "SELECT id_categoria, nome FROM categorias;";
    // Prepara a consulta
    $stmt = $pdo->prepare($query_categorias);
    // Executa a consulta
    $stmt->execute();
    // Busca resultados em um array
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<form action="ProdutoController.php" method="post">
    <div class="mb-3">
        <label for="sku">SKU:</label>
        <input type="text" name="sku" id="sku" class="form-control">
    </div>
    <div class="mb-3">
        <label for="nome">nome:</label>
        <input type="text" name="nome" id="nome" class="form-control">
    </div>
    <div class="mb-3">
        <label for="descricao">Descrição:</label>
        <textarea name="descricao" id="descricao"></textarea>
    </div>
    <div class="mb-3">
        <label for="preco_custo">Preço do Produto:</label>
        <input type="number" name="preco_custo" id="preco_custo" class="form-control">
    </div>
    <div class="mb-3">
        <label for="preco_venda">Valor Total:</label>
        <input type="number" name="preco_venda" id="preco_venda" class="form-control">
    </div>
    <div class="mb-3">
        <label for="qtd_estoque">Quantidade de Estoque:</label>
        <input type="number" name="qtd_estoque" id="qtd_estoque" class="form-control">
    </div>

    <div class="mb-3">
        <select name="id_categoria" id="categorias" class="form-control">
            <option value="">Selecione a Categoria</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?php echo htmlspecialchars($categoria['id_categoria']); ?>">
                    <?php echo htmlspecialchars($categoria['nome']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Salvar Produto</button>
    </div>
</form>