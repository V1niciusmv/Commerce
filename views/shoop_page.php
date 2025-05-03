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
    <link rel="stylesheet" href="/css/shoop.css">
    <title>Loja</title>
</head>

<body>
    <?php include('header_page.php'); ?>
    <div class="container-shoop-full">
        <h1> Sua loja</h1>
        <?php if (isset($loja)): ?>
            <?php if (isset($_SESSION['cadastroLoja_sucesso'])) {
                echo '<p class="green">' . $_SESSION['cadastroLoja_sucesso'] . '</p>';
                unset($_SESSION['cadastroLoja_sucesso']);
            }
            if (isset($_SESSION['produto_apagar'])) {
                echo '<p class="red">' . $_SESSION['produto_apagar'] . '</p>';
                unset($_SESSION['produto_apagar']);
            } ?>
        <div class="div-exibicao">
            <div class="div1">
                <img src="../<?= ($loja['caminho_img']); ?>">
                <label> Logo da loja </label>
            </div>
        <div class="div2">
            <div class="exib-register">
                <label> Nome da loja: </label>
                <input type="text" name="nome" value="<?= $loja['nome_loja'] ?>" readonly required>
            </div>
            <div class="exib-register">
                <label> Telefone: </label>
                <input type="text" name="telefone" value="<?= $loja['telefone_loja'] ?>" readonly required>
            </div>
            <div class="exib-register">
                <label> CNPJ: </label>
                <input type="text" name="cnpj" id="cnpj-id" value="<?= $loja['cnpj_loja'] ?>" readonly required>
            </div>
        </div>
        </div>
        <div class="btn-exib">
    <div class="btn1">
        <button type="button" onclick="window.location.href='shoopEdit_page.php'"> Editar  <i class='bx bx-pencil'></i></button>
    </div>

    <form action="../shoopDelete.php" method="POST">
        <input type="hidden" name="id_loja" value="<?= $loja['id_loja'] ?>"> 
        <button type="submit" name="deletar_loja"> Deletar loja <i class='bx bx-trash'></i></button>
    </form>
</div>

        <?php else: ?>
            <h2> Cadastre sua Loja </h2>
            <form action="../shoop.php" method="POST" enctype="multipart/form-data">
                <div class="sessionRegister">
                <?php
                if (isset($_SESSION['restrincao_criarLoja'])) {
                    echo '<p class="red">' . $_SESSION['restrincao_criarLoja'] . '</p>';
                    unset($_SESSION['restrincao_criarLoja']);
                }
                if (isset($_SESSION['loja_deletada'])) {
                    echo '<p class="red">' . $_SESSION['loja_deletada'] . '</p>';
                    unset($_SESSION['loja_deletada']);
                } ?>
                    </div>
                <div class="input-register">
                    <div class="result-register">
                        <?php
                        if (isset($_SESSION['nomeLojaUsado'])) {
                            echo '<p class="red">' . $_SESSION['nomeLojaUsado'] . '</p>';
                            unset($_SESSION['nomeLojaUsado']);
                        } ?>
                        <label> Nome da loja: </label>
                        <input type="text" name="nome" value="<?= $_SESSION['register_loja']['nome'] ?? '' ?>" required>
                    </div>
                    <span id="erros"></span>
                    <div class="result-register">
                        <?php
                        if (isset($_SESSION['telefoneUsado'])) {
                            echo '<p class="red">' . $_SESSION['telefoneUsado'] . '</p>';
                            unset($_SESSION['telefoneUsado']);
                        }
                        ?>
                        <label> Telefone: </label>
                        <input type="text" name="telefone" maxlength="15" id="telefone-id"
                            value="<?= $_SESSION['register_loja']['telefone'] ?? '' ?>" required>
                    </div>
                </div>
                <div class="input-register">
                    <div class="result-register">
                        <?php
                        if (isset($_SESSION['cnpjUsado'])) {
                            echo '<p class="red">' . $_SESSION['cnpjUsado'] . '</p>';
                            unset($_SESSION['cnpjUsado']);
                        }
                        ?>
                        <span id="errosCnpj"></span>
                        <label> CNPJ: </label>
                        <input type="text" name="cnpj" id="cnpj-id" pattern="{18}" value="<?= $_SESSION['register_loja']['cnpj'] ?? '' ?>"
                            maxlength="18" required>
                    </div>
                    <div class="result-register-img">
                        <input type="file" id="idimg" name="imagem" accept="image/*">
                        <label for="idimg">
                            <i class='bx bx-camera'></i>
                       <span id=file-name> <?= $_SESSION['register_loja_files']['imagem_nome'] ?? 'Adicione uma imagem' ?> </span>
                        </label>
                    </div>
                </div>
                <div class="btn-register">
                    <button type="submit" id="submit-form" name="cadastrar_loja" disabled> Criar loja </a>
                </div>
            </form>
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
        window.onload = toggleButtonState;

        document.getElementById('idimg').addEventListener('change', function(e) {
    const fileNameSpan = document.getElementById('file-name');
    
    if (this.files.length > 0) {
        // Se um novo arquivo foi selecionado
        fileNameSpan.textContent = this.files[0].name;
    } else {
        // Se nenhum arquivo selecionado, mostra o da sessão ou texto padrão
        fileNameSpan.textContent = "<?= $_SESSION['register_loja_files']['imagem_nome'] ?? 'Adicione uma imagem' ?>";
    }
});
    </script>
</body>

</html>