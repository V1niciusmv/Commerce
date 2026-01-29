<?php
session_start();
require 'bd/connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; 

if (isset($_POST['div2'])) {
    $idProduct = $_POST['id_product']; // Array com id dos produtos
    $idQuantidade = $_POST['quantidade_product']; // Array de quantidade, com id dos produtos e valores
    $metodo = $_POST['input-pix']; // Inputs Radio
    $parcelas = null;

    // Se caso o $metodo for igual ao credito, ele verifica se existe se existe algo selecionado no SELECT de parcelamentos
    // Caso exista algo ele pega esse dado e transforma em um inteiro
    if ($metodo == 'credito') {
        if (!isset($_POST['select-parcelamento']) || empty($_POST['select-parcelamento'])) {
            $_SESSION['erro_parcelas'] = "Selecione o número de parcelas";
            header('Location: views/buy_page.php');
            exit();
        }
        $parcelas = (int)$_POST['select-parcelamento'];
    }
}
// Verifica se existe um array de $idProduct, e cria um array(array_fill que cria), cheio de interogação de acordo com a quantidade de produto que existe
// implode junta essas interogações separadas por virgulas e transforma em uma String
if (!empty($idProduct)) {
    $placeholders = implode(',', array_fill(0, count($idProduct), '?'));
}

/* Pegando o email do usuario  */
$sqlEmail = "SELECT email_users FROM users WHERE id_users = :user_id";
$stmtEmail = $connection->prepare($sqlEmail);
$stmtEmail->bindParam(':user_id', $_SESSION['user_id']);
$stmtEmail->execute();
$idEmail = $stmtEmail->fetch(PDO::FETCH_ASSOC);

// pegando o nome do user
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
$sqlProdutos = "SELECT products.id_products, products.nome_products, products.valor_products, products.estoque_products, imagens.caminho_img, loja.id_loja, loja.nome_loja
FROM cart_items
INNER JOIN products ON cart_items.product_id = products.id_products
INNER JOIN imagens ON imagens.produtos_id_products = products.id_products
INNER JOIN loja ON products.loja_id_loja = loja.id_loja
WHERE cart_items.product_id IN ($placeholders) AND cart_items.user_id = ?"; // Passamos o placeHolders que armazenas os ( ?, ?, ?) de acordo 
$stmtProdutos = $connection->prepare($sqlProdutos);                         // com a quantidade de produtos e tambem usamos o '?' para o Id do user
// Como temos um array do id dos produtos enviados pelo $POST, temos que pegar um por um com o foreach no array($idProduto)
// e como é um array indexado, pegamos o index de cada id que tem nele, e o valor de cada id 
// O bindValue so aceita valores de 1 pra cima, 0 ele nao aceita, entao somamos 0 + 1, para o index se torna 1 e começar do 1 em vez do 0
foreach ($idProduct as $index => $productId) {
    $stmtProdutos->bindValue($index + 1, $productId);
}
$stmtProdutos->bindValue(count($idProduct) + 1, $_SESSION['user_id']); //  verifica quantos id tem dentro do array e soma +1 para adicionar o Id do user

$stmtProdutos->execute();
$dadosProdutos = $stmtProdutos->fetchAll(PDO::FETCH_ASSOC);

/* utilizando um foreach para percorrer cada produto e quantidade que tem dentro carrinho*/
// Ele pega o idQuantidade que contem os id de produto com os valores da quantidade e associa o dados[idDoProduto] como chave para acessar o valor
foreach ($dadosProdutos as $dados) {
    $quantidade = $idQuantidade[$dados['id_products']];
    $estoqueAtual = $dados['estoque_products'];

    /* verificando se o estoque é menor que quantidade */
    if($quantidade > $estoqueAtual) {
        $_SESSION['quantidadeMaiorEstoque'] = "Qunatidade maior que o estoque";
        header ('location: views/buy_page.php');
        exit();
    }
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

    // Pegando o ultimo ID inserindo agora na tabela
    $vendaId = $connection->lastInsertId();

    // Multiplica os valores pela quantidade dos produtos que estão no carrinho
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

    // Atualiza o estoque de produto depois que acontece uma compra com o produto expecifico
    // Se novo estoque for maior que zero ele sera 1 caso nao sera 0 
    $sqlNovoEstoque = "UPDATE products SET estoque_products = :novoEstoque, ativo = CASE WHEN :novoEstoque > 0 THEN 1 ELSE 0 
    END WHERE id_products = :idProduto";
    $stmtNovoEstoque = $connection->prepare($sqlNovoEstoque);
    // diminuindo estoque pela quantidade para atualizar o valor em :novoEstoque
        $novoEstoque = $dados['estoque_products'] - $quantidade;
        $stmtNovoEstoque->bindParam(':novoEstoque', $novoEstoque);
        $stmtNovoEstoque->bindParam(':idProduto', $dados['id_products']);
        $stmtNovoEstoque->execute();
    }

    // Deletando o produto que acabou de ser comprado do carrinho de compras 
    $sqlDeleteCart = "DELETE FROM cart_items WHERE cart_id = :idCart AND product_id = :product_id";
    $stmtDeleteCart = $connection->prepare($sqlDeleteCart);
    foreach ($dadosProdutos as $dados) {
        $stmtDeleteCart->bindParam(':idCart', $idCart['id_cart']);
        $stmtDeleteCart->bindParam(':product_id', $dados['id_products']);
        $stmtDeleteCart->execute();
    }

    // Se chegou ate o commit sem dar erros, ele libera pro codigo rodar
    $connection->commit();

    //
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'freecommerce100@gmail.com'; // Seu e-mail
        $mail->Password = 'wfxw uztz qbaj clcg'; // senha 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Remetente e destinatário
        $mail->setFrom('freecommerce100@gmail.com', 'Commerce');
        $mail->addAddress($idEmail['email_users'], $nomeUser['nome_users']);

        // Assunto e corpo do e-mail
        $mail->Subject = "Confirmação de compra - " . $transacaoVendas;

        $mail->isHTML(true);

        // HEADER
        $mensagem = "<html><body>";
        $mensagem .= "<div style='background-color: #E49502; padding: 50px;'></div>"; // Header laranja
        
        $mensagem .= "<table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>";
        $mensagem .= "<tr>";
        $mensagem .= "<td align='center' style='padding: 20px; background-color: white;'>";
        
        $mensagem .= "<h3 style='margin: 0 0 15px 0; font-family: Arial, sans-serif;'>Olá " . $nomeUser['nome_users'] . ", sua compra foi realizada com sucesso.</h3>";
        $mensagem .= "<h4 style='margin: 0 0 20px 0; font-family: Arial, sans-serif;'>Detalhes da sua compra:</h4>";

        foreach ($dadosProdutos as $dados) {
            $caminhoImagem = __DIR__ . '/' . $dados['caminho_img'];
            
            $mensagem .= "<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 400px; margin-bottom: 30px;'>";
            $mensagem .= "<tr><td align='center'>";
            
            if (file_exists($caminhoImagem)) {
                $mail->addEmbeddedImage($caminhoImagem, 'produto_'.$dados['id_products']);
                $mensagem .= "<img src='cid:produto_".$dados['id_products']."' style='width: 200px; height: auto; display: block; margin: 0 auto 15px; border: 1px solid #eee;'>";
            } 

            $mensagem .= "<p style='margin: 5px 0; font-family: Arial, sans-serif; font-weight: bold;'>".$dados['nome_products']."</p>";
            $mensagem .= "<p style='margin: 5px 0 0 0; font-family: Arial, sans-serif;'>(Quantidade: ".$quantidade.") - R$ ".number_format($dados['valor_products'], 2, ',', '.')."</p>";
            
            $mensagem .= "</td></tr>";
            $mensagem .= "</table>";
        }
        
        $mensagem .= "<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 400px;'>";
        $mensagem .= "<tr><td align='center' style='padding-top: 20px; border-top: 1px solid #ddd;'>";
        $mensagem .= "<p style='margin: 0 0 10px 0; font-family: Arial, sans-serif; font-weight: bold;'>Total: R$".number_format($totalCompra, 2, ',', '.')."</p>";
        $mensagem .= "<h3 style='margin: 20px 0 0 0; font-family: Arial, sans-serif;'>Obrigado por comprar em nossa loja!</h3>";
        $mensagem .= "</td></tr>";
        $mensagem .= "</table>";
        
        $mensagem .= "</td></tr>";
        $mensagem .= "</table>";
        
        $mensagem .= "<div style='background-color: #E49502; padding: 50px;'></div>"; // Footer laranja
        $mensagem .= "</body></html>";
    
        $mail->Body = $mensagem;
        $mail->send();
    } catch (Exception $e) {
        $_SESSION['erro_email'] = "Compra finalizada, mas houve um erro no envio do e-mail. Por favor, entre em contato com o suporte.";
        error_log("Erro no envio de e-mail: " . $e->getMessage()); 
    }

    $_SESSION['ultima_compra'] = $vendaId;
    header("location: views/finalyBuy_page.php");
    exit();
} catch (Exception $e) {
    $connection->rollBack();
    die("Erro ao finalizar compra: " . $e->getMessage());
}
?>