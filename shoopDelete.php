<?php
session_start();
require 'bd/connection.php';

if (isset($_POST['deletar_loja'])) {
    $idLoja = $_POST['id_loja'];
    
    $sql = "SELECT COUNT(*) FROM products WHERE loja_id_loja = :id_loja"; 
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':id_loja', $idLoja);
    $stmt->execute();

    $resultIdLoja = $stmt->fetchColumn();
    if($resultIdLoja > 0) {
        $_SESSION['produto_apagar'] =  'Para apagar a loja, você deve apagar os produtos primeiro';
        header ("location: ../views/shoop_page.php");
        exit();
    }

    $sqlImg= "DELETE FROM imagens WHERE lojas_id_loja = :id_loja";
    $stmtImg = $connection->prepare($sqlImg);
    $stmtImg->bindParam(':id_loja', $idLoja);
    $stmtImg->execute();

    $sqlLoja = "DELETE FROM loja WHERE id_loja = :id_loja";
    $stmtLoja = $connection->prepare($sqlLoja);
    $stmtLoja->bindParam(':id_loja', $idLoja);
    $stmtLoja->execute();

    $_SESSION['loja_deletada'] ="Loja deletada com sucesso";
    header ('location: ../views/shoop_page.php');
    exit();
}
?>