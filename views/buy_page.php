<?php
session_start();
require '../bd/connection.php';
if (!isset($_SESSION['user_id'])) {
    header('location: register_page.php?action=login');
    exit();
}

$sqlCart = "SELECT id_cart FROM cart WHERE user_id_cart = :idUser";
$stmtCart = $connection->prepare($sqlCart);
$stmtCart->bindParam(':idUser', $_SESSION['user_id']);
$stmtCart->execute();
$cart = $stmtCart->fetch(PDO::FETCH_ASSOC);
if ($cart) {
    $cartId = $cart['id_cart'];
} else {
    $cartId = null;
}

$sqlProdutosCarrinho = "SELECT products.*, imagens.caminho_img, loja.nome_loja, category.nome_category, cart_items.quantity
FROM cart_items 
INNER JOIN products ON cart_items.product_id = products.id_products
LEFT JOIN imagens ON products.id_products = imagens.produtos_id_products
LEFT JOIN loja ON products.loja_id_loja = loja.id_loja
LEFT JOIN category ON products.category_id_category = category.id_category
WHERE cart_items.cart_id = :cartId";

$stmtProdutos = $connection->prepare($sqlProdutosCarrinho);
$stmtProdutos->bindParam(':cartId', $cartId);
$stmtProdutos->execute();
$produtosCarrinho = $stmtProdutos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/buy.css">
    <title>Carrinho de compras</title>
</head>

<body>
    <?php
    include('header_page.php');
    ?>
    <div class="container-buyPage">
        <?php if (count($produtosCarrinho) > 0): ?>
            <div class="div1">
                <?php foreach ($produtosCarrinho as $produto): ?>
                    <?php if (isset($_SESSION['produto_carro_deletado'])) {
                        echo '<p class="sessionGreen">' . $_SESSION['produto_carro_deletado'] . '</p>';
                        unset($_SESSION['produto_carro_deletado']);
                    } ?>
                    <?php if (isset($_SESSION['estoqueMenorQuantidade'])) {
                        echo '<p class="sessionGreen">' . $_SESSION['estoqueMenorQuantidade'] . '</p>';
                        unset($_SESSION['estoqueMenorQuantidade']);
                    } ?>
                    <?php if (isset($_SESSION['compra_finalizada'])) {
                        echo '<p class="sessionGreen">' . $_SESSION['compra_finalizada'] . '</p>';
                        unset($_SESSION['compra_finalizada']);
                    } ?>
                    <?php if (isset($_SESSION['erro_email'])) {
                        echo '<p class="sessionGreen">' . $_SESSION['erro_email'] . '</p>';
                        unset($_SESSION['erro_email']);
                    } ?>
                    <div class="product-buyPage">
                        <div class="img_product">
                            <img src=" ../<?= $produto['caminho_img'] ?>">
                        </div>
                        <div class="u">
                            <div class="details">
                                <div class="nome-product">
                                    <input id="nome-pro" type="text" name="nome" value="<?= $produto['nome_products'] ?> "
                                        readonly required>
                                </div>
                                <div class="nome-category">
                                    <input id="cat-pro" type="text" name="categoria" value="<?= $produto['nome_category'] ?>"
                                        readonly required>
                                </div>
                            </div>
                            <div class="dollar">
                                <p> R$ </p>
                                <input type="number" id="input" name="valor" value="<?= $produto['valor_products'] ?>" readonly
                                    required>
                            </div>
                            <div class="quantidade">
                                <i id="less" class='bx bx-minus'></i>
                                <input type="number" id="quantity" name="quantidade" value="<?= $produto['quantity'] ?>">
                                <i id="more" class='bx bx-plus'></i>
                            </div>
                            <div class="buttons">
                                <form action="../cartDelete.php" method="POST" style="display : inline">
                                    <input type="hidden" name="id_produto" value="<?= $produto['id_products'] ?>">
                                    <button type=submit name="deletar_produto">
                                        <i class='bx bx-trash'></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
            <div class="div2">
                <form class="form-div2" action="../vendas.php" method="POST">
                    <div class="vendas">
                        <h1>Vendas</h1>
                        <?php foreach ($produtosCarrinho as $produto): ?>
                            <input type="hidden" name="id_product[]" value="<?= $produto['id_products'] ?>">

                            <input type="hidden" name="quantidade_product[<?= $produto['id_products'] ?>]" id="quantidade_<?= $produto['id_products'] ?>" value="<?= $produto['quantity'] ?>">

                            <div class="quant">
                                <span class="quantidadeVenda" id="quantidadeVenda-<?= $produto['id_products'] ?>"></span>
                                <span> <?= $produto['nome_products'] ?> </span>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <div class="radio-container">
                        <div>
                            <label> Pix </label>
                            <input type="radio" name="input-pix" value="pix" id="pix">
                        </div>
                        <div>
                            <label> Debite</label>
                            <input type="radio" name="input-pix" value="debite" id="cartaoDeDebito">
                        </div>
                        <div class="creditoRelative">
                            <label> Credito </label>
                            <input type="radio" name="input-pix" value="credito" id="cartaoDeCredito">
                            <div id="parcelamento" style="display : none;">
                                <select name="select-parcelamento">
                                    <option disabled selected> Escolha as parcelas </option>
                                    <option value="1">1x sem juros</option>
                                    <option value="2">2x sem juros</option>
                                    <option value="3">3x sem juros</option>
                                    <option value="4">4x com juros</option>
                                    <option value="5">5x com juros</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="total">
                        <h3>Total do Carrinho: </h3>
                        <span id="totalCarrinho">0,00</span>
                        <button type="submit" name="div2"> Finalizar compra </button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="opti">
                <h1> Você não tem produtos no carrinho </h1>
                <button onclick="window.location.href='home_page.php'"> Ver produtos </button>
            </div>
        <?php endif ?>
    </div>
    <script>
        const verificarSeTem = <?php echo count($produtosCarrinho) > 0 ? 'true' : 'false'; ?>;
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

            const inputsCategoria = document.querySelectorAll('#cat-pro');
            inputsCategoria.forEach(input => {
                const categoriaId = input.value;

                if (categorias[categoriaId]) {
                    input.value = categorias[categoriaId];
                }
            });

            const produtos = <?= json_encode($produtosCarrinho) ?>;
            const totalCarrinhoElement = document.getElementById('totalCarrinho');
            const totalQuantidadeVenda = document.getElementById('quantidadeVenda');


            window.addEventListener("DOMContentLoaded", () => {
                const quantidadesSalvas = JSON.parse(localStorage.getItem("quantidadesProdutos")) || {};

                const quantidades = document.querySelectorAll('#quantity');
                quantidades.forEach((input, index) => {
                    const produtoId = produtos[index].id_products;

                    if (quantidadesSalvas[produtoId] !== undefined) {
                        input.value = quantidadesSalvas[produtoId];
                    }
                });
                calcularTotal();
            });

            function calcularTotal() {
                let total = 0;
                quantidadesSalvas = {};

                const quantidades = document.querySelectorAll('#quantity');
                quantidades.forEach((input, index) => {
                    const produtoId = produtos[index].id_products;
                    const quantidade = parseInt(input.value) || 0;
                    const preco = parseFloat(produtos[index].valor_products) || 0;

                    if (isNaN(quantidade) || isNaN(preco)) {
                        return;
                    }

                    total += quantidade * preco;

                    const quantidadeElemento = document.getElementById(`quantidadeVenda-${produtoId}`);
                    if (quantidadeElemento) {
                        quantidadeElemento.textContent = `x${quantidade}`;
                    }

                    quantidadesSalvas[produtoId] = quantidade;

                    const campoQuantidade = document.getElementById(`quantidade_${produtoId}`);
                    if (campoQuantidade) {
                        campoQuantidade.value = quantidade;
                    }
                });
                localStorage.setItem("quantidadesProdutos", JSON.stringify(quantidadesSalvas));

                totalCarrinhoElement.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            }

            moreIcons = document.querySelectorAll("#more");
            lessIcons = document.querySelectorAll("#less");
            quantityInputs = document.querySelectorAll("#quantity");

            moreIcons.forEach((icon, index) => {
                icon.addEventListener("click", () => {
                    let currentQuantity = parseInt(quantityInputs[index].value) || 0;
                    quantityInputs[index].value = currentQuantity + 1;
                    calcularTotal();
                });
            });

            lessIcons.forEach((icon, index) => {
                icon.addEventListener("click", () => {
                    let currentQuantity = parseInt(quantityInputs[index].value) || 0;
                    quantityInputs[index].value = Math.max(0, currentQuantity - 1);
                    calcularTotal();
                });
            });
        }

        document.getElementById('cartaoDeCredito').addEventListener('click', function () {
            document.getElementById('parcelamento').style.display = 'flex';
        });
        document.querySelectorAll('#cartaoDeDebito, #pix').forEach(radio => {
            radio.addEventListener('click', () => {
                document.getElementById('parcelamento').style.display = 'none';
            });
        });
    </script>
</body>

</html>