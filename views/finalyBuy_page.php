<?php
session_start();
require '../bd/connection.php';

if (isset($_POST['user_id']) && isset($_POST['ultima_compra'])) {
    header('location: ../register_page.php');
    exit();
}

$vendaId = $_SESSION['ultima_compra'];

$sql = "SELECT products_id_products, products.nome_products, products.valor_products, users.email_users, imagens.caminho_img, loja.nome_loja, vendas.*, vendas_has_products.quantity, vendas_has_products.preco_unitario
FROM vendas_has_products
INNER JOIN products ON vendas_has_products.products_id_products = products.id_products
INNER JOIN users ON products.users_id_users = users.id_users
INNER JOIN imagens ON products.id_products = imagens.produtos_id_products
INNER JOIN loja ON products.loja_id_loja = loja.id_loja
INNER JOIN vendas ON vendas_has_products.vendas_id_vendas = vendas.id_vendas
WHERE vendas_has_products.vendas_id_vendas = :idVendas";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':idVendas', $vendaId);
$stmt->execute();
$dadosUltimaCompra = $stmt->fetchAll(PDO::FETCH_ASSOC);

$infoUnica = $dadosUltimaCompra[0];

$sql = "SELECT email_users FROM users WHERE id_users =:idUser";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':idUser', $_SESSION['user_id']);
$stmt->execute();
$email = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/finalBuy.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>Compra finalizada </title>
</head>

<body>
    <?php include('header_page.php') ?>

    <div class="container-finaly">
        <div class="text">
            <h1> Compra finalizada </h1>
            <h3> Muito obrigado por comprar conosco <?= $_SESSION['user_nome'] ?> </h3>
        </div>

        <div class="info-compra">
            <p> Informações da compra: </p>
        </div>

        <div>
            <div class="info-first">
                <p> <span class="intro"> Loja: </span> </p>
                <span class="info-first-span"> <?= $infoUnica['nome_loja'] ?> </span>
            </div>
            <div class="info-first">
                <p> <span class="intro"> Email enviado para: </p>
                <span class="info-first-span"><?= $email['email_users'] ?> </span>
            </div>
            <div class="info-first">
                <p> <span class="intro">Data e horário da compra:</span></p>
                   <span class="info-first-span"><?= date('d/m/Y H:i', strtotime($infoUnica['data_vendas'])) ?> </span> 
            </div>
            <div class="info-first">
            <p><span class="intro"> Metodo de pagamento: </span></p>
            <span class="n-lojaproduto-span"><?= $infoUnica['metodo_pagamento'] ?> </span>
            </div>
            <div class="info-first">
                <p> <span class="intro"> Codigo: </span> </p>
                <span class="n-lojaproduto-span"><?= $infoUnica['transacao_vendas'] ?></span>
            </div>
        </div>

        <?php foreach ($dadosUltimaCompra as $dadosUltimaCompraa): ?>
            <div class="container-info">
                <div class="img-info">
                    <img src=" ../<?= $dadosUltimaCompraa['caminho_img'] ?>">
                </div>
                <div class="group">
                <div class="n-lojaproduto">
                        <p> <span class="intro"> Produto: </span> </p>
                      <span class="n-lojaproduto-span"><?= $dadosUltimaCompraa['nome_products'] ?> </span>
                    </div>
                    <div class="n-lojaproduto">
                        <p> <span class="intro">Valor do produto: </span> </p> 
                        <span class="n-lojaproduto-span"><?= $dadosUltimaCompraa['valor_products'] ?> </span>
                    </div>
                    <div class="n-lojaproduto">
                        <p> <span class="intro"> Quantidade: </span> </p>
                        <span class="n-lojaproduto-span"><?= $dadosUltimaCompraa['quantity'] ?> </span>
                    </div>
                    <div class="n-lojaproduto">
                    <p> <span class="intro">Valor total:</span> </p> 
                    <span class="n-lojaproduto-span"><?= $dadosUltimaCompraa['preco_unitario'] ?> </span>
                    </div>
                </div>
                </div>
            <?php endforeach ?>
        </div>

    </div>


</body>

</html>