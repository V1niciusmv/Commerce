<?php 
session_start();
require 'bd/connection.php';

header('Content-Type: application/json');

if(isset($_POST['editar_usuario'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $senha = $_POST['senha'];
    $cpf = $_POST['cpf'];
}
    $errosEdit = [];

if (empty($nome) || empty($email) || empty($telefone) || empty($endereco) || empty($senha) || empty($cpf)) {
    $errosEdit[] = "Preencha todos os campos";
 
}
    if (strlen($cpf) < 14 ) {
        $errosEdit[] = 'Esta faltando numeros no CPF';
       
    }
    if (strlen($telefone) < 15) { 
        $errosEdit[] = 'Telefone incompleto (formato: (00) 00000-0000)';
    }
    try {
        $sql = "SELECT * FROM users WHERE id_users = :idUser";
    $stmt= $connection->prepare($sql);
    $stmt->bindParam(':idUser', $_SESSION['user_id']);
    $stmt->execute();
    $resultUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if (
        $resultUser['nome_users'] === $nome &&
        $resultUser['email_users'] === $email &&
        $resultUser['telefone_users'] === $telefone &&
        $resultUser['endereco_users'] === $endereco &&
        $resultUser['senha_users'] === $senha &&
        $resultUser['cpf_users'] === $cpf) {
            $errosEdit[]= "Você não alterou nenhum valor"; 
        }

    if(!empty($errosEdit)) {
        echo json_encode(['success' => false, 'erros' => $errosEdit]);
        exit();
    }

$sqlEmail = "SELECT COUNT(*) FROM users WHERE email_users = :email AND id_users != :user";
$stmtEmail = $connection->prepare($sqlEmail);
$stmtEmail->bindParam(':email', $email);
$stmtEmail->bindParam(':user', $_SESSION['user_id']);
$stmtEmail->execute();

$veriEmail = $stmtEmail->fetchColumn();
if ($veriEmail > 0){
    echo json_encode(['success' => false, 'erros' => ["O email ja esta em uso"]]);
    exit();
}

$sqlTelefone = "SELECT COUNT(*) FROM users WHERE telefone_users = :telefone AND id_users != :user";
$stmtTelefone = $connection->prepare($sqlTelefone);
$stmtTelefone->bindParam(':telefone', $telefone);
$stmtTelefone->bindParam(':user', $_SESSION['user_id']);
$stmtTelefone->execute();

$veriTelefone = $stmtTelefone->fetchColumn();
if ($veriTelefone > 0){
    echo json_encode(['success' => false, 'erros' => ["O telefone ja esta em uso"]]);
    exit();
}

$sqlSenha = "SELECT COUNT(*) FROM users WHERE senha_users = :senha AND id_users != :user";
$stmtSenha = $connection->prepare($sqlSenha);
$stmtSenha->bindParam(':senha', $senha);
$stmtSenha->bindParam(':user', $_SESSION['user_id']);
$stmtSenha->execute();

$veriSenha = $stmtSenha->fetchColumn();
if ($veriSenha > 0) {
    echo json_encode(['success' => false, 'erros' => ["A senha já esta em uso"]]);
    exit();
}

$sqlCpf = "SELECT COUNT(*) FROM users WHERE cpf_users = :cpf AND id_users != :user";
$stmtCpf = $connection->prepare($sqlCpf);
$stmtCpf->bindParam(':cpf', $cpf);
$stmtCpf->bindParam(':user', $_SESSION['user_id']);
$stmtCpf->execute();

$veriCpf= $stmtCpf->fetchColumn();
if ($veriCpf> 0) {
    echo json_encode(['success' => false, 'erros' => ["O CPF já esta em uso"]]);
    exit();
}

$sql = "UPDATE users SET nome_users = :user, email_users = :email, telefone_users = :telefone, endereco_users = :endereco, senha_users = :senha,
cpf_users = :cpf WHERE id_users = :id";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':user', $nome);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':telefone', $telefone);
$stmt->bindParam(':endereco', $endereco);
$stmt->bindParam(':senha', $senha);
$stmt->bindParam(':cpf', $cpf);
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$_SESSION['user_atualizado'] = "Usuario atualizado com sucesso";
echo json_encode(['success' => true]);
exit();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'erros' => [$e->getMessage()]]);
        exit();
    }