<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/register.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>registrar/entrar</title>
</head>
<body>
    <?php include('header_page.php'); ?>
    <div class="container-full">
        <div class="form-full">
            <div class="form-login">
                <form action="../login.php" method="POST" id="form-id-login">
                    <h1> Faça seu login </h1>
                    <div class="lg">
                        <?php
                        if (isset($_SESSION['mensagem_sucesso_cadastro'])) {
                            echo '<p class="mensagem-sucesso">' . $_SESSION['mensagem_sucesso_cadastro'] . '</p>';
                            unset($_SESSION['mensagem_sucesso_cadastro']);
                        }
                        if (isset($_SESSION['sessao_vazia'])) {
                            echo '<p class="mensagem-sessao-vazia">' . $_SESSION['sessao_vazia'] . '</p>';
                            unset($_SESSION['sessao_vazia']);
                        }
                        if (isset($_SESSION['erro_login'])) {
                            echo '<p class="mensagem-sessao">' . $_SESSION['erro_login'] . '</p>';
                            unset($_SESSION['erro_login']);
                        } ?>
                        <label> E-mail </label>
                        <input type="text" name="email" required>
                        <i class="bx bx-envelope"></i>
                    </div>
                    <div class="lg">
                        <label> Senha </label>
                        <input type="password" name="senha" required>
                        <i class="bx bx-lock"></i>
                    </div>
                    <div class="lg-button">
                        <button type="submit" name="login_usuario"> Entrar </button>
                    </div>
                </form>
                <div class="div-login" id="div-login-id">
                    <h1>Olá, seja bem vindo</h1>
                    <p> Você ja tem cadastro?</p>
                    <div class="div-button">
                        <button onclick="toggleForm('login')"> Clique aqui </button>
                    </div>
                </div>
            </div>
            <div class="form-registro">
                <form action="../register.php" method="POST" id="form-id-register">
                    <h1> Faça cadastro </h1>
                    <div class="rg">
                        <label> Nome </label>
                        <input type="text" name="nome" required>
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="rg">
                        <?php
                        if (isset($_SESSION['email_usado'])) {
                            echo '<p class="usado">' . $_SESSION['email_usado'] . '</p>';
                            unset($_SESSION['email_usado']);
                        } ?>
                        <label> E-mail </label>
                        <input type="text" name="email" required>
                        <i class="bx bx-envelope"></i>
                    </div>
                    <div class="rg">
                        <?php
                        if (isset($_SESSION['cpf_usado'])) {
                            echo '<p class="usado">' . $_SESSION['cpf_usado'] . '</p>';
                            unset($_SESSION['cpf_usado']);
                        } ?>
                        <label> CPF </label>
                        <input type="text" name="cpf" id="cpf-id" required maxlength="14">
                        <i class="bx bx-id-card"></i>
                    </div>
                    <div class="rg">
                        <?php
                        if (isset($_SESSION['telefone_usado'])) {
                            echo '<p class="usado">' . $_SESSION['telefone_usado'] . '</p>';
                            unset($_SESSION['telefone_usado']);
                        } ?>
                        <label> Telefone </label>
                        <input type="text" name="telefone" id="telefone-id" required maxlength="15">
                        <i class="bx bx-phone"></i>
                    </div>
                    <div class="rg">
                        <label> endereco </label>
                        <input type="text" name="endereco" required>
                        <i class="bx bx-map"></i>
                    </div>
                    <div class="rg">
                        <label> Senha </label>
                        <input type="password" name="senha" required maxlength="14">
                        <i class="bx bx-lock"></i>
                    </div>
                    <div class="esqueci-senha">
                        <a href="#"> Esqueci minha senha </a>
                    </div>
                    <div class="rg-button">
                        <button type="submit" name="cadastrar_usuario"> cadastrar </button>
                    </div>
                </form>
                <div class="div-register" id="div-register-id">
                    <h1> Ainda não <br>
                        tem o cadastro??</h1>
                    <div class="div-button">
                        <button onclick="toggleForm('register')"> Clique aqui </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const params = new URL(document.location.toString()).searchParams;
        const action = params.get("action");

        toggleForm(action);

        function toggleForm(form) {
            const formCadastro = document.getElementById('div-register-id');
            const Formlogin = document.getElementById('div-login-id');
            if (form === 'register') {
                formCadastro.style.display = 'none';
                Formlogin.style.display = 'flex';
            } else if (form === 'login') {
                formCadastro.style.display = 'flex';
                Formlogin.style.display = 'none';
            }
        }

        function applyMaskCpf(cpf) {
            cpf = cpf.replace(/\D/g, '');
            cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
            cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
            cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            return cpf;
        }
        document.getElementById('cpf-id').addEventListener('input', function (e) {
            e.target.value = applyMaskCpf(e.target.value); 
        });

        function applyMaskPhone(phone){
            phone = phone.replace(/\D/g, '');
            phone = phone.replace(/(\d{2})(\d)/, '($1) $2');
            phone = phone.replace(/(\d{5})(\d{4})$/, '$1-$2');
            return phone;
        }
        document.getElementById('telefone-id').addEventListener('input', function (e){
            e.target.value = applyMaskPhone(e.target.value);
        });
    </script>
</body>

</html>