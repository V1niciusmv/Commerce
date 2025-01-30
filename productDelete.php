<?php
session_start();
require 'bd/connection.php';

if (isset($_POST['deletar_produto'])) {
    $idProduto = $_POST['id_produto'];

    $sqlImg= "DELETE FROM imagens WHERE produtos_id_products = :id_produto";
    $stmtImg = $connection->prepare($sqlImg);
    $stmtImg->bindParam(':id_produto', $idProduto);
    $stmtImg->execute();

    $sqlIdCategory ="SELECT category_id_category FROM products WHERE id_products = :id_produto";
    $stmtIdCategory = $connection->prepare($sqlIdCategory);
    $stmtIdCategory->bindParam(':id_produto', $idProduto);
    $stmtIdCategory->execute();

    $idCategoria = $stmtIdCategory->fetch(PDO::FETCH_ASSOC);

    $sqlCategory = "DELETE FROM category WHERE id_category = :id_category";
    $stmtCategory = $connection->prepare($sqlCategory);
    $stmtCategory->bindParam(':id_category', $idCategoria['category_id_category']);
    $stmtCategory->execute();

    $sqlProduto = "DELETE FROM products WHERE id_products = :id_produto";
    $stmtProduto = $connection->prepare($sqlProduto);
    $stmtProduto->bindParam(':id_produto', $idProduto);
    $stmtProduto->execute();

    $_SESSION['produto_deletado'] ="Produto deletado com sucesso";
    header ('location: ../views/product_page.php');
    exit();
}
?>