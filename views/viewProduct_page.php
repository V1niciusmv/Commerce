<?php
require_once '../bd/connection.php';

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
    <link rel="stylesheet" href="/css/viewproduct.css">
    <title>produtos</title>
</head>

<body>
    <div class="product">
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
                <input type="number" id="input" name="valor" value="<?= $produto['valor_products'] ?>" readonly
                    required>
                <i class='bx bx-dollar'></i>
            </div>
            <div class="box">
                <input type="nunmber" name="estoque" value="<?= $produto['estoque_products'] ?>" readonly required>
                <i class='bx bx-box'></i>
            </div>
        </div>
        <div class="descricao">
            <textarea id="descricao" name="descricao" rows="3" cols="20" placeholder="Digite a descrição do produto"
                readonly> <?= $produto['descricao_products'] ?> </textarea>
        </div>
    </div>

<script>
     const action = <?= isset($produto) ?>;

const moreIcon = document.getElementById("more");
const lessIcon = document.getElementById("less");
const input = document.getElementById("input");

if (action) {
    moreIcon.style.display = "none";
    lessIcon.style.display = "none";
} else {
    moreIcon.addEventListener("click", () => {
        input.value = parseInt(input.value || 0) + 1;
    });

    lessIcon.addEventListener("click", () => {
        input.value = Math.max(0, parseInt(input.value || 0) - 1);
    });
}

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
    const inputCategoria = document.getElementById('categoria');
    const categoriaId = inputCategoria.value;

    if (categorias[categoriaId]) {
        inputCategoria.value = categorias[categoriaId];
    } 
</script>
</body>
</html>