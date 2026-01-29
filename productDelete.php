<?php
session_start();
require 'bd/connection.php';

if (isset($_POST['deletar_produto'])) {
    $idProduto = $_POST['id_produto'];

    session_start();
require 'bd/connection.php';

if (!isset($_POST['deletar_produto'])) {
    exit();
}

$idProduto = $_POST['id_produto'];

try {
    $connection->beginTransaction();

    // 1️⃣ Apagar itens de venda ligados ao produto
    $sqlVHP = "DELETE FROM vendas_has_products 
               WHERE products_id_products = :id_produto";
    $stmtVHP = $connection->prepare($sqlVHP);
    $stmtVHP->bindParam(':id_produto', $idProduto);
    $stmtVHP->execute();

    // 2️⃣ Apagar imagens do produto
    $sqlImg = "DELETE FROM imagens 
               WHERE produtos_id_products = :id_produto";
    $stmtImg = $connection->prepare($sqlImg);
    $stmtImg->bindParam(':id_produto', $idProduto);
    $stmtImg->execute();

    // 3️⃣ Apagar do carrinho (se existir)
    $sqlCart = "DELETE FROM cart_items 
                WHERE product_id = :id_produto";
    $stmtCart = $connection->prepare($sqlCart);
    $stmtCart->bindParam(':id_produto', $idProduto);
    $stmtCart->execute();

     $sqlIdCategory ="SELECT category_id_category FROM products WHERE id_products = :id_produto";
    $stmtIdCategory = $connection->prepare($sqlIdCategory);
    $stmtIdCategory->bindParam(':id_produto', $idProduto);
    $stmtIdCategory->execute();

    $idCategoria = $stmtIdCategory->fetch(PDO::FETCH_ASSOC);

    $sqlCategory = "DELETE FROM category WHERE id_category = :id_category";
    $stmtCategory = $connection->prepare($sqlCategory);
    $stmtCategory->bindParam(':id_category', $idCategoria['category_id_category']);
    $stmtCategory->execute();

    // 4️⃣ Apagar o produto
    $sqlProduto = "DELETE FROM products 
                   WHERE id_products = :id_produto";
    $stmtProduto = $connection->prepare($sqlProduto);
    $stmtProduto->bindParam(':id_produto', $idProduto);
    $stmtProduto->execute();

    $connection->commit();

    header('Location: views/product_page.php');
    exit();

} catch (Exception $e) {
    $connection->rollBack();
    die("Erro ao deletar produto: " . $e->getMessage());
}
}
?>