<?php 
session_start();
if (isset($_SESSION['user_id']))  { // Quando o user mudar a URL para welcome e nao dar erro de nao ter uma quebra de session, a pagina ira quebrar
    unset($_SESSION['user_id']);
}

//Array associativo onde tem o register_page que esta associado a tres outras opções
$rotass = [
    'register_page.php' => [
        'login' => 'welcome_page.php',
        'register' => 'welcome_page.php',
        'default' => 'welcome_page.php' 
    ],
    'default' => 'welcome_page.php' // se n existir nada na URL ele vai para welcome pagina padrão
];

$paginaAtuall = basename($_SERVER['PHP_SELF']); // Pega o nome da pagina atual, register_page.php
$action = isset($_GET['action']) ? $_GET['action'] : 'default'; // Pega o parametro da URL atual se existir, se nao existir pega o default

if ($paginaAtuall === 'register_page.php') { // Verifica se a pagina atual é register
    $destinoo = $rotass['register_page.php'][$action] ?? $rotass['register_page.php']['default']; // E verifica e assosia
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/register.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <title>registrar/entrar</title>
</head>
<body>
    <?php include('header_page.php'); ?>
    <div class="container-full">
    <div class="back-icon"> <!-- Icon pega a variavel $destino que esta fazendo a verificação e associação do array associativo -->
    <i class='bx bx-chevron-left' onclick="window.location.href='<?= $destinoo ?>'"></i>
</div>
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
                        <input type="text" name="email"  value="<?= $_SESSION['login_data']['email'] ?? '' ?>" required>
                        <i class="bx bx-envelope"></i>
                    </div>
                    <div class="lg">
                        <label> Senha </label>
                        <input type="password" name="senha" value="<?= $_SESSION['login_data']['senha'] ?? '' ?>" required>
                        <i class="bx bx-lock"></i>
                    </div>
                    <div class="lg-button">
                        <button type="submit" name="login_usuario"> Entrar </button>
                    </div>
                </form>
                <div class="div-login" id="div-login-id">
                    <h1>Olá, seja bem vindo</h1>
                    <p> Você ja tem cadastro?</p>
                    <div class="div-button"> <!-- button de alternar o Form entre Login e cadastro -->
                        <button onclick="toggleForm('login')"> Clique aqui </button>
                    </div>
                </div>
            </div>
            <div class="form-registro">
                <form action="../register.php" method="POST" id="form-id-register">
                    <h1> Faça cadastro </h1>
                    <div class="rg">
                        <label> Nome </label>
                        <input type="text" name="nome" value="<?= $_SESSION['register_data']['nome'] ?? '' ?>" required>
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="rg">
                        <?php
                        if (isset($_SESSION['email_usado'])) {
                            echo '<p class="usado">' . $_SESSION['email_usado'] . '</p>';
                            unset($_SESSION['email_usado']);
                        } ?>
                        <label> E-mail </label>
                        <input type="text" name="email" value="<?= $_SESSION['register_data']['email'] ?? '' ?>" required>
                        <i class="bx bx-envelope"></i>
                    </div>
                    <div class="rg">
                        <?php
                        if (isset($_SESSION['cpf_menor'])) {
                            echo '<p class="usado">' . $_SESSION['cpf_menor'] . '</p>';
                            unset($_SESSION['cpf_menor']);
                        } ?>
                          <?php
                        if (isset($_SESSION['cpf_menor'])) {
                            echo '<p class="usado">' . $_SESSION['cpf_menor'] . '</p>';
                            unset($_SESSION['cpf_menor']);
                        } ?>
                        <label> CPF </label>
                        <input type="text" name="cpf" id="cpf-id" value="<?= $_SESSION['register_data']['cpf'] ?? '' ?>" required maxlength="14">
                        <i class="bx bx-id-card"></i>
                    </div>
                    <div class="rg">
                        <?php
                        if (isset($_SESSION['telefone_usado'])) {
                            echo '<p class="usado">' . $_SESSION['telefone_usado'] . '</p>';
                            unset($_SESSION['telefone_usado']);
                        } ?>
                          <?php
                        if (isset($_SESSION['telefone-menor'])) {
                            echo '<p class="usado">' . $_SESSION['telefone-menor'] . '</p>';
                            unset($_SESSION['telefone-menor']);
                        } ?>
                        
                        <label> Telefone </label>
                        <input type="text" name="telefone" id="telefone-id" value="<?= $_SESSION['register_data']['telefone'] ?? '' ?>" required maxlength="15">
                        <i class="bx bx-phone"></i>
                    </div>
                    <div class="rg">
                        <label> endereco </label>
                        <input type="text" name="endereco" value="<?= $_SESSION['register_data']['endereco'] ?? '' ?>" required>
                        <i class="bx bx-map"></i>
                    </div>
                    <div class="rg">
                        <label> Senha </label>
                        <input type="password" name="senha" value="<?= $_SESSION['register_data']['senha'] ?? '' ?>" required maxlength="14">
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
                    <div class="div-button"> <!-- button de alternar o Form entre Login e cadastro -->
                        <button onclick="toggleForm('register')"> Clique aqui </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // New URL transforma a URL em um objeto manipulavel 
        // document.location.toString  Pega a URL completa da pagina
        // searchParams pega os parametros da URL que vem depois do '?'
        const params = new URL(document.location.toString()).searchParams;
        const action = params.get("action"); // Pegamos o parametro

        toggleForm(action); // E chamamos a function passando o action

        function toggleForm(form) { // Verificamos se a action é login ou register para a visibilidades das DIV 
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
            // Remove tudo oque não é numero
            // \D procura tudo que não tem numero
            // g, aplica em todos os caracteres não so o primeiro
            // \d procura digitos numericos
            // Exemplo: Pegamos os 3 primeiros digitos e depois pegamos os proximos digitos
            // acrescentamos um (.) a cada 3 digitos e no ultimo replace pegamos 1 ou 2 numeros
            cpf = cpf.replace(/\D/g, '');
            cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2'); 
            cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
            cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            return cpf;
        }
        // Pegamos o id do input do CPF e adicinamos um Event input e pegamos os dados em tempo real doque esta sendo digitado e aplicamos a function
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