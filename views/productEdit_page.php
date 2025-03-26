<?php
session_start();
require '../bd/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('location: register_page.php?action=login');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['produto'])) {
    $_SESSION['id_produto'] = $_POST['produto']; 
}

$sql = "SELECT products.*, imagens.caminho_img, loja.id_loja, category.id_category, category.nome_category
FROM products
LEFT JOIN imagens ON products.id_products = produtos_id_products 
LEFT JOIN loja ON products.loja_id_loja= id_loja
LEFT JOIN category ON products.category_id_category = id_category 
WHERE products.users_id_users = :user_id AND products.id_products = :id_produto";

$stmt = $connection->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->bindParam(':id_produto', $_SESSION['id_produto']);
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
    <div class="container-product-edit">
        <h3> Edite seus produtos </h3>
    <div class="sessionsMensage">
        <?php if (isset($_SESSION['atualizarProdutoSucesso'])) {
                echo '<p class="sessionGreen">' . $_SESSION['atualizarProdutoSucesso'] . '</p>';
                unset($_SESSION['atualizarProdutoSucesso']);
            } ?>
            <?php if (isset($_SESSION['nomeProdutoUsado'])) {
                echo '<p class="sessionRed">' . $_SESSION['nomeProdutoUsado'] . '</p>';
                unset($_SESSION['nomeProdutoUsado']);
            } ?>
            <?php if (isset($_SESSION['restrincao_editarLoja'])) {
                echo '<p class="sessionRed">' . $_SESSION['restrincao_editarLoja'] . '</p>';
                unset($_SESSION['restrincao_editarLoja']);
            } ?>
            <?php if (isset($_SESSION['restrincao_imgProduto'])) {
                echo '<p class="sessionRed">' . $_SESSION['restrincao_imgProduto'] . '</p>';
                unset($_SESSION['restrincao_imgProduto']);
            } ?>
            <?php if (isset($_SESSION['nenhuma_alteracao'])) {
                echo '<p class="sessionRed ">' . $_SESSION['nenhuma_alteracao'] . '</p>';
                unset($_SESSION['nenhuma_alteracao']);
            } ?>
             <?php if (isset($_SESSION['ValorEstoqueGrande'])) {
                echo '<p class="sessionRed">' . $_SESSION['ValorEstoqueGrande'] . '</p>';
                unset($_SESSION['ValorEstoqueGrande']);
            } ?>
             <?php if (isset($_SESSION['ValorGrande'])) {
                echo '<p class="sessionRed">' . $_SESSION['ValorGrande'] . '</p>';
                unset($_SESSION['ValorGrande']);
            } ?>
            </div>
            <form action="../productEdit.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_product" value="<?= $produto['id_products'] ?>">
            
                <div class="div1">
                    <img id="img-produto" src="../<?= $produto['caminho_img'] ?>">
                    <input type="file" accept="image/*" name="imagem" id="input-img-produto" style="display:none">
                    <i class="bx bx-camera" id="id-icon"></i>
                </div>
        <div class="div2"> 
            <div class="nomeCat">
                <div class="resultados-div">
                    <label> Nome do produto</label>
                    <input type="text" name="nome" value="<?= $produto['nome_products'] ?>" required>
                </div>
                <div class="resultados-div-cat">
                    <label>Categoria</label>
                        <select name="categoria" id="categoriia" required>
                            <option value="1" <?= ($produto['nome_category'] == '1') ? 'selected' : '' ?>> Eletrônicos</option>
                            <option value="2" <?= ($produto['nome_category'] == '2') ? 'selected' : '' ?>>Comidas
                            </option>
                            <option value="3" <?= ($produto['nome_category'] == '3') ? 'selected' : '' ?>>Bebidas
                            </option>
                            <option value="4" <?= ($produto['nome_category'] == '4') ? 'selected' : '' ?>>Roupas
                            </option>
                            <option value="5" <?= ($produto['nome_category'] == '5') ? 'selected' : '' ?>>
                                Acessórios</option>
                            <option value="6" <?= ($produto['nome_category'] == '6') ? 'selected' : '' ?>>Móveis
                            </option>
                            <option value="7" <?= ($produto['nome_category'] == '7') ? 'selected' : '' ?>>
                                Brinquedos</option>
                            <option value="8" <?= ($produto['nome_category'] == '8') ? 'selected' : '' ?>>Livros
                            </option>
                            <option value="9" <?= ($produto['nome_category'] == '9') ? 'selected' : '' ?>>
                                Ferramentas</option>
                            <option value="10" <?= ($produto['nome_category'] == '10') ? 'selected' : '' ?>>
                                Beleza e Cuidados</option>
                            <option value="11" <?= ($produto['nome_category'] == '11') ? 'selected' : '' ?>>Esportes
                            </option>
                            <option value="12" <?= ($produto['nome_category'] == '12') ? 'selected' : '' ?>>Saúde
                            </option>
                            <option value="13" <?= ($produto['nome_category'] == '13') ? 'selected' : '' ?>>
                                Automotivo</option>
                            <option value="14" <?= ($produto['nome_category'] == '14') ? 'selected' : '' ?>>
                                Casa e Decoração</option>
                            <option value="15" <?= ($produto['nome_category'] == '15') ? 'selected' : '' ?>>
                                Jardinagem</option>
                            <option value="16" <?= ($produto['nome_category'] == '16') ? 'selected' : '' ?>>
                                Tecnologia</option>
                            <option value="17" <?= ($produto['nome_category'] == '17') ? 'selected' : '' ?>>Higiene
                            </option>
                            <option value="18" <?= ($produto['nome_category'] == '18') ? 'selected' : '' ?>>
                                Informática</option>
                        </select>
                </div>
            </div>
            <div class="valorQuant">
                <div class="resultados-div">
                    <label> Valor</label>
                    <i id="less" class='bx bx-minus'></i>
                    <input type="number" id="input" name="valor" value="<?= $produto['valor_products'] ?>" required>
                    <i id="more" class='bx bx-plus'></i>
                </div>
                <div class="resultados-div">
                    <label> Quantidade</label>
                    <input type="number" name="estoque" value="<?= $produto['estoque_products'] ?>" required>
                </div>
            </div>
                <div class="descri-edit">
                    <label> Descrição </label>
                    <textarea id="descricao" name="descricao" rows="5" cols="40" placeholder="Digite a descrição do produto">
                    <?= htmlspecialchars(trim($produto['descricao_products'])) ?>
                    </textarea>
                </div>
            <div class="btn-edit">
                <button type="submit" name="editar_produto"> Salvar </button>
            </div>
        </div>
            </form>
    </div>
    <script>
        const moreIcon = document.getElementById("more");
        const lessIcon = document.getElementById("less");
        const input = document.getElementById("input");

        moreIcon.addEventListener("click", () => {
            input.value = parseInt(input.value || 0) + 1;
        });

        lessIcon.addEventListener("click", () => {
            input.value = Math.max(0, parseInt(input.value || 0) - 1);
        });

        document.addEventListener("DOMContentLoaded", function () {
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
    });

        const inputCategoria = document.querySelector('#categoriia');
        const categoriaId = inputCategoria.value;

        if (categorias[categoriaId]) {
            inputCategoria.value = categorias[categoriaId];
        }

        const cameraIcon = document.getElementById('id-icon');
        const inputImg = document.getElementById('input-img-produto');
        const imgProduto = document.getElementById('img-produto');

        cameraIcon.addEventListener('click', () => {
            inputImg.click();
        });

        inputImg.addEventListener('change', (event) => {
            const file = event.target.files[0]; // Obtém o arquivo selecionado
            if (file) {
                const reader = new FileReader(); // Cria um leitor de arquivos
                reader.onload = function (e) {
                    imgProduto.src = e.target.result; // Atualiza a imagem mostrada
                };
                reader.readAsDataURL(file); // Lê o arquivo como uma URL local
            }
        });
    </script>
</body>

</html>