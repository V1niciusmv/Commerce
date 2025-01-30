<?php
session_start();
require '../bd/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('location: register_page.php?action=login');
    exit();
}

$sql = "SELECT products.*, imagens.caminho_img, loja.id_loja, category.id_category, category.nome_category
FROM products
LEFT JOIN imagens ON products.id_products = produtos_id_products 
LEFT JOIN loja ON products.loja_id_loja= id_loja
LEFT JOIN category ON products.category_id_category = id_category 
WHERE products.users_id_users = :user_id";

$stmt = $connection->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/product.css">
    <title>produtos</title>
</head>
<body>
    <?php include('header_page.php'); ?>
    <div class="container-product-full">
        <div id="formulario-view">
            <?php if (isset($produto)): ?>
                <div class="first" id="first">
                    <div class="itens">
                        <h4> Seus produtos </h4>
                        <div class="a" id="icon">
                            <i class='bx bx-plus'></i>
                        </div>
                    </div>
                    <div class="show-products">
                        <?php include('viewProduct_page.php'); ?>

                        <div class="buttons">
                            <button type="button" onclick="window.location.href='productEdit_page.php'">
                                <i class='bx bx-pencil'></i></button>

                            <form action="../productDelete.php" method="POST" style="display : inline">
                                <input type="hidden" name="id_produto" value="<?= $produto['id_products'] ?>">
                                <button type=submit name="deletar_produto">
                                    <i class='bx bx-trash'></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="oi" id="idCliqueAq">
                    <h1> Você ainda não tem produtos</h1>
                    <div class="cliqueAq" id="cadastrar"> Cadastrar </div>
                </div>
            <?php endif ?>
            <div class="container-form-cadastro" id="form-cadastro">
            <h3> Cadastre seu produto </h3>
            <h4> cadastre</h4>
            <?php if (isset($_SESSION['cadastroProduto_sucesso'])) {
                echo '<p class="">' . $_SESSION['cadastroProduto_sucesso'] . '</p>';
                unset($_SESSION['cadastroProduto_sucesso']);
            } ?>
            <?php if (isset($_SESSION['restricao_criarImgProduto'])) {
                echo '<p class="">' . $_SESSION['restricao_criarImgProduto'] . '</p>';
                unset($_SESSION['restricao_criarImgProduto']);
            } ?>
            <?php if (isset($_SESSION['produto_deletado'])) {
                echo '<p class="">' . $_SESSION['produto_deletado'] . '</p>';
                unset($_SESSION['produto_deletado']);
            } ?>
            <form class="" action="../product.php" method="POST" enctype="multipart/form-data">
                <div>
                    <div class="">
                        <label> Nome do produto</label>
                        <input type="text" name="nome" required>
                    </div>
                    <div class="">
                        <label> Adicione uma imagem para seu produto: </label>
                        <input type="file" name="imagem" accept="image/*">
                    </div>
                    <div class="">
                        <label>Categoria</label>
                        <select name="categoria" id="">
                            <option>Selecionar</option>
                            <option value="1">Eletrônicos</option>
                            <option value="2">Comidas</option>
                            <option value="3">Bebidas</option>
                            <option value="4">Roupas</option>
                            <option value="5">Acessórios</option>
                            <option value="6">Móveis</option>
                            <option value="7">Brinquedos</option>
                            <option value="8">Livros</option>
                            <option value="9">Ferramentas</option>
                            <option value="10">Beleza e Cuidados</option>
                            <option value="11">Esportes</option>
                            <option value="12">Saúde</option>
                            <option value="13">Automotivo</option>
                            <option value="14">Casa e Decoração</option>
                            <option value="15">Jardinagem</option>
                            <option value="16">Tecnologia</option>
                            <option value="17">Higiene</option>
                            <option value="18">Informática</option>
                        </select>
                    </div>
                    <div class="">
                        <label> Valor :</label>
                        <i id="less" class='bx bx-minus'></i>
                        <input type="number" id="input" name="valor" required>
                        <i id="more" class='bx bx-plus'></i>
                    </div>
                    <div class="">
                        <label> Quantidade :</label>
                        <input type="nunmber" name="estoque" required>
                    </div>
                    <div class="">
                        <label for="descricao"> Descrição </label>
                        <textarea id="descricao" name="descricao" rows="5" cols="40"
                            placeholder="Digite a descrição do produto"></textarea>
                    </div>
                    <div class="">
                        <button type="submit" name="adicionar_produto"> Adicionar</button>
                    </div>
                </div>
            </form>
            </div>
        </div>
        <script>
            document.getElementById('cadastrar').addEventListener("click", function () {
                const semCadastro = document.getElementById('idCliqueAq');
                const cadastro = document.getElementById('form-cadastro');

                semCadastro.style.display = 'none';
                cadastro.style.display = 'flex';
            });
            

            document.getElementById('icon').addEventListener("click", function () {
                const produtos = document.getElementById('first');
                
                produtos.style.display='none';
                cadastro.style.display = 'flex';
            });
    
        </script>
</body>

</html>