<?php
session_start();
require 'bd/connection.php';

if (isset($_POST['id_produto'])) {
    $id_produto = $_POST['id_produto'];
}

$sql = "SELECT id_cart FROM cart WHERE user_id_cart = :idUser";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':idUser', $_SESSION['user_id']);
$stmt->execute();

$verificarCart = $stmt->fetch(PDO::FETCH_ASSOC);
if ($verificarCart) {
    $cartId = $verificarCart['id_cart'];
} else {
    $sql = "INSERT INTO cart (user_id_cart) VALUES (:user_id_cart)";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':user_id_cart', $_SESSION['user_id']);
    $stmt->execute();
    $cartId = $connection->lastInsertId();
}

$sqlChekProductInCart = "SELECT quantity FROM cart_items WHERE cart_id = :id_cart AND product_id = :product_id";
$stmtChekProductIdCart = $connection->prepare($sqlChekProductInCart);
$stmtChekProductIdCart->bindParam(':id_cart', $cartId);
$stmtChekProductIdCart->bindParam(':product_id', $id_produto);
$stmtChekProductIdCart->execute();
$quantidade = $stmtChekProductIdCart->fetch(PDO::FETCH_ASSOC);

if ($quantidade) {
    header('location: ../views/home_page.php');
    $_SESSION['produto_no_carrinho'] = 'O produto ja existe no seu carrinho de compras';
    exit();
} else {
    $sqlInsert = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES 
    (:cart_id, :product_id, 1)";
    $stmtInsert = $connection->prepare($sqlInsert);
    $stmtInsert->bindParam(':cart_id', $cartId);
    $stmtInsert->bindParam(':product_id', $id_produto);
    $stmtInsert->execute();

    $sqlAddIdCartProduct = "SELECT * FROM products WHERE id_products = :idProduto";
    $stmtAddIdCartProduct = $connection->prepare($sqlAddIdCartProduct);
    $stmtAddIdCartProduct->bindParam(':idProduto', $id_produto);
    $stmtAddIdCartProduct->execute();
    $_SESSION['produto_adicionado_carrinho'] = 'Produto adicionado';
    header('location: views/buy_page.php');
    exit();
}
?>