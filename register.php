<?php 
session_start();
require 'bd/connection.php';

if(isset($_POST['cadastrar_usuario'])) {
    $_SESSION['register_data'] = $_POST;

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $senha = $_POST['senha'];
    $cpf = $_POST['cpf'];

    try {
        $sqlEmail = "SELECT * FROM users WHERE email_users = :email";
        $stmtEmail = $connection->prepare($sqlEmail);
        $stmtEmail->bindParam(':email', $email);
        $stmtEmail->execute();

        if($stmtEmail->rowCount() > 0){
            $_SESSION['email_usado'] = 'O E-mail ja esta em uso';
            header('location: views/register_page.php?action=register');
            exit();
        }

        if (strlen($cpf) < 15 ) {
            $_SESSION['cpf_menor'] = 'Esta faltando numeros no CPF';
            header ('location: views/register_page.php?action=register ');
            exit();
        }

        $sqlCpf = "SELECT * FROM users WHERE cpf_users = :cpf";
        $stmtCpf = $connection->prepare($sqlCpf);
        $stmtCpf->bindParam(':cpf', $cpf);
        $stmtCpf->execute();

        if($stmtCpf->rowCount() > 0){
            $_SESSION['cpf_usado'] = 'O CPF ja esta em uso';
            header('location: views/register_page.php?action=register');
            exit();
        }

        if (strlen($telefone) < 15) { 
            $_SESSION['telefone_menor'] = 'Telefone incompleto (formato: (00) 00000-0000)';
            header('location: views/register_page.php?action=register');
            exit();
        }


        $sqlTelefone = "SELECT * FROM users WHERE telefone_users = :telefone";
        $stmtTelefone = $connection->prepare($sqlTelefone);
        $stmtTelefone->bindParam(':telefone', $telefone);
        $stmtTelefone->execute();

        if($stmtTelefone->rowCount() > 0){
            $_SESSION['telefone_usado'] = "O telefone ja esta em uso";
            header('location: views/register_page.php?action=register');
            exit();
        }

        $sql = "INSERT INTO users (nome_users, email_users, telefone_users, endereco_users, senha_users, cpf_users) 
        VALUES (:nome, :email, :telefone, :endereco, :senha, :cpf)";
        $stmt = $connection->prepare($sql);

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();

        unset($_SESSION['form_data']);

        $_SESSION['mensagem_sucesso_cadastro'] = "Cadastro feito, agora faça seu login";
        header('location: views/register_page.php');
        exit();
    }catch (PDOException $e) {
        echo "Erro ao cadastrar usuario: " . $e->getMessage();
    }
}
?>