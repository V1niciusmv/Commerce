<?php
require_once '../bd/connection.php';

if (isset($_SESSION['idProduto'])) { // Verifica sem alguma session de idDoProduto (vinda do ViewPage)
    $query = $_SESSION['idProduto']; // Adiciona esse id na query
    unset($_SESSION['idProduto']); // quebra a session depois que a pagina atualizar
} else {
    $query = '';
}

// Pega a url depois do dominiu 
$page = basename($_SERVER['REQUEST_URI']);

// Seleciona todos os produtos. imagem, nome da loja, e categoria do produto
$sql = "SELECT products.*, imagens.caminho_img, loja.nome_loja, category.nome_category
        FROM products
        LEFT JOIN imagens ON products.id_products = imagens.produtos_id_products 
        LEFT JOIN loja ON products.loja_id_loja= loja.id_loja
        LEFT JOIN category ON products.category_id_category = category.id_category";

// Se a page for home, e existir a query com o id do produto, ela exibe apenas o produto selecionado, e se ele estiver com o estoque ativo
if ($page == 'home_page.php') {
    if ($query) {
        $sql .= " WHERE products.id_products = :query AND products.ativo = 1";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':query', $query);
        
    } else { // Se não ele exibe todos os produtos do estoque ativo
        $sql .= " WHERE products.ativo = 1";
        $stmt = $connection->prepare($sql);
    } // Verifica se esta na productpage e tem a query para mostrar apenas o product expecifico passado e com o estoque ativo
} elseif ($page === 'product_page.php') {
    if ($query) {
        $sql .= " WHERE products.users_id_users = :user_id AND products.id_products = :query";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':query', $query);
    } else { // Se não mostra todos os produtos do user
        $sql .= " WHERE products.users_id_users = :user_id";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
    }
}

$stmt->execute();

if ($stmt->rowCount() > 0) {
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $produtos = [];
}
?>

<div class="show-products">
    <?php if ($page == 'home_page.php'): ?> <!-- Se for em home vai mostrar os product sem icon de edit e excluir -->
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
    <?php else: ?> <!--  Se for em productPage vai mostrar os icon editar e excluir -->
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
        <?php if ($page == 'home_page.php'): ?> <!--  Se for em home vai mostrar os button de back, comprar, adicionarCarrinho -->
            <div class="modalProduct">
                <div class="div1-modal">
                    <div class="img_product-modal">
                        <img id="modal-img">
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
                        <div class="ceta" onclick="fecharModal()"> <!-- Chama a função -->
                            <i class='bx bx-arrow-back'></i>
                        </div>
                        <div class="button-link-modal">
                            <form class="button-link-form" action="../cart.php" method="POST">
                                <input type="hidden" name="id_produto" id="modal-id-product-comprar">
                                <button type="button" onclick="verificarUser(event)"> Comprar </button> <!-- Chama a função event que verifica se o user esta logado -->
                            </form>

                            <div class="ceta">
                                <form id="id-form-cart-add" action="../cartAddBuy.php" method="POST">
                                    <input type="hidden" name="id_produto" id="modal-id-product-adicionar">
                                    <button type="button" onclick="verificarUser(event)"><i class='bx bx-cart-add'></i></button> <!-- Chama a função event que verifica se o user esta logado -->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="modalProduct">
                <div class="div1-modal">
                    <div class="img_product-modal">
                        <img id="modal-img">
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
                        <i class='bx bx-arrow-back'></i> <!-- Chama a função para fechar o modal -->
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>