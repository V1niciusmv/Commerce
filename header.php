<?php
session_start();
require 'bd/connection.php';

if (isset($_GET['query'])) {
  $query = $_GET['query'];

  $sql = "SELECT id_products, nome_products FROM products WHERE nome_products LIKE :query 
  ORDER BY nome_products COLLATE utf8mb4_general_ci ASC";
$stmt = $connection->prepare($sql);
$stmt->bindValue(':query', "$query%");
$stmt->execute();
  
  $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  echo json_encode($produtos);
  
} else {
  echo json_encode(["error" => "Nenhuma consulta fornecida"]);
}
?>
