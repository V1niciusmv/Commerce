<?php 
session_start();
require 'bd/connection.php';

if(isset($_POST['deletar_produto'])) {
    $idProduto = $_POST['id_produto'];
}

$sql = "SELECT id_cart_item FROM cart_items WHERE product_id = :produtoId";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':produtoId', $idProduto);
$stmt->execute();

$idCart = $stmt->fetch(PDO::FETCH_ASSOC);

if ($idCart) {
    $sqlDelete = "DELETE FROM cart_items WHERE id_cart_item = :idCart";
    $stmtDelete = $connection->prepare($sqlDelete);
    $stmtDelete->bindParam(':idCart', $idCart['id_cart_item']);
    $stmtDelete->execute();
    $_SESSION['Produto_carro_deletado'] = "Produto deletado do carrinho";
    header('location: views/buy_page.php');
    exit();
}
?>