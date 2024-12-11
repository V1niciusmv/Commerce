<?php session_start();
require '../bd/connection.php';
if (!isset($_SESSION['user_id'])) {
    header('location:
    register_page.php?action=login');
    exit();
}
$sql = "SELECT loja.*, imagens.caminho_img FROM loja LEFT JOIN imagens ON loja.id_loja = lojas_id_loja
        WHERE loja.users_id_users = :user_id";
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
                <?php if (isset($loja)): ?>
                    <?php if (isset($_SESSION['cadastroLoja_sucesso'])) {
                        echo '<p class="">' . $_SESSION['cadastroLoja_sucesso'] . '</p>';
                        unset($_SESSION['cadastroLoja_sucesso']);
                    } ?>
                    <img src="../<?= ($loja['caminho_img']); ?>">
                    <div class="">
                    </div>
                    <div class="">
                        <label> Nome da loja: </label>
                        <input type="text" name="nome" value="<?= $loja['nome_loja'] ?>" readonly required>
                    </div>
                    <div class="">
                        <label> Telefone: </label>
                        <input type="text" name="telefone" value="<?= $loja['telefone_loja'] ?>" readonly required>
                    </div>
                    <div class="">
                        <label> CNPJ: </label>
                        <input type="text" name="cnpj" id="cnpj-id" value="<?= $loja['cnpj_loja'] ?>" readonly required>
                    </div>

                    <button type="button" onclick="window.location.href='shoopEdit_page.php'"> Editar </button>

                    <form action="../shoopDelete.php" method="POST" style="display : inline">
                        <input type="hidden" name="id_loja" value="<?= $loja['id_loja'] ?>">
                        <button type=submit name="deletar_loja"> Deletar loja </button>
                        </input>
                    </form>
        <?php else: ?>
            <form action="../shoop.php" method="POST" enctype="multipart/form-data">
            <div class="">
                <?php
                if (isset($_SESSION['nomeLojaUsado'])) {
                    echo '<p class="">' . $_SESSION['nomeLojaUsado'] . '</p>';
                    unset($_SESSION['nomeLojaUsado']);
                }
                if (isset($_SESSION['restrincao_criarLoja'])) {
                    echo '<p class="">' . $_SESSION['restrincao_criarLoja'] . '</p>';
                    unset($_SESSION['restrincao_criarLoja']);
                }
                if (isset($_SESSION['loja_deletada'])) {
                    echo '<p class="">' . $_SESSION['loja_deletada'] . '</p>';
                    unset($_SESSION['loja_deletada']);
                }
                ?>
                <label> Nome da loja: </label>
                <input type="text" name="nome" value="<?= $_GET['nome'] ?? '' ?>" required>
            </div>
            <span id="erros"></span>
            <div class="">
                <?php
                if (isset($_SESSION['telefoneUsado'])) {
                    echo '<p class="">' . $_SESSION['telefoneUsado'] . '</p>';
                    unset($_SESSION['telefoneUsado']);
                }
                ?>
                <label> Telefone: </label>
                <input type="text" name="telefone" maxlength="15" id="telefone-id" value="<?= $_GET['telefone'] ?? '' ?>"
                    required>
            </div>
            <span id="errosCnpj"> </span>
            <div class="">
                <?php
                if (isset($_SESSION['cnpjUsado'])) {
                    echo '<p class="">' . $_SESSION['cnpjUsado'] . '</p>';
                    unset($_SESSION['cnpjUsado']);
                }
                ?>
                <label> CNPJ: </label>
                <input type="text" name="cnpj" id="cnpj-id" pattern="{18}" value="<?= $_GET['cnpj'] ?? '' ?>" maxlength="18"
                    required>
            </div>
            <div class="">
                <label> Adicione uma imagem para sua loja: </label>
                <input type="file" name="imagem" accept="image/*">
            </div>
            <div class="">
                <button type="submit" id="submit-form" name="cadastrar_loja" disabled> Criar loja </a>
            </div>
            </form>
        <?php endif ?>
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

            const password = e.target.value.replace(/\D/g, '');
            const errors = document.getElementById('erros');
            const button = document.getElementById('submit-form');

            if (password.length < 11) {
                if (password.length === 0) {
                    errors.innerHTML = '';
                } else {
                    errors.innerHTML = 'É necessario ter 14 números';
                }
            } else {
                errors.innerHTML = '';
            }
            toggleButtonState();
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

            const cnpj = e.target.value.replace(/\D/g, '');
            const erros = document.getElementById('errosCnpj');
            const button = document.getElementById('submit-form');

            if (cnpj.length < 14) {
                if (cnpj.length === 0) {
                    erros.innerHTML = '';
                } else {
                    erros.innerHTML = 'O CNPJ tem que ter 14 digitos';
                }
            } else {
                erros.innerHTML = '';
            }

            toggleButtonState();
        });

        function toggleButtonState() {
            const phone = document.getElementById('telefone-id').value.replace(/\D/g, '');
            const cnpj = document.getElementById('cnpj-id').value.replace(/\D/g, '');
            const button = document.getElementById('submit-form');

            if (phone.length === 11 && cnpj.length === 14) {
                button.disabled = false;
            } else {
                button.disabled = true;
            }
        }
        window.onload =toggleButtonState;
    </script>
</body>

</html>