<?php
session_start();
require '../bd/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('location: register_page.php?action=login');
    exit();
}

$mostrarFormulario = false;
if (
    isset($_SESSION['restricao_criarImgProduto']) || isset($_SESSION['nomeUtilizado']) || isset($_SESSION['restrincao_criarProduto']) ||
    isset($_SESSION['ValorEstoqueGrande'])
) {
    $mostrarFormulario = true;
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
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$sql = "SELECT id_loja FROM loja WHERE users_id_users = :user_id";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$loja = $stmt->fetch(PDO::FETCH_ASSOC);
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
        <div id="produto">
            <?php if (isset($produtos)): ?>
                <div class="itens">
                    <h4> Seus produtos </h4>
                    <?php if (isset($_SESSION['cadastroProduto_sucesso'])) {
                        echo '<p class="sessionGreen">' . $_SESSION['cadastroProduto_sucesso'] . '</p>';
                        unset($_SESSION['cadastroProduto_sucesso']);
                        unset($_SESSION['produto_deletado']);
                    } ?>
                    <?php if (isset($_SESSION['produto_deletado']) && isset($produtos) && count($produtos) >= 1) {
                        echo '<p class="sessionRed">' . $_SESSION['produto_deletado'] . '</p>';
                        unset($_SESSION['produto_deletado']);
                    } ?>
                    <div class="a" id="icon">
                        <i class='bx bx-plus'></i>
                    </div>
                </div>
                <?php include('productView_page.php') ?>
            </div>
        <?php else: ?>
            <div class="semProdutos" id="idCliqueAq">
                <p id="criarLoja" style="display: none;"> Você precisa criar uma loja, para cadastrar os produtos </p>
                <h1> Você ainda não tem produtos</h1>
                <div class="cliqueAq" id="cadastrar"> Cadastrar </div>
            </div>
        <?php endif ?>
        <div class="container-form-cadastro" id="form-cadastro"
            style="display: <?= $mostrarFormulario ? 'true' : 'false'; ?>">
            <h1> Cadastre seu produto </h1>
            <?php if (isset($_SESSION['restricao_criarImgProduto'])) {
                echo '<p class="sessionRed ">' . $_SESSION['restricao_criarImgProduto'] . '</p>';
                unset($_SESSION['restricao_criarImgProduto']);
            } ?>
            <?php if (isset($_SESSION['restrincao_criarProduto'])) {
                echo '<p class="sessionRed ">' . $_SESSION['restrincao_criarProduto'] . '</p>';
                unset($_SESSION['restrincao_criarProduto']);
            } ?>
            <?php if (isset($_SESSION['nomeUtilizado'])) {
                echo '<p class="sessionRed ">' . $_SESSION['nomeUtilizado'] . '</p>';
                unset($_SESSION['nomeUtilizado']);
            } ?>
            <?php if (isset($_SESSION['ValorEstoqueGrande'])) {
                echo '<p class="sessionRed ">' . $_SESSION['ValorEstoqueGrande'] . '</p>';
                unset($_SESSION['ValorEstoqueGrande']);
            } ?>
              <?php if (isset($_SESSION['ValorGrande'])) {
                echo '<p class="sessionRed">' . $_SESSION['ValorGrande'] . '</p>';
                unset($_SESSION['ValorGrande']);
            } ?>
            <form id="form" action="../product.php" method="POST" enctype="multipart/form-data">
                <div class="divs-input">
                    <div class="div-resultados">
                        <label> Nome do produto</label>
                        <input type="text" name="nome" value="<?= $_GET['nome'] ?? '' ?>">
                    </div>
                    <div class="div-resultados">
                        <label>Categoria</label>
                        <select name="categoria">
                            <option disabled selected>Selecionar</option>
                            <option value="1" <?= isset($_POST['categoria']) && $_POST['categoria'] == 1 ? 'selected' : ''; ?>>Eletrônicos</option>
                            <option value="2" <?= isset($_POST['categoria']) && $_POST['categoria'] == 2 ? 'selected' : ''; ?>>Comidas</option>
                            <option value="3" <?= isset($_POST['categoria']) && $_POST['categoria'] == 3 ? 'selected' : ''; ?>>Bebidas</option>
                            <option value="4" <?= isset($_POST['categoria']) && $_POST['categoria'] == 4 ? 'selected' : ''; ?>>Roupas</option>
                            <option value="5" <?= isset($_POST['categoria']) && $_POST['categoria'] == 5 ? 'selected' : ''; ?>>Acessórios</option>
                            <option value="6" <?= isset($_POST['categoria']) && $_POST['categoria'] == 6 ? 'selected' : ''; ?>>Móveis</option>
                            <option value="7" <?= isset($_POST['categoria']) && $_POST['categoria'] == 7 ? 'selected' : ''; ?>>Brinquedos</option>
                            <option value="8" <?= isset($_POST['categoria']) && $_POST['categoria'] == 8 ? 'selected' : ''; ?>>Livros</option>
                            <option value="9" <?= isset($_POST['categoria']) && $_POST['categoria'] == 9 ? 'selected' : ''; ?>>Ferramentas</option>
                            <option value="10" <?= isset($_POST['categoria']) && $_POST['categoria'] == 10 ? 'selected' : ''; ?>>Beleza e Cuidados</option>
                            <option value="11" <?= isset($_POST['categoria']) && $_POST['categoria'] == 11 ? 'selected' : ''; ?>>Esportes</option>
                            <option value="12" <?= isset($_POST['categoria']) && $_POST['categoria'] == 12 ? 'selected' : ''; ?>>Saúde</option>
                            <option value="13" <?= isset($_POST['categoria']) && $_POST['categoria'] == 13 ? 'selected' : ''; ?>>Automotivo</option>
                            <option value="14" <?= isset($_POST['categoria']) && $_POST['categoria'] == 14 ? 'selected' : ''; ?>>Casa e Decoração</option>
                            <option value="15" <?= isset($_POST['categoria']) && $_POST['categoria'] == 15 ? 'selected' : ''; ?>>Jardinagem</option>
                            <option value="16" <?= isset($_POST['categoria']) && $_POST['categoria'] == 16 ? 'selected' : ''; ?>>Tecnologia</option>
                            <option value="17" <?= isset($_POST['categoria']) && $_POST['categoria'] == 17 ? 'selected' : ''; ?>>Higiene</option>
                            <option value="18" <?= isset($_POST['categoria']) && $_POST['categoria'] == 18 ? 'selected' : ''; ?>>Informática</option>
                        </select>
                    </div>
                </div>
                <div class="divs-input">
                    <div class="div-resultados">
                        <label> Valor</label>
                        <div class="position">
                            <i id="less" class='bx bx-minus'></i>
                            <input type="number" id="input" name="valor" value="<?= $_GET['valor'] ?? '' ?>">
                            <i id="more" class='bx bx-plus'></i>
                        </div>
                    </div>
                    <div class="div-resultados">
                        <label> Quantidade</label>
                        <input type="number" name="estoque" value="<?= $_GET['estoque'] ?? '' ?>" required>
                    </div>
                </div>
                <div class="div-img">
                    <input type="file" id="idimg" class="hidden" name="imagem" accept="image/*">
                    <label for="idimg">
                        <i class='bx bx-camera'></i>
                        Adicione uma imagem
                    </label>
                    <span id="file-name" class="file-name">Nenhum arquivo escolhido</span>
                </div>
                <div class="div-text">
                    <label for="descricao"> Descrição </label>
                    <textarea id="descricao" name="descricao" rows="5" cols="40"
                        placeholder="Digite a descrição do produto">
                            <?= isset($_POST['descricao']) ? htmlspecialchars($_POST['descricao']) : '' ?>
                        </textarea>
                </div>
                <div class="btn">
                    <button type="submit" for="form" name="adicionar_produto"> Adicionar</button>
                </div>
            </form>
        </div>
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

        document.getElementById("idimg").addEventListener("change", function () {
            let fileName = this.files.length > 0 ? this.files[0].name : "Nenhum arquivo escolhido";
            document.getElementById("file-name").textContent = fileName;
        });

        const mostrarFormulario = <?= $mostrarFormulario ? 'true' : 'false' ?>;
        if (mostrarFormulario) {
            const produtos = document.getElementById('produto');
            const cadastro = document.getElementById('form-cadastro');

            produtos.style.display = 'none';
            cadastro.style.display = 'flex';
        }

        const verificar = <?php echo !isset($produtos) ? 'false' : 'true'; ?>;
        const verificarLoja = <?php echo isset($loja['id_loja']) ? 'true' : 'false'; ?>;

        if (!verificar) {
            document.getElementById('cadastrar').addEventListener("click", function () {
                if (verificarLoja) {
                    const semCadastro = document.getElementById('idCliqueAq');
                    const cadastro = document.getElementById('form-cadastro');

                    semCadastro.style.display = 'none';
                    cadastro.style.display = 'flex';
                } else {
                    document.getElementById('criarLoja').style.display = 'flex';
                }
            });
        }

        const verificarSeTem = <?php echo isset($produtos) ? 'true' : 'false'; ?>;
        if (verificarSeTem) {
            document.getElementById('icon').addEventListener("click", function () {
                const produtos = document.getElementById('produto');
                const cadastro = document.getElementById('form-cadastro');

                produtos.style.display = 'none';
                cadastro.style.display = 'flex';
            });
        }

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
        }

        function openModal(idProduto) {
            const pro = <?= json_encode($produtos) ?>;
            const product = pro.find(products => products.id_products == idProduto);

            document.getElementById('modalProduct').style.display = 'flex';

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
    </script>
</body>

</html>