<?php
session_start();
require '../bd/connection.php';

$sql = "SELECT COUNT(*) FROM products WHERE users_id_users = :id_user";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':id_user', $_SESSION['user_id']);
$stmt->execute();

$existeProduto = $stmt->fetchColumn();
if ($existeProduto > 0) {
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
            <?php if (isset($_SESSION['produto_no_carrinho'])) {
                echo '<p class="sessionRed ">' . $_SESSION['produto_no_carrinho'] . '</p>';
                unset($_SESSION['produto_no_carrinho']);
            } ?>
              <?php if (isset($_SESSION['Existe_produtoAdd'])) {
                echo '<p class="sessionRed ">' . $_SESSION['Existe_produtoAdd'] . '</p>';
                unset($_SESSION['Existe_produtoAdd']);
            } ?>
            <i class='bx bx-filter' alt="filtros"></i>
        </div>
        <?php include('productView_page.php') ?>
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

        function openModal(idProduto) {
            const pro = <?= json_encode($produtos) ?>;
            const product = pro.find(products => products.id_products == idProduto);

            document.getElementById('modalProduct').style.display = 'flex';

            document.getElementById('modal-id-product-comprar').value = product.id_products;
            document.getElementById('modal-id-product-adicionar').value = product.id_products;
            document.getElementById('modal-img').src = '../' + product.caminho_img;
            document.getElementById('modal-nome').value = product.nome_products;
            document.getElementById('modal-categoria').value = product.nome_category;
            const categoria = {
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
            const categoriaIdModal = product.nome_category;

            if (categoria[categoriaIdModal]) {
                document.getElementById('modal-categoria').value = categoria[categoriaIdModal];
            }
            document.getElementById('modal-valor').value = product.valor_products;
            document.getElementById('modal-estoque').value = product.estoque_products;
            document.getElementById('modal-descricao').value = product.descricao_products;
        }

        function fecharModal() {
            document.getElementById('modalProduct').style.display = 'none';
        }

        const veriUserOn =  <?= (isset($_SESSION['user_id'])) ? 'true' : 'false' ?>;
        if (veriUserOn) {
            document.getElementById('add-carrinho').addEventListener("click", function () {
                document.getElementById('id-form-cart-add').submit();
            })
        }
        
        const veriUser =  <?= (!isset($_SESSION['user_id'])) ? 'true' : 'false' ?>;
        function verificarUser(event) {
          if (veriUser) {
            window.location.href='register_page.php'; 
            return;
          }
            event.target.closest('form').submit();
        }
    </script>
</body>

</html>