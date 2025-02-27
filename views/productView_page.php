<?php
require_once '../bd/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('location: register_page.php?action=login');
    exit();
}

$page = explode('/views/', $_SERVER['REQUEST_URI'])[1];
if ($page == 'home_page.php') {
    $sql = "SELECT products.*, imagens.caminho_img, loja.id_loja, category.id_category, category.nome_category
FROM products
LEFT JOIN imagens ON products.id_products = produtos_id_products 
LEFT JOIN loja ON products.loja_id_loja= id_loja
LEFT JOIN category ON products.category_id_category = id_category";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
} else {
    $sql = "SELECT products.*, imagens.caminho_img, loja.id_loja, category.id_category, category.nome_category
FROM products
LEFT JOIN imagens ON products.id_products = produtos_id_products 
LEFT JOIN loja ON products.loja_id_loja= id_loja
LEFT JOIN category ON products.category_id_category = id_category 
WHERE products.users_id_users = :user_id";

    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
}
if ($stmt->rowCount() > 0) {
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $produtos = [];
}
?>

<div class="show-products">
    <?php if ($page == 'home_page.php'): ?>
        <?php foreach ($produtos as $produto): ?>
            <div class="product" onclick="openModal(<?= $produto['id_products'] ?>)">
                <div class="img_product">
                    <img src=" ../<?= $produto['caminho_img'] ?>">
                </div>
                <div class="details">
                    <div class="nome-product">
                        <input type="text" name="nome" value="<?= $produto['nome_products'] ?>" readonly required>
                    </div>
                    <div class="nome-category">
                        <input id="categoria" type="text" name="categoria" value="<?= $produto['nome_category'] ?>" readonly
                            required>
                    </div>
                </div>
                <div class="priceEestoque">
                    <div class="dollar">
                        <p> R$ </p>
                        <input type="number" id="input" name="valor" value="<?= $produto['valor_products'] ?>" readonly
                            required>
                    </div>
                    <div class="box">
                        <input type="number" name="estoque" value="<?= $produto['estoque_products'] ?>" readonly required>
                        <i class='bx bx-box'></i>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    <?php else: ?>
        <?php foreach ($produtos as $produto): ?>
            <div>
                <div class="product" onclick="openModal(<?= $produto['id_products'] ?>)">
                    <div class="img_product">
                        <img src=" ../<?= $produto['caminho_img'] ?>">
                    </div>
                    <div class="details">
                        <div class="nome-product">
                            <input type="text" name="nome" value="<?= $produto['nome_products'] ?> " readonly required>
                        </div>
                        <div class="nome-category">
                            <input id="cat-pro" type="text" name="categoria" value="<?= $produto['nome_category'] ?>" readonly
                                required>
                        </div>
                    </div>
                    <div class="priceEestoque">
                        <div class="dollar">
                            <p> R$ </p>
                            <input type="number" id="input" name="valor" value="<?= $produto['valor_products'] ?>" readonly
                                required>
                        </div>
                        <div class="box">
                            <i class='bx bx-box'></i>
                            <input type="number" name="estoque" value="<?= $produto['estoque_products'] ?>" readonly required>
                        </div>
                    </div>
                </div>
                <div class="buttons">
                    <form action="productEdit_page.php" method="POST">
                        <input type="hidden" name="produto" value="<?= $produto['id_products'] ?>">
                        <button type="submit">
                            <i class='bx bx-pencil'></i>
                        </button>
                    </form>

                    <form action="../productDelete.php" method="POST" style="display : inline">
                        <input type="hidden" name="id_produto" value="<?= $produto['id_products'] ?>">
                        <button type=submit name="deletar_produto">
                            <i class='bx bx-trash'></i></button>
                    </form>
                </div>
            </div>
        <?php endforeach ?>
    <?php endif ?>

    <!-- MODAL PRODUTOS  -->
    <div class="div-modal" id="modalProduct" style="display :none">
        <?php if ($page == 'home_page.php'): ?>
            <div class="modalProduct">
                <div class="div1-modal">
                    <div class="img_product-modal">
                        <img id="modal-img" src=" ../<?= $produto['caminho_img'] ?>">
                    </div>
                    <div class="details-modal">
                        <div class="nome-product-modal">
                            <input type="text" name="nome" id="modal-nome" readonly required>
                        </div>
                        <div class="nome-category-modal">
                            <input type="text" name="categoria" id="modal-categoria" readonly required>
                        </div>
                    </div>
                </div>
                <div class="priceEstoque-modal">
                    <div class="dollar-modal">
                        <p> R$ </p>
                        <input type="number" name="valor" id="modal-valor" readonly required>
                    </div>
                    <div class="box-modal">
                        <i class='bx bx-box'></i>
                        <input type="number" name="estoque" id="modal-estoque" readonly required>
                    </div>
                    <div class="descricao-modal">
                        <label> Descrição </label>
                        <textarea id="modal-descricao" name="descricao" rows="5" cols="40" readonly>
                            </textarea>
                    </div>
                    <div class="link-modal">
                        <div class="ceta" onclick="fecharModal()">
                            <i class='bx bx-arrow-back'></i>
                        </div>
                        <div class="button-link-modal">
                            <button> Comprar </button>
                        </div>
                        <div class="ceta">
                            <i class='bx bx-cart-add'></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="modalProduct">
                <div class="div1-modal">
                    <div class="img_product-modal">
                        <img id="modal-img" src=" ../<?= $produto['caminho_img'] ?>">
                    </div>
                    <div class="details-modal">
                        <div class="nome-product-modal">
                            <input type="text" name="nome" id="modal-nome" readonly required>
                        </div>
                        <div class="nome-category-modal">
                            <input type="text" name="categoria" id="modal-categoria" readonly required>
                        </div>
                    </div>
                </div>
                <div class="priceEstoque-modal">
                    <div class="dollar-modal">
                        <p> R$ </p>
                        <input type="number" name="valor" id="modal-valor" readonly required>
                    </div>
                    <div class="box-modal">
                        <i class='bx bx-box'></i>
                        <input type="number" name="estoque" id="modal-estoque" readonly required>
                    </div>
                    <div class="descricao-modal">
                        <label> Descrição </label>
                        <textarea id="modal-descricao" name="descricao" rows="5" cols="40" readonly>
                            </textarea>
                    </div>
                    <div class="ceta-back" onclick="fecharModal()">
                        <i class='bx bx-arrow-back'></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>