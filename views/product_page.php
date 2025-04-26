<?php
session_start();
require '../bd/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('location: register_page.php?action=login');
    exit();
}

// Quando o user sai da pagina de cadastro que tem o paramentro da URL, a session perde os dados armazenados dentro dela
if (!isset($_GET['show_form'])) {
    unset($_SESSION['form_data']);
    unset($_SESSION['form_files']);
}

// Start como false 
$mostrarFormulario = false; 

// Verifica se existe, GET,SESSION....caso exista transforma o $mostrarFormulario para TRUE
if ( isset($_GET['show_form']) || isset($_SESSION['restricao_criarImgProduto']) || isset($_SESSION['nomeUtilizado']) || 
    isset($_SESSION['restrincao_criarProduto']) || isset($_SESSION['ValorEstoqueGrande'])) {
    $mostrarFormulario = true;
}

// Seleciona todos os produtos e imagens, categorias dos 'produtos'
$sql = "SELECT products.*, imagens.caminho_img, category.id_category, category.nome_category
FROM products
LEFT JOIN imagens ON products.id_products = produtos_id_products 
LEFT JOIN category ON products.category_id_category = id_category 
WHERE products.users_id_users = :user_id";

$stmt = $connection->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Pega o ID da loja, para verificar se existe uma loja cadastrada
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
            <!-- Se existir produtos exibe eles -->
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
                    <!-- Icon para clicar e ir para o cadastro de produto -->
                    <div class="a" id="icon">
                        <i class='bx bx-plus'></i>
                    </div>
                </div>
                <!-- Include dos produtos -->
                <?php include('productView_page.php') ?>
            </div>
        <!-- Se não existir produtos -->
        <?php else: ?>
            <div class="semProdutos" id="idCliqueAq">
                <p id="criarLoja" style="display: none;"> Você precisa criar uma loja, para cadastrar os produtos </p>
                <h1> Você ainda não tem produtos</h1>
                <div class="cliqueAq" id="cadastrar"> Cadastrar </div>
            </div>
        <?php endif ?>
        <!-- Container do formulario de cadastro, começa com display : none, oculto -->
        <div class="container-form-cadastro" id="form-cadastro">
            <h1> Cadastre seu produto </h1>

            <!-- Uma Session-array que armazena todos os erros -->
              <?php if (isset($_SESSION['erros'])): ?>
                <?php foreach ($_SESSION['erros'] as $erros): ?>
               <p class="sessionRed"> <?= $erros ?> </p>
                <?php endforeach; ?>
              <?php  unset($_SESSION['erros']); ?>
             <?php endif; ?>

             <!-- $formData vai armazenar a Session-array que contem os dados dos produtos que foram cadastrados -->
            <?php $formData = $_SESSION['form_data'] ?? []; ?>

            <!-- Em cada input do Form de cadastro tem um value exibindo o $formData passando o name dos input para a informação ser mostrada correta -->
             <!-- Caso não exista nenhum resultado na $_SESSION['form_data'], não exibe nada -->
            <form id="form" action="../product.php" method="POST" enctype="multipart/form-data">
                <div class="divs-input">
                    <div class="div-resultados">
                        <label> Nome do produto</label>
                        <input type="text" name="nome"  value="<?= htmlspecialchars($formData['nome'] ?? '') ?>">
                    </div>
                    <div class="div-resultados">
                        <label>Categoria</label>
                        <select name="categoria">
                            <option disabled selected>Selecionar</option>
                            <!-- cria um laço que percorre do 1 ao 18 -->
                             <!-- o resultado desse $i, é usado no option, com um value, e a cada interação do for o $i vai mudando de valor -->
                            <!-- e a cada mudança de valor, ele cria um value novo para cada option -->
                             <!--  Ainda dentro do for tem um array-associativo, e um [$i] acessando a chave expecifica dentro do array -->
                            <?php for ($i = 1; $i <= 18; $i++): ?>
                                <option value="<?= $i ?>" <?= ($formData['categoria'] ?? '') == $i ? 'selected' : '' ?>>
                                <?= [
                                    1 => 'Eletrônicos',
                                    2 => 'Comidas',
                                    3 => 'Bebidas',
                                    4 => 'Roupas',
                                    5 => 'Acessórios',
                                    6 => 'Móveis',
                                    7 => 'Brinquedos',
                                    8 => 'Livros',
                                    9 => 'Ferramentas',
                                    10 => 'Beleza e Cuidados',
                                    11 => 'Esportes',
                                    12 => 'Saúde',
                                    13 => 'Automotivo',
                                    14 => 'Casa e Decoração',
                                    15 => 'Jardinagem',
                                    16 => 'Tecnologia',
                                    17 => 'Higiene',
                                    18 => 'Informática'
                                ][$i] ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    </div>
                </div>
                <div class="divs-input">
                    <div class="div-resultados">
                        <label> Valor</label>
                        <div class="position">
                            <i id="less" class='bx bx-minus'></i>
                            <input type="number" id="input" name="valor" value="<?= $formData['valor'] ?? '' ?>">
                            <i id="more" class='bx bx-plus'></i>
                        </div>
                    </div>
                    <div class="div-resultados">
                        <label> Quantidade</label>
                        <input type="number" name="estoque" value="<?= $formData['estoque'] ?? '' ?>" required>
                    </div>
                </div>
                <div class="div-img">
                    <input type="file" id="idimg" class="hidden" name="imagem" accept="image/*">
                    <label for="idimg">
                        <i class='bx bx-camera'></i>
                        Adicione uma imagem
                    </label>
                    <span id="file-name" class="file-name">
                    <?= $_SESSION['form_files']['imagem_nome'] ?? 'Nenhum arquivo escolhido' ?>
                    </span>
                </div>
                <div class="div-text">
                    <label for="descricao"> Descrição </label>
                    <textarea id="descricao" name="descricao" rows="5" cols="40" placeholder="Digite a descrição do produto">
                    <?= htmlspecialchars($formData['descricao'] ?? '') ?>
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

        //  Pega o ID do icon e adiciona um evento de click, redirecionando para a pagina atual com um parametro 
        // ele pega o caminho da pagina atual passando o '?show_form=true' de paramentro, e redireciona para essa nova URL
        const verificarSeTem = <?php echo isset($produtos) ? 'true' : 'false'; ?>;
        if (verificarSeTem) {
            document.getElementById('icon').addEventListener("click", function () {
               window.location.href = window.location.pathname + '?show_form=true';
            });
        }

        // Quando é adicionado um paramentro a URL atual, a variavel $mostrarFormulario fica true, ocultando os produtos e exibindo o formCadastro
        const mostrarFormulario = <?= $mostrarFormulario ? 'true' : 'false' ?>;
        if (mostrarFormulario) {
            const produtos = document.getElementById('produto');
            const cadastro = document.getElementById('form-cadastro');

            produtos.style.display = 'none';
            cadastro.style.display = 'flex';
        }

        // Caso não exista Loja, ira exibir uma mensagem para criar, caso exista exibe o form de cadastro e ocultando a tela de 'sem produtos, cadastresse' 
        // So pode cadastrar produto se existir uma loja
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