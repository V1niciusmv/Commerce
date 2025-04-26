
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
                        <div class="product-select">
                        <input type="checkbox" name="selected_products[]" value="<?= $produto['id_products'] ?>"
                        class="product-checkbox" id="checkbox_<?= $produto['id_products'] ?>" checked>
                        </div>
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
                                <input type="number" id="quantity" name="quantidade" value="<?= $produto['quantity'] ?>"
                                    data-product-id="<?= $produto['id_products'] ?>"
                                    data-estoque="<?= $produto['estoque_products'] ?>"
                                    max="<?= $produto['estoque_products'] ?>">
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
                            <!-- inputs Hidden -->
                            <input type="hidden" name="id_product[]" value="<?= $produto['id_products'] ?>"
                                id="hidden_id_<?= $produto['id_products'] ?>">

                            <input type="hidden" name="quantidade_product[<?= $produto['id_products'] ?>]"
                                id="hidden_quant_<?= $produto['id_products'] ?>" value="<?= $produto['quantity'] ?>">

                            <div class="quant" id="container-produto-<?= $produto['id_products'] ?>">
                                <span class="quantidadeVenda" id="quantidadeVenda-<?= $produto['id_products'] ?>"></span>
                                <span id="idSpanQuant"> <?= $produto['nome_products'] ?> </span>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <div class="erroParcelaRelative">
                        <span class="erroParcela" id="erro-parcelas" style="display:none"> Falta quantas vezes você quer
                            parcelar </span>
                    </div>
                    <div class="radio-container">
                        <div>
                            <label> Pix </label>
                            <input type="radio" name="input-pix" value="pix" id="pix" required>
                            <div class="divpix" id="div-pix" style="display : none">
                                <span class="spanpix" id="numeroPix"> 81 979014191</span>
                            </div>
                        </div>
                        <div>
                            <label> Debite</label>
                            <input type="radio" name="input-pix" value="debito" id="cartaoDeDebito" required>
                        </div>
                        <div class="creditoRelative">
                            <label> Credito </label>
                            <input type="radio" name="input-pix" value="credito" id="cartaoDeCredito" required>
                            <div id="parcelamento" style="display : none;">
                                <select name="select-parcelamento" required>
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
                        <button id="finalizarCompraButton" type="submit" name="div2"> Finalizar compra </button>
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

        window.addEventListener("DOMContentLoaded", () => {
            const carrinhoSalvo = JSON.parse(localStorage.getItem("carrinhoAtual")) || {};
            const quantidades = document.querySelectorAll('#quantity');
            const checkboxes = document.querySelectorAll('.product-checkbox');

            quantidades.forEach((input, index) => {
                const produtoId = produtos[index].id_products;
                if (carrinhoSalvo[produtoId]) {
                    input.value = carrinhoSalvo[produtoId].quantidade || 0;
                }
            });

            checkboxes.forEach(checkbox => {
                const produtoId = checkbox.value;
                if (carrinhoSalvo[produtoId]) {
                    checkbox.checked = carrinhoSalvo[produtoId].selecionado;
                }
            });

            atualizarProdutosSelecionados();
        });

        function atualizarProdutosSelecionados() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    
    checkboxes.forEach(checkbox => {
        const productId = checkbox.value;
        const hiddenId = document.getElementById(`hidden_id_${productId}`);
        const hiddenQuant = document.getElementById(`hidden_quant_${productId}`);
        const containerProduto = document.getElementById(`container-produto-${productId}`);
        const quantityInput = document.querySelector(`#quantity[data-product-id="${productId}"]`);

        // Atualiza sempre, independente do checkbox
        if (quantityInput && hiddenQuant) {
            hiddenQuant.value = quantityInput.value;
        }

        if (checkbox.checked) {
            if (hiddenId) hiddenId.disabled = false;
            if (containerProduto) containerProduto.style.display = 'flex';
        } else {
            if (hiddenId) hiddenId.disabled = true;
            if (containerProduto) containerProduto.style.display = 'none';
        }
    });
    
    calcularTotal();
}

        function calcularTotal() {
            let total = 0;
            let temItensValidos = false;
            const produtosSalvos = {};

            const checkboxes = document.querySelectorAll('.product-checkbox');

            checkboxes.forEach(checkbox => {
                const productId = checkbox.value;
                const quantityInput = document.querySelector(`#quantity[data-product-id="${productId}"]`);
                const produto = produtos.find(p => p.id_products == productId);

                if (quantityInput && produto) {
                    const quantidade = parseInt(quantityInput.value) || 0;
                    const preco = parseFloat(produto.valor_products) || 0;

                    if (checkbox.checked && quantidade > 0) {
                        total += quantidade * preco;
                        temItensValidos = true;
                    }

                    // Salvar estado no objeto
                    produtosSalvos[productId] = {
                        quantidade: quantidade,
                        selecionado: checkbox.checked
                    };

                    const quantidadeElemento = document.getElementById(`quantidadeVenda-${productId}`);
                    if (quantidadeElemento) quantidadeElemento.textContent = `x${quantidade}`;
                }
            });

            localStorage.setItem("carrinhoAtual", JSON.stringify(produtosSalvos));
            totalCarrinhoElement.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            document.getElementById('finalizarCompraButton').disabled = !temItensValidos;
        }

        // Atualizar ao clicar nos checkboxes
        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', atualizarProdutosSelecionados);
        });

        // Event listeners dos botões + e -
        const moreIcons = document.querySelectorAll("#more");
        const lessIcons = document.querySelectorAll("#less");
        const quantityInputs = document.querySelectorAll("#quantity");

        moreIcons.forEach((icon, index) => {
            icon.addEventListener("click", () => {
                let currentQuantity = parseInt(quantityInputs[index].value) || 0;
                let maxEstoque = parseInt(quantityInputs[index].dataset.estoque) || 0;

                if (currentQuantity < maxEstoque) {
                    quantityInputs[index].value = currentQuantity + 1;
                    atualizarProdutosSelecionados();
                }
            });
        });

        lessIcons.forEach((icon, index) => {
            icon.addEventListener("click", () => {
                let currentQuantity = parseInt(quantityInputs[index].value) || 0;
                quantityInputs[index].value = Math.max(1, currentQuantity - 1);
                atualizarProdutosSelecionados();
            });
        });

        // Novo: capturar digitação manual no input de quantidade
        quantityInputs.forEach(input => {
            input.addEventListener("input", calcularTotal);
        });

        // Seção de métodos de pagamento
        document.getElementById('cartaoDeCredito').addEventListener('click', function () {
            document.getElementById('parcelamento').style.display = 'flex';
        });

        document.querySelectorAll('#cartaoDeDebito, #pix').forEach(radio => {
            radio.addEventListener('click', () => {
                document.getElementById('parcelamento').style.display = 'none';
                erroParcelas.style.display = 'none';
            });
        });

        document.getElementById('pix').addEventListener('click', function () {
            document.getElementById('div-pix').style.display = 'flex';
        });

        document.querySelectorAll('#cartaoDeDebito, #cartaoDeCredito').forEach(Element => {
            Element.addEventListener('click', () => {
                document.getElementById('div-pix').style.display = 'none';
            });
        });

        const erroParcelas = document.getElementById('erro-parcelas');
        document.querySelector('.form-div2').addEventListener('submit', function (e) {
            const creditoSelecionado = document.getElementById('cartaoDeCredito').checked;
            const selectParcelas = document.querySelector('select[name="select-parcelamento"]');
            const selectedProducts = document.querySelectorAll('.product-checkbox:checked');

            localStorage.removeItem('carrinhoAtual');

            if (selectedProducts.length === 0) {
                e.preventDefault();
                alert('Selecione pelo menos um produto para finalizar a compra!');
            }

            if (creditoSelecionado && selectParcelas.selectedIndex <= 0) {
                e.preventDefault();
                erroParcelas.style.display = 'flex';
                selectParcelas.focus();
            } else {
                erroParcelas.style.display = 'none';
            }

            let temItens = false;
            document.querySelectorAll('#quantity').forEach(input => {
                if (parseInt(input.value) > 0) temItens = true;
            });

            if (!temItens) {
                e.preventDefault();
                alert('Adicione pelo menos um produto para finalizar a compra!');
            }
        });
    }
</script>

</body>

</html>