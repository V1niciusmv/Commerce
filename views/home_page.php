<?php
session_start();
require '../bd/connection.php';
if (!isset($_SESSION['user_id'])) {
    header('location: register_page.php?action=login');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=b">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/home.css">
    <title>Bem vindo</title>
</head>

<body>
    <?php
    include('header_page.php');
    ?>
    <div class="container-main-full">
        <div class="first">
            <h1> Produtos </h1>
            <i class='bx bx-filter' alt="filtros"></i>
        </div>
            <?php if (isset($produto)): ?>
            <?php include ('viewProduct_page.php') ?>;

            <?php else: ?>
                
            <?php endif ?>   
    </div>
</body>

</html>