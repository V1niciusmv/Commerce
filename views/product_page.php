<?php
session_start();
require '../bd/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('location: register_page.php?action=login');
    exit();
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
                    <?php if (isset($_SESSION['produtoCadastrado']) && isset($produtos) && count($produtos) >= 1) {
                        echo '<p class="sessionGreen">' . $_SESSION['produtoCadastrado'] . '</p>';
                        unset($_SESSION['produtoCadastrado']);
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

            <!-- div que vai mostrar os erros capturados no array de erros do back, exibido pelo Fetch  -->
            <div id="erros-container"></div>

             <!-- $formData vai armazenar a Session-array que contem os dados dos produtos que foram cadastrados -->
            <?php $formData = $_SESSION['form_data'] ?? []; ?>

            <!-- Em cada input do Form de cadastro tem um value exibindo o $formData passando o name dos input para a informação ser mostrada correta -->
             <!-- Caso não exista nenhum resultado na $_SESSION['form_data'], não exibe nada -->
            <form id="form" enctype="multipart/form-data">
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
                            <input type="number" id="input" name="valor" step="1" min="0"
                            onkeypress="return event.charCode >= 48 && event.charCode <= 57" value="<?= htmlspecialchars($formData['valor'] ?? '') ?>">
                            <i id="more" class='bx bx-plus'></i>
                        </div>
                    </div>
                    <div class="div-resultados">
                        <label> Quantidade</label>
                        <input type="number" name="estoque" step="1" min="0"
                        onkeypress="return event.charCode >= 48 && event.charCode <= 57" value="<?= $formData['estoque'] ?? '' ?>" required>
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
                    <?= htmlspecialchars($formData['descricao'] ?? '') ?> </textarea>
                </div>
                <div class="btn">
                    <button type="submit" id="btn-submit"> Adicionar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        //Pegamos o id do icon (+, -) e o input 
        const moreIcon = document.getElementById("more");
        const lessIcon = document.getElementById("less");
        const input = document.getElementById("input");

        moreIcon.addEventListener("click", () => {
            input.value = parseInt(input.value || 0) + 1;
        });

        lessIcon.addEventListener("click", () => {
            input.value = Math.max(0, parseInt(input.value || 0) - 1);
        });

        // pega o ID do input type = file que armazena a imagem, e adiciona um event change
        // verifica se neste arquivo file tem alguma foto, se for maior que zero tem
        //this.file[0].name, pega o nome da 1 foto que começando do indece[0], como so temos 1foto. Se não exibe a mensagem nenhum arquivo....
        document.getElementById("idimg").addEventListener("change", function () {
            let fileName = this.files.length > 0 ? this.files[0].name : "Nenhum arquivo escolhido";
            document.getElementById("file-name").textContent = fileName;
        });

        // Pega o ID do icon e ao clicar ele exibe o form cadastro
        document.getElementById('icon').addEventListener("click", function () {
            const produtos = document.getElementById('produto');
            const cadastro = document.getElementById('form-cadastro');

            produtos.style.display = 'none';
            cadastro.style.display = 'flex';
    });

    // Pega o id do formulario e adiciona um event submit para não enviar o email, passa o async para poder usar o await
    document.getElementById('form').addEventListener('submit', async function(e) {
    e.preventDefault(); // Canceala o envio do formulario
    
    const formData = new FormData(this); // Pega os dados que estão dentro do Form com o 'New FromData'
    const btnSubmit = document.getElementById('btn-submit'); // pega o id do button 
    
    try {
        btnSubmit.disabled = true; //Desabilita o button para não ter mais requisições
        btnSubmit.textContent = 'Enviando...'; // Exibe essa mensagem
        
        const response = await fetch('../product.php', { // Faz um requisição assicrona com o back de productts
            method: 'POST', // define o metodo das informações enviadas
            body: formData, // e passa as informações
            credentials: 'same-origin' // Mantém os cookies da sessão (sessão do user)
        });
        
        // Verifica se a resposta é JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Resposta não é JSON');
        }
        
        // Recebe as respostas em json_enconde porem são enviadas HTTP bruta e elas chegam como string, e converte para json, e armazena em data
        const data = await response.json();
        
        // verifica se tem erros
        if (!data.success) {
            // pega o array de erros e transforma em HTML 
            // ele pega data.erros e transforma com o map pegando cada item da lista do array e passando para erro que é um arrow function, transformando cada erro em um <p>
            const errorsHtml = data.erros.map(erro => 
                `<p class="sessionRed">${erro}</p>`
            ).join('');// Concatena todos em uma unica String '<p>, <p>'
            document.getElementById('erros-container').innerHTML = errorsHtml; // Pega o id da div de exibir o erro, e atualiza para o valor dos resultados
        } else if (data.success) {
            window.location.reload();
        }
    } catch (error) {
        console.error('Erro:', error);
        document.getElementById('erros-container').innerHTML = 
            '<p class="sessionRed">Erro ao processar a resposta do servidor</p>';
    } finally {
        btnSubmit.disabled = false; // Habilita o button
        btnSubmit.textContent = 'Adicionar';
    }
});
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
//Veirifica se existem produtos
        const verificarSeTem = <?= isset($produto) ? 'true' : 'false' ?>;
        if (verificarSeTem) {
            const categorias = { // Um Dicionario de objeto JS com chaves e valores
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
                // Pega as categorias de todos os produtos que estão em exibição
            const inputsCategoria = document.querySelectorAll('#cat-pro');
            inputsCategoria.forEach(input => { // Usa forEach para percorrer todos eles
                const categoriaId = input.value; // Pega o valor de cada um 

                if (categorias[categoriaId]) { // e associa ao objeto JS, se existir e for iguais 
                    input.value = categorias[categoriaId]; // Ele atualiza o valor do campo para o valor respectivo ao do objeto
                }
            });
        }

        // Function para abrir um modal exibindo o produto
        function openModal(idProduto) { // Pega o id do produto passado
            const pro = <?= json_encode($produtos) ?>; // Transforma o array em um json para o js entender
            const product = pro.find(products => products.id_products == idProduto); // usamos o find para percorre o array e achar o 1 valor igual a condição

            document.getElementById('modalProduct').style.display = 'flex';

            // Pega os id dos produtos pelo ID, e atualiza os valores pela variavel product do JS para tornar interativo 
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
        // Quando eu clicar na seta de voltar, ele feicha o modal
        function fecharModal() {
    document.getElementById('modalProduct').style.display = 'none';
}
    </script>
</body>
.
</html>