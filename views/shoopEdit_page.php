<?php
session_start();
require '../bd/connection.php';


if (!isset($_SESSION['user_id'])) {
    header('location: register_page.php?action=login');
    exit();
}
$sql = "SELECT loja.*, imagens.caminho_img FROM loja LEFT JOIN imagens ON loja.id_loja = lojas_id_loja
        WHERE users_id_users = :user_id";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $loja = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>Loja</title>
</head>

<body>
    <?php include('header_page.php'); ?>
    <div class="container-shooop-full">
        <div class="form-shoop">
            <form action="../shoop.php" method="POST" enctype="multipart/form-data">
                <?php if (isset($loja)) ?>
                <img src="../<?= ($loja['caminho_img']); ?>">
                <div class="">
                </div>
                <div class="">
                    <label> Nome da loja: </label>
                    <input type="text" name="nome" value="<?= $loja['nome_loja'] ?>" required>
                </div>
                <div class="">
                    <label> Telefone: </label>
                    <input type="text" name="telefone" id="telefone-id" value="<?= $loja['telefone_loja'] ?>" required>
                </div>
                <div class="">
                    <label> CNPJ: </label>
                    <input type="text" name="cnpj" id="cnpj-id" value="<?= $loja['cnpj_loja'] ?>" required>
                </div>
                <div>
                    <button submit="editar_loja"> Editar </button>
                </div>
        </div>
    </div>
    <script>
        function applyMaskPhone(phone) {
            phone = phone.replace(/\D/g, '');
            phone = phone.replace(/(\d{2})(\d)/, '($1) $2');
            phone = phone.replace(/(\d{4})(\d{4})$/, '$1-$2');
            return phone;
        }
        document.getElementById('telefone-id').addEventListener('input', function (e) {
            e.target.value = applyMaskPhone(e.target.value);
        });

        function applyMaskCnpj(cnpj) {
            cnpj = cnpj.replace(/\D/g, '');
            cnpj = cnpj.replace(/(\d{2})(\d)/, '$1.$2');
            cnpj = cnpj.replace(/(\d{3})(\d)/, '$1.$2');
            cnpj = cnpj.replace(/(\d{3})(\d)/, '$1/$2');
            cnpj = cnpj.replace(/(\d{4})(\d{2})$/, '$1-$2');
            return cnpj;
        }
        document.getElementById('cnpj-id').addEventListener('input', function (e) {
            e.target.value = applyMaskCnpj(e.target.value);
        });
    </script>
</body>

</html>