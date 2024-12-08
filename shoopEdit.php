<?php 
session_start();
require 'bd/connection.php';
if (isset($_POST['editar_loja']));
$nomeLoja = $_POST['nome'];
$telefoneLoja = $_POST['telefone'];
$cnpjLoja = $_POST['cnpj'];

if (empty($nomeLoja) || empty($cnpjLoja)) {
    $_SESSION['restrincao_criarLoja'] = "Preencha todos os campos";
    header('location: views/shoop_page.php');
    exit();
}

if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['restricao_criarLoja'] = "Erro no envio de imagem";
    header('location: views/shoop_page.php');
    exit();
}

try {
    $sqlNomeLoja = "SELECT * FROM  loja WHERE nome_loja= :nome";
    $stmtNomeLoja = $connection->prepare($sqlNomeLoja);
    $stmtNomeLoja->bindParam(':nome', $nomeLoja);
    $stmtNomeLoja->execute();

    if ($stmtNomeLoja->rowCount() > 0) {
        $_SESSION['nomeLojaUsado'] = "O nome da Loja já esta em uso";
        header('location: views/shoop_page.php');
        exit();
    }

    $sqlCnpjLoja = "SELECT * FROM loja WHERE cnpj_loja= :cnpj";
    $stmtCnpjLoja = $connection->prepare($sqlCnpjLoja);
    $stmtCnpjLoja->bindParam(':cnpj', $cnpjLoja);
    $stmtCnpjLoja->execute();

    if ($stmtCnpjLoja->rowCount() > 0) {
        $_SESSION['cnpjUsado'] = "O CNPJ ja esta em uso";
        header('location: views/shoop_page.php');
        exit();
    }

    $sql = "INSERT INTO loja (nome_loja, telefone_loja, cnpj_loja, users_id_users) 
        VALUES (:nome, :telefone, :cnpj, :users_id_users)";
    $stmt = $connection->prepare($sql);

    $stmt->bindParam(':nome', $nomeLoja);
    $stmt->bindParam(':telefone', $telefoneLoja);
    $stmt->bindParam(':cnpj', $cnpjLoja);
    $stmt->bindParam(':users_id_users', $_SESSION['user_id']);
    $stmt->execute();

    $idLoja = $connection->lastInsertId();

    $imagem = $_FILES['imagem'];
    $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);

    $nomeArquivo = hash('sha256', uniqid(mt_rand(), true)) . '.' . $extensao;
    $caminhoUpload = __DIR__ . '/img/img_loja/' . $nomeArquivo;

    if (!move_uploaded_file($imagem['tmp_name'], $caminhoUpload)) {
        $_SESSION['restricao_criarLoja'] = "Erro ao salvar imagem";
        header('location: views/shoop_page.php');
        exit();
    }

    $urlImagem = 'img/img_loja/' . $nomeArquivo;
    $tipoImg = 'loja';

    $sqlImg = "INSERT INTO imagens (tipo_img, caminho_img, lojas_id_loja) 
        VALUEs (:tipo_img, :caminho_img, :lojas_id_loja)";
    $stmtImg = $connection->prepare($sqlImg);
    $stmtImg->bindParam(':tipo_img', $tipoImg);
    $stmtImg->bindParam(':caminho_img', $urlImagem);
    $stmtImg->bindParam(':lojas_id_loja', $idLoja);
    $stmtImg->execute();

    $_SESSION['cadastroLoja_sucesso'] = "Loja criada com sucesso";
    header('location: views/shoop_page.php');
    exit();
} catch (PDOException $e) {
    echo "Erro ao realizar cadastro da loja: " . $e->getMessage();
}
?>