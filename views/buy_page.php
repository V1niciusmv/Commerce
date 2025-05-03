
<?php
session_start();
require '../bd/connection.php';
if (!isset($_SESSION['user_id'])) {
    header('location: register_page.php?action=login');
    exit();
}

// Pega o id do carrinho de compras
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

// Seleciona os produtos, imagens, nome de loja, categoria e quantidade de cart_items(itens do carrinho), aonde o id do carrinho (unico) esteja no cart_items
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
                        <!-- CheckBox para o usuario selecionar se quer esse produto ou não, ele começa selecionado -->
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
                            <!-- Na quantidade criamos um data passando o id do product (para sabermos qual quantidade estamos mexendo)
                             data estoque, e usamos o Max para o usuario nunca aumentar a mais que a quantidade do estoque -->
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
                            <!-- inputs Hidden  que vao ser enviados para o back, contendo os produtos que vao ser enviados e as quantidades de cada produto-->
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
                            <span class=erroParcela id="semProdutos"> Você precisa ter algum produto selecionado para finalizar a compra</span>
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

            // Converte o arrat de produtosCarrinho para Json
        const produtos = <?= json_encode($produtosCarrinho) ?>;

        function calcularTotal() {
            let total = 0; // Total da compra começa com 0
            let temItensValidos = false; // Flag para verificar se há itens válidos (selecionados e com quantidade > 0)
            const produtosSalvos = {}; // Objeto que sera salvo no localstorage
            const totalCarrinhoElement = document.getElementById('totalCarrinho'); // pega o Id do total da compra

            const checkboxes = document.querySelectorAll('.product-checkbox'); // Pega todos os checkBox 

            checkboxes.forEach(checkbox => { // Verifica cada checkbox
                const productId = checkbox.value; // Pega o valor de cada checkBox
                const quantityInput = document.querySelector(`#quantity[data-product-id="${productId}"]`); // Pega do input de quantidade da div1, 
                // passando o id do produto do checkBox atual 
                const produto = produtos.find(p => p.id_products == productId); // Usamos o find para achar o primeiro elemento que é igual a condição
                // Então usamos find no produtos que é o array de produtos do banco de dados, que esta em json_encode pro js entender, e verifica se existe 
                //o id do produto com o valor do checkBox, se existir ele retorna o valor, se não undfenied

                if (quantityInput && produto) {
                    const quantidade = parseInt(quantityInput.value) || 1; // Pega o valor da quantidade da div1 e transforma em um inteiro
                    const preco = parseFloat(produto.valor_products) || 1; // Pega o valor do produto que esta no carrinho selecionado

                    if (checkbox.checked) { // Se o checkBox estiver marcado e a quantidade estiver maior que 0
                        total += quantidade * preco; // Quantidade vezes o preço do produto
                        temItensValidos = true; // Define como true, pos passou nos requisitos de ser valido
                    }

                    // Se existir o span na div2 de quantidadeVenda ele atualiza exibindo o valor da quantidade da div1 
                    const quantidadeElemento = document.getElementById(`quantidadeVenda-${productId}`);
                    if (quantidadeElemento) {
                        quantidadeElemento.textContent = `x${quantidade}`;
                    }

                    // Salvar estado no objeto para o localStorage
                    produtosSalvos[productId] = { // Para cada produto ele salva no dicionario de objeto sua quantidade e se esta selecionado ou não 
                        quantidade: quantidade,
                        selecionado: checkbox.checked
                    };

                }
            });
            // Salvamos no localStorage adicionando o nome como carrinho atual e passando como jsonStringfy o dicionario de objtos
            // Pos o localStorage so entende string
            localStorage.setItem("carrinhoAtual", JSON.stringify(produtosSalvos)); 
            totalCarrinhoElement.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`; // Atualiza o valor total, define 2 casa no maximo e tira os ponto e coloca virgulas
            document.getElementById('finalizarCompraButton').disabled = !temItensValidos; // Atualiza o button a ser clicavel dependendo se tem produtos ou não
        }

        window.addEventListener("DOMContentLoaded", () => { // Utiliza isso para o js so rodar quando todo o html estiver presente no codigo 
            const carrinhoSalvo = JSON.parse(localStorage.getItem("carrinhoAtual")) || {}; // Esta pegando o valor salvo do carrinho do localstorage, 
            // usando o json.parse para traduzir de String para js

            const quantidades = document.querySelectorAll('#quantity'); // Pega todas as quantidades
            const checkboxes = document.querySelectorAll('.product-checkbox'); // Pega todos os checkBox 

            quantidades.forEach((input, index) => { // Para cada quantidade ele passa o input para pegar 
                const produtoId = produtos[index].id_products; // Pega o id de produtos correspondente a o input de quantidade, usando o index
                if (carrinhoSalvo[produtoId]) { // verifica se existe esse produto no localstorage
                    input.value = carrinhoSalvo[produtoId].quantidade || 1; // se existe ele captura a quantidade que tinha, atualiza, se não define 0
                }
            });

            checkboxes.forEach(checkbox => { // Faz o loop por todos os checkbox
                const produtoId = checkbox.value; // Pega o valor do value, que é o id do product
                if (carrinhoSalvo[produtoId]) { // Se existir no localstorage, ele atualiza o checkbox 
                    checkbox.checked = carrinhoSalvo[produtoId].selecionado;
                }
            });

            atualizarProdutosSelecionados(); // Chama a function
        });

        function atualizarProdutosSelecionados() { // Chamada logo apos o resultado da div1 ser recuperada pelo localstorage, para atualizar a div2 
    const checkboxes = document.querySelectorAll('.product-checkbox'); // Pegamos o id de todos os checkBox 
    
    checkboxes.forEach(checkbox => { // Verificamos cada um
        const productId = checkbox.value; // Pegamos o valor do checkBox que contem o ID de cada produto
        const hiddenId = document.getElementById(`hidden_id_${productId}`); // input hidden que armazena o id do produto
        const hiddenQuant = document.getElementById(`hidden_quant_${productId}`); // input hidden que armazena o id de quantidade 
        const containerProduto = document.getElementById(`container-produto-${productId}`); // Div que exibe o resultado dos produtos selecionados da div2

        if (checkbox.checked) { // Se o chekBox estiver marcado 
            if (hiddenId) hiddenId.disabled = false; // habilidade o input hidden com o id do produto
            if (containerProduto) containerProduto.style.display = 'flex'; // e a div de resultados selecionados fica flex
        } else {
            if (hiddenId) hiddenId.disabled = true; // Desabilita o envio do id do produto
            if (containerProduto) containerProduto.style.display = 'none'; // e a div de resultados fica none
        }

        if (!checkbox.checked && erroParcelas) { // Se clicar no checkbox parar tirar o produto e tiver o erro na tela, apague
            erroParcelas.style.display = 'none';
        }

        if (checkbox.checked && erroSemProduto) {
            erroSemProduto.style.display = ' none';
        } 
    });
    
    calcularTotal(); //  Chama o calcular total para calcular se tiver diferente e salvar no localstorage
}
        // Atualizar ao clicar nos checkboxes
        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', atualizarProdutosSelecionados);
        });

        // Event listeners dos botões + e -
        const moreIcons = document.querySelectorAll("#more"); // icon de mais
        const lessIcons = document.querySelectorAll("#less"); // icon de menos
        const quantityInputs = document.querySelectorAll("#quantity"); // inputs da div1 quantidades

        moreIcons.forEach((icon, index) => { // Percorre todos os Icon de mais
            icon.addEventListener("click", () => { // adiciona um event de click   
                let currentQuantity = parseInt(quantityInputs[index].value) || 1; // Ele pega o valor da quantidade do input baseado no index do more, e transforma em um inteiro
                let maxEstoque = parseInt(quantityInputs[index].dataset.estoque) || 1; //  Acessa o valor do estoque do produto baseado no index do more

                if (currentQuantity < maxEstoque) { // enquanto a quantidade for menos que o estoque ele pode aumentar
                    quantityInputs[index].value = currentQuantity + 1;
                    calcularTotal(); // Chama a function para atualizar o valor do input
                }
            });
        });

        lessIcons.forEach((icon, index) => { // Percorre todos os icon de menos
            icon.addEventListener("click", () => { // adiciona um event de click
                let currentQuantity = parseInt(quantityInputs[index].value) || 0; // Pega o input de quantidade baseado no index do icon less
                quantityInputs[index].value = Math.max(1, currentQuantity - 1); // atualiza o input diminuindo, o maximo que pode diminuir é 1
                calcularTotal(); // Atualiza os valores na div2 e salva no localstorge os valores alterados
            });
        });

        // Quando clicar no radio de credito mostra o select de parcelamento 
        document.getElementById('cartaoDeCredito').addEventListener('click', function () {
            document.getElementById('parcelamento').style.display = 'flex';
        });

        // Quando clicar no radio de Debito e pix, select de parcelamento desaparece
        document.querySelectorAll('#cartaoDeDebito, #pix').forEach(radio => {
            radio.addEventListener('click', () => {
                document.getElementById('parcelamento').style.display = 'none';
                erroParcelas.style.display = 'none';
            });
        });

        // Quando clicar no radio de Pix mostra a div com a chave pix
        document.getElementById('pix').addEventListener('click', function () {
            document.getElementById('div-pix').style.display = 'flex';
        });
        // Quando clicar no radio de credito, e debito desaparece a div com a chave pix
        document.querySelectorAll('#cartaoDeDebito, #cartaoDeCredito').forEach(Element => {
            Element.addEventListener('click', () => {
                document.getElementById('div-pix').style.display = 'none';
            });
        });

        const erroParcelas = document.getElementById('erro-parcelas'); // Div aonde vai mostrar o erro de parcela nao selecionada
        const erroSemProduto = document.getElementById('semProdutos');

        document.querySelector('.form-div2').addEventListener('submit', function (e) { // Event no form para cancelar o envio caso o parcelamento não esteja selecionado
            const creditoSelecionado = document.getElementById('cartaoDeCredito').checked; // Pega o ID radio se estiver selecionado
            const selectParcelas = document.querySelector('select[name="select-parcelamento"]'); // Pega a lista do select de numreos de parcelas
            const selectedProducts = document.querySelectorAll('.product-checkbox:checked'); // Pega todos os produtos que estao marcados como checked

            localStorage.removeItem('carrinhoAtual'); // 

               document.getElementById('finalizarCompraButton').addEventListener('click', function () {
                   if (selectedProducts.length === 0) { // Se nenhum produto estiver selecionado ele cancela o envio dos dados
                    erroSemProduto.style.display = 'flex';
                       e.preventDefault();
                   }
                });

            if (creditoSelecionado && selectParcelas.selectedIndex <= 0) { // Se existir cartao de credito e as parcelas estiverem igual ou menor que 0 (nao existir nada selecionado)
                e.preventDefault(); // Cancela o envio do form tambem
                erroParcelas.style.display = 'flex'; // Exibe a mensangem de erro
                selectParcelas.focus(); // Foca no campo de parcelas
            } else {
                erroParcelas.style.display = 'none'; // Se não fica none
            }
        });
    }
</script>

</body>

</html>