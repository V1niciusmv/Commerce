<?php
session_start();
require 'bd/connection.php';

if (isset($_POST['editar_loja'])) {
    $idLoja = $_POST['id_loja']; 
    $nomeLoja = $_POST['nome'];
    $telefoneLoja = $_POST['telefone'];
    $cnpjLoja = $_POST['cnpj'];

    if (empty($nomeLoja) || empty($cnpjLoja) || empty($telefoneLoja))  {
        $_SESSION['restrincao_criarLoja'] = "Preencha todos os campos";
        header('location: views/shoopEdit_page.php');
        exit();
    }

     $sqlLojaAtual = "SELECT nome_loja, telefone_loja, cnpj_loja FROM loja WHERE id_loja = :id_loja";
     $stmtLojaAtual = $connection->prepare($sqlLojaAtual);
     $stmtLojaAtual->bindParam(':id_loja', $idLoja);
     $stmtLojaAtual->execute();
     $lojaAtual = $stmtLojaAtual->fetch(PDO::FETCH_ASSOC);
 
     if ($lojaAtual['nome_loja'] == $nomeLoja && $lojaAtual['telefone_loja'] == $telefoneLoja && $lojaAtual['cnpj_loja'] == $cnpjLoja 
     && (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK)){
         $_SESSION['loja_nao_editada'] = "Nenhuma alteração detectada.";
         header('location: views/shoopEdit_page.php');
         exit();
     }
 
    try {
        $sqlNomeLoja = "SELECT COUNT(*) FROM loja WHERE nome_loja = :nome AND id_loja != :id_loja";
        $stmtNomeLoja = $connection->prepare($sqlNomeLoja);
        $stmtNomeLoja->bindParam(':nome', $nomeLoja);
        $stmtNomeLoja->bindParam(':id_loja', $idLoja);
        $stmtNomeLoja->execute();

        $resultNome = $stmtNomeLoja->fetchColumn();

        if ($resultNome > 0) {
            $_SESSION['nomeLojaUsado'] = "O nome da Loja já esta em uso";
            header("location: views/shoopEdit_page.php?telefone=$telefoneLoja&nome=$nomeLoja&cnpj=$cnpjLoja");
            exit();
        }

        $sqlTelefoneLoja = "SELECT COUNT(*) FROM loja LEFT JOIN users ON users.id_users = loja.users_id_users
    WHERE (loja.telefone_loja = :telefone AND id_loja != :id_loja) OR (users.telefone_users = :telefone AND users.id_users != :users)";
        $stmtTelefoneLoja = $connection->prepare($sqlTelefoneLoja);
        $stmtTelefoneLoja->bindParam(':telefone', $telefoneLoja);
        $stmtTelefoneLoja->bindParam(':id_loja', $idLoja);
        $stmtTelefoneLoja->bindParam(':users', $_SESSION['user_id']);
        $stmtTelefoneLoja->execute();

        $resultTelefone = $stmtTelefoneLoja->fetchColumn();
        if ($resultTelefone > 0) {
            $_SESSION['telefoneUsado'] = "O numero já esta em uso";
            header("location: views/shoopEdit_page.php?telefone=$telefoneLoja&nome=$nomeLoja&cnpj=$cnpjLoja");
            exit();
        }

        $sqlCnpjLoja = "SELECT COUNT(*) FROM loja WHERE cnpj_loja = :cnpj AND id_loja != :id_loja";
        $stmtCnpjLoja = $connection->prepare($sqlCnpjLoja);
        $stmtCnpjLoja->bindParam(':cnpj', $cnpjLoja);
        $stmtCnpjLoja->bindParam(':id_loja', $idLoja);
        $stmtCnpjLoja->execute();

        $resultCnpj = $stmtCnpjLoja->fetchColumn();

        if ($resultCnpj > 0) {
            $_SESSION['cnpjUsado'] = "O CNPJ ja esta em uso";
            header("location: views/shoopEdit_page.php?telefone=$telefoneLoja&nome=$nomeLoja&cnpj=$cnpjLoja");
            exit();
        }

        $sql = "UPDATE loja SET nome_loja = :nome, telefone_loja = :telefone, cnpj_loja = :cnpj, users_id_users = :users WHERE id_loja = :id_loja";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':nome', $nomeLoja);
        $stmt->bindParam(':telefone', $telefoneLoja);
        $stmt->bindParam(':cnpj', $cnpjLoja);
        $stmt->bindParam(':users', $_SESSION['user_id']);
        $stmt->bindParam(':id_loja', $idLoja);
        $stmt->execute();

        $sqlImgAtual = "SELECT caminho_img FROM imagens WHERE lojas_id_loja = :id_loja";
        $stmtImgAtual = $connection->prepare($sqlImgAtual);
        $stmtImgAtual->bindParam(':id_loja', $idLoja);
        $stmtImgAtual->execute();
        $imgAtual = $stmtImgAtual->fetchColumn();

        if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
            $urlImagem = $imgAtual;
        }else {
        ($imgAtual && file_exists(__DIR__ . "/../" . $imgAtual));
            unlink(__DIR__ . "/../" . $imgAtual);
        

        $imagem = $_FILES['imagem'];
        $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);          

        $nomeArquivo = hash('sha256', uniqid(mt_rand(), true)) . '.' . $extensao;
        $caminhoUpload = __DIR__ . '/img/img_loja/' . $nomeArquivo;

        if (!move_uploaded_file($imagem['tmp_name'], $caminhoUpload)) {
            $_SESSION['restrincao_criarLoja'] = "Erro ao salvar imagem";
            header('location: views/shoopEdit_page.php');
            exit();
        }

        $urlImagem = 'img/img_loja/' . $nomeArquivo;
    }
        $tipoImg = 'loja';
        
        $sqlImg = "UPDATE imagens SET caminho_img = :caminho_img WHERE lojas_id_loja = :id_loja AND tipo_img = :tipo_img";
        $stmtImg = $connection->prepare($sqlImg);
        $stmtImg->bindParam(':tipo_img', $tipoImg);
        $stmtImg->bindParam(':caminho_img', $urlImagem);
        $stmtImg->bindParam(':id_loja', $idLoja);
        $stmtImg->execute();

        $_SESSION['editLoja_sucesso'] = "Loja atualizada com sucesso";
        header('location: views/shoopEdit_page.php');
        exit();
    } catch (PDOException $e) {
        echo "Erro ao realizar edição da loja: " . $e->getMessage();
    }
}