<?php
session_start();
require 'bd/connection.php';

if (isset($_GET['query'])) { // Pega via GET o parametro enviado pelo Fetch, $itens
  $query = $_GET['query'];  // salva a informação digitada no input

  // Seleciona o id e nome do produto aonde nome do produto é igual a query passada e que ele esteja com estoque ativo
  // Usa ORDER BY para ordenar as buscas (A - Z)
  $sql = "SELECT id_products, nome_products FROM products WHERE nome_products LIKE :query AND ativo = 1 
  ORDER BY nome_products COLLATE utf8mb4_general_ci ASC";
$stmt = $connection->prepare($sql);
$stmt->bindValue(':query', "$query%");
$stmt->execute();
  
  $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  echo json_encode($produtos); // Envia o resultado para o fetch
  
} else {
  echo json_encode(["error" => "Nenhuma consulta fornecida"]);
}
?>
