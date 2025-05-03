<?php 
session_start();
require 'bd/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['form_data'] = $_POST;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $_SESSION['form_files']['imagem_nome'] = $_FILES['imagem']['name'];
    }

    $erros = [];
    $nomeProduto = $_SESSION['form_data']['nome'];
    $categoriaProduto = $_SESSION['form_data']['categoria'];
    $valorProduto = $_SESSION['form_data']['valor'];
    $estoqueProduto = $_SESSION['form_data']['estoque'];
    $descricaoProduto = $_SESSION['form_data']['descricao'];
    $userId = $_SESSION['user_id'];

    
    if (empty($nomeProduto)) {
        $erros[] = "Nome do produto é obrigatório";
    }
    
    if (empty($categoriaProduto)) {
        $erros[] = "Categoria é obrigatória";
    }
    
    if (empty($valorProduto) || !is_numeric($valorProduto) || $valorProduto <= 0) {
        $erros[] = "Valor obrigatorio";
    } elseif ($valorProduto > 10000) {
        $erros[] = 'É permitido apenas 10.000 no valor';
    }
    
    if (empty($estoqueProduto) || !is_numeric($estoqueProduto) || $estoqueProduto <= 0) {
        $erros[] = "Estoque inválido";
    } elseif ($estoqueProduto > 1000) {
        $erros[] = 'É permitido apenas 1.000 no estoque';
    }
    
    if (empty($descricaoProduto)) {
        $erros[] = "Descrição é obrigatória";
    }
 
    if (empty($_SESSION['form_files']['imagem_nome'])) {
        $erros[] = "Imagem é obrigatória";
    }

    if (!empty($erros)) {
        echo json_encode(['success' => false, 'erros' => $erros]);
        exit();
    }

try {
    $sql = "SELECT COUNT(*) FROM products WHERE nome_products = :nome_produto AND users_id_users = :user_id";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':nome_produto', $nomeProduto);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    $verificarNome = $stmt->fetchColumn();
    if($verificarNome > 0) {
        echo json_encode(['success' => false, 'erros' => ['Você já tem um produto com esse nome']]);
        exit();
    }

    $sqlLoja = "SELECT id_loja FROM loja WHERE users_id_users = :id_user";
    $stmtLoja = $connection->prepare($sqlLoja);
    $stmtLoja->bindParam(':id_user', $_SESSION['user_id']);
    $stmtLoja->execute();
    $loja = $stmtLoja->fetch(PDO::FETCH_ASSOC);
    $id_loja = $loja['id_loja'];


    $sqlCategory = "INSERT INTO category (nome_category) VALUES (:nome_categoria)";
    $stmtCategory = $connection->prepare($sqlCategory);
    $stmtCategory->bindParam(':nome_categoria', $categoriaProduto);
    $stmtCategory->execute();

    $idCategoria = $connection->lastInsertId();

    $sql = "INSERT INTO products (nome_products, valor_products, descricao_products, estoque_products, users_id_users, loja_id_loja, category_id_category) 
    VALUES (:nome, :valor, :descricao, :estoque, :id_user, :id_loja, :id_category)";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':nome', $nomeProduto);
    $stmt->bindParam(':valor', $valorProduto);
    $stmt->bindParam(':estoque', $estoqueProduto);
    $stmt->bindParam(':descricao', $descricaoProduto);
    $stmt->bindParam(':id_user', $_SESSION['user_id']);
    $stmt->bindParam(':id_loja', $id_loja);
    $stmt->bindParam(':id_category',$idCategoria);
    $stmt->execute();

    $idProduto = $connection->lastInsertId();

    $imagem = $_FILES['imagem'];
    $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);

    $nomeArquivo = hash('sha256', uniqid(mt_rand(), true)) . '.' . $extensao;
    $caminhoUpload = __DIR__ . '/img/img_produto/' . $nomeArquivo;

    if (!move_uploaded_file($imagem['tmp_name'], $caminhoUpload)) {
        echo json_encode(['success' => false, 'erros' =>['Erro ao salvar imagem']]);
        exit();
    
    }

    $urlImagem = 'img/img_produto/' . $nomeArquivo;
    $tipoImg = 'produto';

    $sqlImg = "INSERT INTO imagens (tipo_img, caminho_img, produtos_id_products) 
        VALUES (:tipo_img, :caminho_img, :produtos_id_products)";
    $stmtImg = $connection->prepare($sqlImg);
    $stmtImg->bindParam(':tipo_img', $tipoImg);
    $stmtImg->bindParam(':caminho_img', $urlImagem);
    $stmtImg->bindParam(':produtos_id_products', $idProduto);
    $stmtImg->execute();
    
    unset($_SESSION['form_data']);
    unset($_SESSION['form_files']);
    unset($_SESSION['erros']);

    $_SESSION['produtoCadastrado'] = "Produto cadastrado com sucesso";
    echo json_encode(['success' => true]);
    exit();
} catch (PDOException $e) {
    echo "Erro ao realizar cadastro do produto: " . $e->getMessage();
}
}
?>