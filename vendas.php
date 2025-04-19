<?php
session_start();
require 'bd/connection.php';

if (isset($_POST['div2'])) {
    $idProduct = $_POST['id_product'];
    $idQuantidade = $_POST['quantidade_product'];
    $metodo = $_POST['input-pix'];
    $parcelas = null;

    if ($metodo == 'credito') {
        if (!isset($_POST['select-parcelamento']) || empty($_POST['select-parcelamento'])) {
            $_SESSION['erro_parcelas'] = "Selecione o número de parcelas";
            header('Location: views/buy_page.php');
            exit();
        }
        $parcelas = (int)$_POST['select-parcelamento'];
    }
}

if (!empty($idProduct)) {
    $placeholders = implode(',', array_fill(0, count($idProduct), '?'));
}

/* Pegando o email do usuario  */
$sqlEmail = "SELECT email_users FROM users WHERE id_users = :user_id";
$stmtEmail = $connection->prepare($sqlEmail);
$stmtEmail->bindParam(':user_id', $_SESSION['user_id']);
$stmtEmail->execute();
$idEmail = $stmtEmail->fetch(PDO::FETCH_ASSOC);

$sqlNomeUser = "SELECT nome_users FROM users WHERE id_users = :user_id";
$stmtNomeUser = $connection->prepare($sqlNomeUser);
$stmtNomeUser->bindParam(':user_id', $_SESSION['user_id']);
$stmtNomeUser->execute();
$nomeUser = $stmtNomeUser->fetch(PDO::FETCH_ASSOC);

/* pegando o id do carrinho de compras unico para cada usuario  */
$sqlIdCart = "SELECT id_cart FROM cart WHERE user_id_cart = :id_user";
$stmtIdCart = $connection->prepare($sqlIdCart);
$stmtIdCart->bindParam(':id_user', $_SESSION['user_id']);
$stmtIdCart->execute();
$idCart = $stmtIdCart->fetch(PDO::FETCH_ASSOC);

/* Pegando os dados dos produtos,loja,itensNoCarrinho */
$sqlProdutos = "SELECT products.id_products, products.nome_products, products.valor_products, products.estoque_products, loja.id_loja, loja.nome_loja
FROM cart_items
INNER JOIN products ON cart_items.product_id = products.id_products
INNER JOIN loja ON products.loja_id_loja = loja.id_loja
WHERE cart_items.product_id IN ($placeholders)";
$stmtProdutos = $connection->prepare($sqlProdutos);
foreach ($idProduct as $index => $productId) {
    $stmtProdutos->bindValue($index + 1, $productId);
}

$stmtProdutos->execute();
$dadosProdutos = $stmtProdutos->fetchAll(PDO::FETCH_ASSOC);

/* utilizando um foreach para percorrer cada produto e quantidade que tem dentro carrinho*/
foreach ($dadosProdutos as $dados) {
    $quantidade = $idQuantidade[$dados['id_products']];
    $estoqueAtual = $dados['estoque_products'];

    /* verificando se o estoque é menor que quantidade */

}

/* Iniciando uma transação para caso der erro, ela não continue com o SQL */
$connection->beginTransaction();
try {
    /* Criando um identificador unico para a transação*/
    $transacaoVendas = uniqid("Produto_");

    /* Inserindo na tabela de vendas os dados como a transacao,data,Id do carrinho do usuario */
    $sqlVenda = "INSERT INTO vendas (transacao_vendas, data_vendas, cart_id_cart, metodo_pagamento, parcelas) VALUES (:transacao, NOW(), :cart_id, :metodo, :parcelas)";
    $stmtVenda = $connection->prepare($sqlVenda);
    $stmtVenda->bindParam(':transacao', $transacaoVendas);
    $stmtVenda->bindParam(':cart_id', $idCart['id_cart']);
    $stmtVenda->bindParam(':metodo', $metodo);
    $stmtVenda->bindParam(':parcelas', $parcelas);
    $stmtVenda->execute();

    $vendaId = $connection->lastInsertId();

    $totalCompra = 0;
    foreach ($dadosProdutos as $dados) {
    $quantidade = $idQuantidade[$dados['id_products']];
    $totalCompra += $dados['valor_products'] * $quantidade;

    /* Inserindo na tabela vendas_has_products o id da venda e produtos*/
    $sqlItemVenda = "INSERT INTO vendas_has_products (vendas_id_vendas, products_id_products, quantity, preco_unitario) VALUES  (:vendas, :products_id, :quantidade, :preco)";
    $stmtItemVenda = $connection->prepare($sqlItemVenda);
        $stmtItemVenda->bindParam(':vendas', $vendaId);
        $stmtItemVenda->bindParam(':products_id', $dados['id_products']);
        $stmtItemVenda->bindParam(':quantidade', $quantidade);
        $stmtItemVenda->bindParam(':preco', $totalCompra);
        $stmtItemVenda->execute();

    $sqlNovoEstoque = "UPDATE products SET estoque_products = :novoEstoque, ativo = CASE WHEN :novoEstoque > 0 THEN 1 ELSE 0 
    END WHERE id_products = :idProduto";
    $stmtNovoEstoque = $connection->prepare($sqlNovoEstoque);
        $novoEstoque = $dados['estoque_products'] - $idQuantidade[$dados['id_products']];
        $stmtNovoEstoque->bindParam(':novoEstoque', $novoEstoque);
        $stmtNovoEstoque->bindParam(':idProduto', $dados['id_products']);
        $stmtNovoEstoque->execute();
    }

    $sqlDeleteCart = "DELETE FROM cart_items WHERE cart_id = :idCart AND product_id = :product_id";
    $stmtDeleteCart = $connection->prepare($sqlDeleteCart);
    foreach ($dadosProdutos as $dados) {
        $stmtDeleteCart->bindParam(':idCart', $idCart['id_cart']);
        $stmtDeleteCart->bindParam(':product_id', $dados['id_products']);
        $stmtDeleteCart->execute();
    }
    $connection->commit();

    $assunto = "Confirmação de compra - " . $transacaoVendas;
    $mensagem = "Olá!" . $nomeUser['nome_users'] . "sua compra foi realizada com sucesso.\n";
    $mensagem .= "Detalhes da sua compra:\n";

    foreach ($dadosProdutos as $dados) {
        $mensagem .= "-" . $dados['nome_products'] . "(Quantidade:" . $dados['quantity'] . ") - R$ " . $dados['valor_products'] . "\n";
    }

    $mensagem .= "\n Total: R$" . number_format($totalCompra, 2, ',', '.') . "\n";
    $mensagem .= "Obrigado por comprar em nossa loja!";

    $headers = "From: loja@commerce.com\r\n";
    $headers .= "Reply-To: suporte@commerce.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";    

    if (mail($idEmail['email_users'], $assunto, $mensagem, $headers)) {
        $_SESSION['compra_finalizada'] = "Compra finalizada com sucesso";
    }else {
        $_SESSION['erro_email'] = "Compra finalizada, erro no envio do email"; 
    }

    $_SESSION['ultima_compra'] = $vendaId;
    header("location: views/finalyBuy_page.php");
    exit();
} catch (Exception $e) {
    $connection->rollBack();
    die("Erro ao finalizar compra: " . $e->getMessage());
}
?>