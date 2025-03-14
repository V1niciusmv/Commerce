<?php 
session_start();
require 'bd/connection.php';

if (isset($_POST['id_cart'])) {
    $id_cart = $_POST=['id_cart'];
}
?>