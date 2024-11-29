<?php
var_dump($_SESSION['loja']);
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
                <?php if(isset($_SESSION['loja'])): ?>
                    <div class="">
                        <label> Nome da loja: </label>
                        <input type="text" name="nome" <?= $loja['nome_loja'] ?> required>
                    </div>
                    <div class="">
                        <label> Telefone: </label>
                        <input type="text" name="telefone" <?= $loja['telefone_loja'] ?>required>
                    </div>
                    <div class="">
                        <label> CNPJ: </label>
                        <input type="text" name="cnpj" maxlength="14" <?= $loja['cnpj_loja'] ?> required>
                    </div>
                    <div class="">
                        <label> Adicione uma imagem para sua loja: </label>
                        <input type="file" name="imagem" accept="image/*" required>
                    </div>
                    <div class="">
                        <button type="submit" name="cadastrar_loja"> Criar loja </a>
                    </div>
                <?php else: ?>
                    <div class="">
                        <?php
                        if (isset($_SESSION['cadastroLoja_sucesso'])) {
                            echo '<p class="">' . $_SESSION['cadastroLoja_sucesso'] . '</p>';
                            unset($_SESSION['cadastroLoja_sucesso']);
                        }
                        if (isset($_SESSION['nomeLojaUsado'])) {
                            echo '<p class="">' . $_SESSION['nomeLojaUsado'] . '</p>';
                            unset($_SESSION['nomeLojaUsado']);
                        }
                        if (isset($_SESSION['restricao_criarLoja'])) {
                            echo '<p class="">' . $_SESSION['restricao_criarLoja'] . '</p>';
                            unset($_SESSION['restricao_criarLoja']);
                        }
                        ?>
                        <label> Nome da loja: </label>
                        <input type="text" name="nome" required>
                    </div>
                    <div class="">
                        <label> Telefone: </label>
                        <input type="text" name="telefone" required>
                    </div>
                    <div class="">
                        <?php
                        if (isset($_SESSION['cnpjUsado'])) {
                            echo '<p class="">' . $_SESSION['cnpjUsado'] . '</p>';
                            unset($_SESSION['cnpjUsado']);
                        }
                        ?>
                        <label> CNPJ: </label>
                        <input type="text" name="cnpj" maxlength="14" required>
                    </div>
                    <div class="">
                        <label> Adicione uma imagem para sua loja: </label>
                        <input type="file" name="imagem" accept="image/*" required>
                    </div>
                    <div class="">
                        <button type="submit" name="cadastrar_loja"> Criar loja </a>
                    </div>
                </form>
            <?php endif ?>
        </div>
    </div>
</body>

</html>