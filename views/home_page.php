<?php
session_start();
require '../bd/connection.php';
if (!isset($_SESSION['user_id'])) {
    header('location: register_page.php?action=login');
    exit();
}

$sql = "SELECT COUNT(*) FROM products WHERE users_id_users = :id_user";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':id_user', $_SESSION['user_id']);
$stmt->execute();

$existeProduto = $stmt->fetchColumn();
 if($existeProduto > 0) {
    $produto = $existeProduto;
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/product.css">
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
        <?php include ('productView_page.php') ?>
    </div>
       <script>
    const verificarSeTem = <?= isset($produto) ? 'true' : 'false' ?>;

    if (verificarSeTem) {
        const categorias = {
            1: "Eletrônicos",
            2: "Comidas",
            3: "Bebidas",
            4: "Roupas",
            5: "Acessórios",
            6: "Móveis",
            7: "Brinquedos",
            8: "Livros",
            9: "Ferramentas",
            10: "Beleza e Cuidados",
            11: "Esportes",
            12: "Saúde",
            13: "Automotivo",
            14: "Casa e Decoração",
            15: "Jardinagem",
            16: "Tecnologia",
            17: "Higiene",
            18: "Informática"
        };

        const inputsCategoria = document.querySelectorAll('#categoria');

        inputsCategoria.forEach(input => {
            const categoriaId = input.value;

            if (categorias[categoriaId]) {
                input.value = categorias[categoriaId];
            }
        });
    }
    </script>
</body>
</html>