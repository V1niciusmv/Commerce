<?php 
session_start();
require 'bd/connection.php';

if(isset($_POST['login_usuario'])){

    $_SESSION['login_data'] = $_POST;

    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if(empty($email) || empty($senha)){
        $_SESSION['sessao_vazia'] = "Preencha todos os campos obrigatorios";
        header('location: views/register_page.php?action=login');
        exit();
    }

    try {
        $sql = "SELECT * FROM users WHERE email_users = :email AND senha_users = :senha";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['id_users'];
            $_SESSION['user_nome'] = $user['nome_users'];
            unset($_SESSION['login_data']);
            header('location: views/home_page.php');
            exit();
        }else{
            $_SESSION['erro_login'] = 'Usuario ou senha incorretos';
            header('location: views/register_page.php?action=login');
            exit();
        }
    }catch (PDOException $e) {
        echo "Erro ao realizar login: " . $e->getMessage();
    }
}
?>