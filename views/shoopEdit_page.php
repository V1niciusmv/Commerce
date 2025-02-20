<?php
session_start();
require '../bd/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('location: register_page.php?action=login');
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
    <link rel="stylesheet" href="/css/shoop.css">
    <title>Loja</title>
</head>

<body>
    <?php include('header_page.php'); ?>
    <div class="container-shoop-full-edit">
        <h2> Edite seus produtos </h2>
        <?php if (isset($_SESSION['editLoja_sucesso'])) {
                    echo '<p class="green">' . $_SESSION['editLoja_sucesso'] . '</p>';
                    unset($_SESSION['editLoja_sucesso']);
                } ?>
                <?php if (isset($_SESSION['restrincao_criarLoja'])) {
                    echo '<p class="red">' . $_SESSION['restrincao_criarLoja'] . '</p>';
                    unset($_SESSION['restrincao_criarLoja']);
                } ?>
                <?php if (isset($_SESSION['loja_nao_editada'])) {
                    echo '<p class="red">' . $_SESSION['loja_nao_editada'] . '</p>';
                    unset($_SESSION['loja_nao_editada']);
                } ?>
                <?php if (isset($_SESSION['Erro no envio de imagem'])) {
                    echo '<p class="red">' . $_SESSION['Erro no envio de imagem'] . '</p>';
                    unset($_SESSION['Erro no envio de imagem']);
                } ?>
        <form action="../shoopEdit.php" method="POST" enctype="multipart/form-data">
            <?php if (isset($loja)): ?>
                <div class="div1-edit">
                    <img id="imagem-loja" src="../<?= ($loja['caminho_img']); ?>">
                    <input type="file" name="imagem" accept="image/*" style="display :none" id="id-input-img">
                    <i class="bx bx-camera" id="id-icon"></i>
                </div>
                <div class="div2-edit">
                    <div class="input2">
                    <div class="result-input-edit">
                        <?php if (isset($_SESSION['nomeLojaUsado'])) {
                            echo '<p class="">' . $_SESSION['nomeLojaUsado'] . '</p>';
                            unset($_SESSION['nomeLojaUsado']);
                        } ?>
                        <input type="hidden" name="id_loja" value="<?= $loja['id_loja'] ?>">

                        <label> Nome da loja: </label>
                        <input type="text" name="nome" value="<?= $loja['nome_loja'] ?>" required>
                    </div>
                    <span id="erros"></span>
                    <div class="result-input-edit">
                        <?php if (isset($_SESSION['telefoneUsado'])) {
                            echo '<p class="red">' . $_SESSION['telefoneUsado'] . '</p>';
                            unset($_SESSION['telefoneUsado']);
                        } ?>
                        <label> Telefone: </label>
                        <input type="text" name="telefone" id="telefone-id" maxlength="15"
                            value="<?= $loja['telefone_loja'] ?>" required>
                    </div>
                    </div>
                    <span id="errosCnpj"></span>
                    <div class="result-input-edit">
                        <?php if (isset($_SESSION['cnpjUsado'])) {
                            echo '<p class="red">' . $_SESSION['cnpjUsado'] . '</p>';
                            unset($_SESSION['cnpjUsado']);
                        } ?>
                        <label> CNPJ: </label>
                        <input type="text" name="cnpj" id="cnpj-id" maxlength="18" value="<?= $loja['cnpj_loja'] ?>"
                            required>
                    </div>
                    <div class="btn-edit">
                        <button type="submit" name="editar_loja" id="submit-form"> Editar </button>
                    </div>
                </div>
            <?php endif ?>
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
        const img = document.getElementById('imagem-loja');
        const input = document.getElementById('id-input-img');
        const icon = document.getElementById('id-icon');

        icon.addEventListener('click', () => {
            input.click();
        });

        input.addEventListener('change', (event) => {
            const file = event.target.files[0]; // Obtém o arquivo selecionado
            if (file) {
                const reader = new FileReader(); // Cria um leitor de arquivos
                reader.onload = function (e) {
                    img.src = e.target.result; // Atualiza a imagem mostrada
                };
                reader.readAsDataURL(file); // Lê o arquivo como uma URL local
            }
        });
    </script>
</body>

</html>