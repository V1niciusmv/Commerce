<?php 
session_start();
require 'bd/connection.php';

if (isset($_POST['adicionar_produto'])) {
    $nomeProduto = $_POST['nome'];
    $categoriaProduto = $_POST['categoria'];
    $valorProduto = $_POST['valor'];
    $estoqueProduto = $_POST['estoque'];
    $descricaoProduto = $_POST['descricao'];
    $userId = $_SESSION['user_id'];

    if ((empty($nomeProduto))  || (empty($categoriaProduto)) || (empty($valorProduto)) || (empty($estoqueProduto)) || (empty($descricaoProduto)) || (!isset($_FILES['imagem']) 
    || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK)) {
        $_SESSION['restrincao_criarProduto'] = "Preencha todos os campos";
        header("location: views/product_page.php?nome=$nomeProduto&categoria=$categoriaProduto&valor=$valorProduto&estoque=$estoqueProduto&descricao=$descricaoProduto");
        exit();
}
    if ($estoqueProduto > 1000) {
        $_SESSION['ValorEstoqueGrande'] = 'È permitido apenas 1.000 no estoque';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    if ($valorProduto > 10000) {
        $_SESSION['ValorGrande'] = 'È permitido apenas 10.000 no valor';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

try {
    $sql = "SELECT COUNT(*) FROM products WHERE nome_products = :nome_produto AND users_id_users = :user_id";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':nome_produto', $nomeProduto);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    $verificarNome = $stmt->fetchColumn();
    if($verificarNome > 0) {
        $_SESSION['nomeUtilizado'] = 'Você ja tem um produto com esse nome cadastrado';
        header ('location: views/product_page.php');
        exit();
    }

    $sqlLoja = "SELECT id_loja FROM loja WHERE users_id_users = :id_user";
    $stmtLoja = $connection->prepare($sqlLoja);
    $stmtLoja->bindParam(':id_user', $_SESSION['user_id']);
    $stmtLoja->execute();
    $loja = $stmtLoja->fetch(PDO::FETCH_ASSOC);
    $id_loja = $loja['id_loja'];

    $sqlCategory = "INSERT INTO category (nome_category) VALUES (:nome_categoria)";
    $stmtCategory = $connection->prepare($sqlCategory);
    $stmtCategory->bindParam(':nome_categoria', $categoriaProduto);
    $stmtCategory->execute();

    $idCategoria = $connection->lastInsertId();

    $sql = "INSERT INTO products (nome_products, valor_products, descricao_products, estoque_products, users_id_users, loja_id_loja, category_id_category) 
    VALUES (:nome, :valor, :descricao, :estoque, :id_user, :id_loja, :id_category)";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':nome', $nomeProduto);
    $stmt->bindParam(':valor', $valorProduto);
    $stmt->bindParam(':estoque', $estoqueProduto);
    $stmt->bindParam(':descricao', $descricaoProduto);
    $stmt->bindParam(':id_user', $_SESSION['user_id']);
    $stmt->bindParam(':id_loja', $id_loja);
    $stmt->bindParam(':id_category',$idCategoria);
    $stmt->execute();

    $idProduto = $connection->lastInsertId();

    $imagem = $_FILES['imagem'];
    $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);

    $nomeArquivo = hash('sha256', uniqid(mt_rand(), true)) . '.' . $extensao;
    $caminhoUpload = __DIR__ . '/img/img_produto/' . $nomeArquivo;

    if (!move_uploaded_file($imagem['tmp_name'], $caminhoUpload)) {
        $_SESSION['restricao_criarImgProduto'] = "Erro ao salvar imagem";
        header("location: views/product_page.php?nome=$nomeProduto&categoria=$categoriaProduto&valor=$valorProduto&estoque=$estoqueProduto&descricao=$descricaoProduto");
        exit();
    }

    $urlImagem = 'img/img_produto/' . $nomeArquivo;
    $tipoImg = 'produto';

    $sqlImg = "INSERT INTO imagens (tipo_img, caminho_img, produtos_id_products) 
        VALUES (:tipo_img, :caminho_img, :produtos_id_products)";
    $stmtImg = $connection->prepare($sqlImg);
    $stmtImg->bindParam(':tipo_img', $tipoImg);
    $stmtImg->bindParam(':caminho_img', $urlImagem);
    $stmtImg->bindParam(':produtos_id_products', $idProduto);
    $stmtImg->execute();
    
    $_SESSION['cadastroProduto_sucesso'] = "Produto criado com sucesso";
    header('location: views/product_page.php');
    exit();
} catch (PDOException $e) {
    echo "Erro ao realizar cadastro do produto: " . $e->getMessage();
}
}
?>