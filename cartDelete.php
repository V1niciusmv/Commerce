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
    header ('location: views/buy_page.php');
}

$sqlIdCartUser = "SELECT id_cart FROM cart WHERE user_id_cart = :idUser";
$stmtIdCartUser  = $connection->prepare($sqlIdCartUser);
$stmtIdCartUser ->bindParam(':idUser', $_SESSION['user_id']);
$stmtIdCartUser ->execute();

$verificarCart = $stmtIdCartUser ->fetch(PDO::FETCH_ASSOC);

$sqlTodosProdutos = "SELECT COUNT(*) FROM cart_items WHERE cart_id = :idCart";
$stmtTodosProdutos = $connection->prepare($sqlTodosProdutos);
$stmtTodosProdutos->bindParam(':idCart', $verificarCart['id_cart']);
$stmtTodosProdutos->execute();
$quantidadeProdutos = $stmtTodosProdutos->fetchColumn();

if ($quantidadeProdutos == 0) {
    $sql = "DELETE FROM cart WHERE id_cart = :idCart";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':idCart', $verificarCart['id_cart']);
    $stmt->execute();
    exit();
}
?>