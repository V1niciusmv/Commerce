<?php 
session_start();
require 'bd/connection.php';

if(isset($_GET['id'])){
    $idProduto = $_GET['id'];

    $_SESSION['idProduto'] = $idProduto;
    header('location: views/home_page.php');
    exit();
}
?>