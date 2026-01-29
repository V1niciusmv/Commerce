<?php
session_start();
require 'bd/connection.php';

if (isset($_POST['cadastrar_loja'])) {

$_SESSION['register_loja'] = $_POST;

if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['restricao_criarLoja'] = "Envie uma imagem válida";
    header("location: views/shoop_page.php");
    exit();
}

$imagem = $_FILES['imagem'];

// valida extensão
$extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
$extPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($extensao, $extPermitidas)) {
    $_SESSION['restricao_criarLoja'] = "Formato de imagem inválido";
    header("location: views/shoop_page.php");
    exit();
}

// cria pasta se não existir
$diretorio = __DIR__ . '/img/img_loja/';
if (!is_dir($diretorio)) {
    mkdir($diretorio, 0755, true);
}

// gera nome único
$nomeArquivo = uniqid('loja_', true) . '.' . $extensao;
$caminhoFinal = $diretorio . $nomeArquivo;

// move o arquivo
if (!move_uploaded_file($imagem['tmp_name'], $caminhoFinal)) {
    $_SESSION['restricao_criarLoja'] = "Erro ao salvar a imagem";
    header("location: views/shoop_page.php");
    exit();
}

// caminho para salvar no banco
$urlImagem = 'img/img_loja/' . $nomeArquivo;


$nomeLoja = $_POST['nome'];
$telefoneLoja = $_POST['telefone'];
$cnpjLoja = $_POST['cnpj'];

if ((empty($nomeLoja) && empty($telefoneLoja) && empty($cnpjLoja)) && (!isset($_FILES['imagem']) 
|| $_FILES['imagem']['error'] !== UPLOAD_ERR_OK)) {
    $_SESSION['restrincao_criarLoja'] = "Preencha todos os campos";
    header("location: views/shoop_page.php");
    exit();
}

try {
    $sqlNomeLoja = "SELECT COUNT(*) FROM loja WHERE nome_loja= :nome";
    $stmtNomeLoja = $connection->prepare($sqlNomeLoja);
    $stmtNomeLoja->bindParam(':nome', $nomeLoja);
    $stmtNomeLoja->execute();

    $resultNome = $stmtNomeLoja->fetchColumn();

    
    if ($resultNome > 0) {
        $_SESSION['nomeLojaUsado'] = "O nome da Loja já esta em uso";
        header("location: views/shoop_page.php");
        exit();
    }

    $sqlTelefoneLoja = "SELECT COUNT(*) FROM users LEFT JOIN loja ON loja.users_id_users = users.id_users
    WHERE loja.telefone_loja = :telefone OR (users.telefone_users = :telefone AND users.id_users != :users)";
    $stmtTelefoneLoja = $connection->prepare($sqlTelefoneLoja);
    $stmtTelefoneLoja->bindParam(':telefone', $telefoneLoja);
    $stmtTelefoneLoja->bindParam(':users', $_SESSION['user_id']);
    $stmtTelefoneLoja->execute();

    $resultTelefone = $stmtTelefoneLoja->fetchColumn();
   
    if ($resultTelefone > 0) {
        $_SESSION['telefoneUsado'] = "O numero já esta em uso";
        header("location: views/shoop_page.php");
        exit();
    } 
    
    $sqlCnpjLoja = "SELECT COUNT(*) FROM loja WHERE cnpj_loja= :cnpj";
    $stmtCnpjLoja = $connection->prepare($sqlCnpjLoja);
    $stmtCnpjLoja->bindParam(':cnpj', $cnpjLoja);
    $stmtCnpjLoja->execute();

    $resultCnpj = $stmtCnpjLoja->fetchColumn();

    if ($resultCnpj > 0) {
        $_SESSION['cnpjUsado'] = "O CNPJ ja esta em uso";
        header("location: views/shoop_page.php");
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

    if (!$stmt->rowCount()) {
    die('Erro: loja não foi criada');
}

$idLoja = $connection->lastInsertId();

if (!$idLoja) {
    die('Erro: ID da loja inválido');
}

    $imagem = $_FILES['imagem'];
    
    $urlImagem = 'img/img_loja/' . $nomeArquivo;

$sqlImg = "INSERT INTO imagens (tipo_img, caminho_img, lojas_id_loja)
           VALUES ('loja', :caminho, :loja)";

$stmtImg = $connection->prepare($sqlImg);
$stmtImg->bindValue(':caminho', $urlImagem);
$stmtImg->bindValue(':loja', $idLoja, PDO::PARAM_INT);
if (!$stmtImg->execute()) {
    print_r($stmtImg->errorInfo());
    exit;
}
    unset($_SESSION['register_loja']);
    unset($_SESSION['register_loja_files']);
    $_SESSION['cadastroLoja_sucesso'] = "Loja criada com sucesso";
    header('location: views/shoop_page.php');
    exit();
} catch (PDOException $e) {
    echo "Erro ao realizar cadastro da loja: " . $e->getMessage();
}
}
?>