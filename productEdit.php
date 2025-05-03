<?php
session_start();
require 'bd/connection.php';

if (isset($_POST['editar_produto'])) {
    $idProduto = $_POST['id_product'];
    $nomeProduto = $_POST['nome'];
    $categoriaProduto = $_POST['categoria'];
    $valorProduto = $_POST['valor'];
    $estoqueProduto = $_POST['estoque'];
    $descricaoProduto = $_POST['descricao'];


    if (empty($nomeProduto)  || empty($categoriaProduto) || empty($valorProduto) || !isset($estoqueProduto) || empty($descricaoProduto)) {
        $_SESSION['restrincao_editarLoja'] = "Preencha todos os campos";
        header("location: views/productEdit_page.php?&nome=$nomeProduto&categoria=$categoriaProduto&valor=$valorProduto&estoque=$estoqueProduto&descricao=$descricaoProduto");
        exit();
}
if ($estoqueProduto > 1000) {
    $_SESSION['ValorEstoqueGrande'] = "È permitido apenas 1.000 no estoque";
    header("location: views/productEdit_page.php?nome=$nomeProduto&categoria=$categoriaProduto&valor=$valorProduto&estoque=$estoqueProduto&descricao=$descricaoProduto");
    exit();
}
if ($valorProduto > 10000) {
    $_SESSION['ValorGrande'] = 'È permitido apenas 10.000 no valor';
    header("Location: views/productEdit_page.php?nome=$nomeProduto&categoria=$categoriaProduto&valor=$valorProduto&estoque=$estoqueProduto&descricao=$descricaoProduto");
    exit();
}

    $sqlTodosProdut = "SELECT products.nome_products, products.valor_products, products.descricao_products, products.estoque_products, 
    category.nome_category FROM products LEFT JOIN category ON products.category_id_category = category.id_category
    WHERE id_products = :id_produto";
    $stmtTodosProdut = $connection->prepare($sqlTodosProdut);
    $stmtTodosProdut->bindParam(':id_produto', $idProduto);
    $stmtTodosProdut->execute();

    $resultProdut = $stmtTodosProdut->fetch(PDO::FETCH_ASSOC);
    
    if (
        $resultProdut['nome_products'] === $nomeProduto &&
        $resultProdut['nome_category'] === $categoriaProduto &&
        $resultProdut['valor_products'] == $valorProduto &&
    $resultProdut['estoque_products'] == $estoqueProduto &&
    $resultProdut['descricao_products'] === $descricaoProduto &&
    (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK)){
    $_SESSION['nenhuma_alteracao'] = "Nenhuma alteração detectada.";
    header("location: views/productEdit_page.php?nome=$nomeProduto&categoria=$categoriaProduto&valor=$valorProduto&estoque=$estoqueProduto&descricao=$descricaoProduto");
    exit();
}
    try {
    $sqlLoja = "SELECT id_loja FROM loja WHERE users_id_users = :id_user";
    $stmtLoja = $connection->prepare($sqlLoja);
    $stmtLoja->bindParam(':id_user', $_SESSION['user_id']);
    $stmtLoja->execute();

    $idLoja = $stmtLoja->fetch(PDO::FETCH_ASSOC);

    $sqlProduto = "SELECT COUNT(*) FROM products WHERE nome_products = :nomeProduto AND loja_id_loja = :id_loja AND id_products != :id_produto"; 
    $stmtProduto = $connection->prepare($sqlProduto);
    $stmtProduto->bindParam(':nomeProduto', $nomeProduto);
    $stmtProduto->bindParam(':id_loja', $idLoja['id_loja']);
    $stmtProduto->bindParam(':id_produto', $idProduto);
    $stmtProduto->execute();

    $resultNomeUsado = $stmtProduto->fetchColumn();

    if($resultNomeUsado > 0) {
    $_SESSION['nomeProdutoUsado'] = "O nome do produto ja esta em uso na sua loja";
    header ("location: views/productEdit_page.php?nome=$nomeProduto&categoria=$categoriaProduto&valor=$valorProduto&estoque=$estoqueProduto&descricao=$descricaoProduto");
    exit();
    }

    $sqlIdCategory = "SELECT category_id_category FROM products WHERE id_products = :id_produto";
    $stmtIdCategory = $connection->prepare($sqlIdCategory);
    $stmtIdCategory->bindParam(':id_produto', $idProduto);
    $stmtIdCategory->execute();

    $idCategoria = $stmtIdCategory->fetch(PDO::FETCH_ASSOC);

    $sqlCategoria = "UPDATE category SET nome_category = :categoria WHERE id_category = :id_categoria";
    $stmtCategoria = $connection->prepare($sqlCategoria);
    $stmtCategoria->bindParam(':categoria', $categoriaProduto);
    $stmtCategoria->bindParam(':id_categoria', $idCategoria['category_id_category']);
    $stmtCategoria->execute();

    if ($estoqueProduto > 0 && $resultProdut['estoque_products'] <= 0) {
        $sqlUpdateAtivo = " UPDATE products SET ativo = 1 WHERE id_products = :idProduto";
        $stmtUpdateAtivo = $connection->prepare($sqlUpdateAtivo);
        $stmtUpdateAtivo->bindParam(':idProduto', $idProduto);
        $stmtUpdateAtivo->execute();
    }

    $sql = "UPDATE products SET nome_products = :nome, valor_products = :valor, estoque_products = :estoque, descricao_products = :descricao 
    WHERE id_products = :id_produto";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':nome', $nomeProduto);
    $stmt->bindParam(':valor' ,$valorProduto);
    $stmt->bindParam(':estoque' , $estoqueProduto);
    $stmt->bindParam(':descricao', $descricaoProduto);
    $stmt->bindParam(':id_produto', $idProduto);
    $stmt->execute();

    $sql = "SELECT estoque_products FROM products WHERE id_products = :idProduto";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':idProduto', $idProduto);
    $stmt->execute();
    $estoque_atualizado = $stmt->fetchColumn();

    if ($estoqueProduto <= 0) {
        $sqlNovoEstoque = "UPDATE products SET ativo = CASE WHEN :novoEstoque > 0 THEN 1 ELSE 0 
        END WHERE id_products = :idProduto";
        $stmtNovoEstoque = $connection->prepare($sqlNovoEstoque);
            $stmtNovoEstoque->bindParam(':novoEstoque', $estoque_atualizado);
            $stmtNovoEstoque->bindParam(':idProduto', $idProduto);
            $stmtNovoEstoque->execute();
        }

    $sqlImgAtual = "SELECT caminho_img FROM imagens WHERE produtos_id_products = :id_produto";
    $stmtImgAtual = $connection->prepare($sqlImgAtual);
    $stmtImgAtual->bindParam(':id_produto', $idProduto);
    $stmtImgAtual->execute();
    $imgAtual = $stmtImgAtual->fetchColumn();

    if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        $urlImagem = $imgAtual;
    }else {
    ($imgAtual && file_exists(__DIR__ . "/../" . $imgAtual));
        unlink(__DIR__ . "/../" . $imgAtual);

        $imagem = $_FILES['imagem'];
        $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);          

        $nomeArquivo = hash('sha256', uniqid(mt_rand(), true)) . '.' . $extensao;
        $caminhoUpload = __DIR__ . '/img/img_produto/' . $nomeArquivo;

        if (!move_uploaded_file($imagem['tmp_name'], $caminhoUpload)) {
            $_SESSION['restrincao_imgProduto'] = "Erro ao salvar imagem";
            header("location: views/productEdit_page.php?nome=$nomeProduto&categoria=$categoriaProduto&valor=$valorProduto&estoque=$estoqueProduto&descricao=$descricaoProduto");
            exit();
        }

        $urlImagem = 'img/img_produto/' . $nomeArquivo;
    }
        $tipoImg = 'produto';
        
        $sqlImg = "UPDATE imagens SET caminho_img = :caminho_img WHERE produtos_id_products = :id_produto AND tipo_img = :tipo_img";
        $stmtImg = $connection->prepare($sqlImg);
        $stmtImg->bindParam(':tipo_img', $tipoImg);
        $stmtImg->bindParam(':caminho_img', $urlImagem);
        $stmtImg->bindParam(':id_produto', $idProduto);
        $stmtImg->execute();

    $_SESSION['atualizarProdutoSucesso'] = "Produto atualizado";
    header("location: views/productEdit_page.php");
    exit();
}catch (PDOException $e) {
    echo "Erro ao realizar edição da loja: " . $e->getMessage();
}
}
?>