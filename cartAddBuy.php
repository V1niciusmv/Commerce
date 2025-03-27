<?php 
session_start();
require 'bd/connection.php';

if(isset($_POST['id_produto'])) {
    $id_produto = $_POST['id_produto'];
}

$quantity = 1 ;

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

$sql = "SELECT COUNT(*) FROM cart_items WHERE product_id = :idProduto";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':idProduto', $id_produto);
$stmt->execute();

$verificarProduto = $stmt->fetchColumn();
if ( $verificarProduto > 0) {
    $_SESSION['Existe_produtoAdd'] = "Item ja existe no carrinho";
    header ('location: views/home_page.php');
    exit();
}else {
    $sqlInsert = "INSERT INTO cart_items (cart_id, product_id, quantity) 
    VALUES (:cart_id, :product_id, :quantity)";
     $stmtInsert = $connection->prepare($sqlInsert);
     $stmtInsert->bindParam(':cart_id', $cartId);
     $stmtInsert->bindParam(':product_id', $id_produto);
     $stmtInsert->bindParam(':quantity', $quantity);
     $stmtInsert->execute();
     header ('location: views/home_page.php');
     exit();
}
?>
