<?php 
session_start();
require '../bd/connection.php';
if (!isset($_SESSION['user_id'])) {
    header('location: register_page.php?action=login');
    exit();
}

$sql = "SELECT * FROM users WHERE id_users = :users";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':users', $_SESSION['user_id']);
$stmt->execute();
$dadoUser = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/user.css">
    <title>Seu perfil </title>
</head>
<body>
    <?php include ('header_page.php'); ?>
    <h2 id="h2viewEdit"> Seus dados </h2>
    <h2 id="h2formEdit"> Edite seus dados </h2>
    <div class="container-perfil">
        <div class="container-datas" id="viewEdit"> 
    <?php if (isset($_SESSION['user_atualizado'])) {
                        echo '<p class="sessionGreen">' . $_SESSION['user_atualizado'] . '</p>';
                        unset($_SESSION['user_atualizado']);
                    } ?>
            <div class="itens-datas"> 
            <div class="forLabel">
                <label> Nome </label>
                <input type='text' value="<?= $dadoUser['nome_users']?>" readonly>
            </div>
            <div class="forLabel">
                <label> E-mail </label>
                <input type='text' value="<?= $dadoUser['email_users']?>" readonly>
            </div>
            <div class="forLabel">
                <label> Telefone </label>
                <input type='text' value="<?= $dadoUser['telefone_users']?>" readonly>
            </div>
            </div>
            <div class="itens-datas">
            <div class="forLabel">
                <label> Endereço</label>
                <input type='text' value="<?= $dadoUser['endereco_users']?>" readonly>
            </div>
            <div class="forLabel">
                <label> CPF </label>
                <input type='text' value="<?= $dadoUser['cpf_users']?>" readonly>
            </div>
            <div class="forLabel">
                <label> Senha</label>
                <input type='password' value="<?= $dadoUser['senha_users']?>" readonly>
            </div>
            </div>
            <div class="btn-user"> 
                <button onclick="edit()"> Editar informações </button> 
            </div>
        </div>

    <!-- edit -->
        <div class="container-datas" id="formEdit"> 
       <div id="errosEdition"> </div>
            <form id="formEditUser">
            <div class="itens-datas"> 
            <input type='hidden' name="editar_usuario" value="<?= $dadoUser['id_users']?>">
            <div class="forLabel">
                <label> Nome </label>
                <input type='text' name="nome" value="<?= $dadoUser['nome_users']?>">
            </div>
            <div class="forLabel">
                <label> E-mail </label>
                <input type='text' name="email" value="<?= $dadoUser['email_users']?>">
            </div>
            <div class="forLabel">
                <label> Telefone </label>
                <input type='text' name="telefone" id="telefone-id" value="<?= $dadoUser['telefone_users']?>" maxlength="15">
            </div>
            </div>
            <div class="itens-datas">
            <div class="forLabel">
                <label> Endereço</label>
                <input type='text' name="endereco" value="<?= $dadoUser['endereco_users']?>">
            </div>
            <div class="forLabel">
                <label> CPF </label>
                <input type='text' name="cpf" id="cpf-id" value="<?= $dadoUser['cpf_users']?>" maxlength="14">
            </div>
            <div class="forLabel">
                <label> Senha</label>
                <input type='text' name="senha" value="<?= $dadoUser['senha_users']?>">
            </div>
            </div>
            <div class="btn-user"> 
                <button type="submit" id="btnEdit"> Salvar</button> 
            </div>
            </form>
        </div>
    </div>
    <script>

        function edit() {
            document.getElementById('viewEdit').style.display = 'none';
            document.getElementById('formEdit').style.display = 'flex';

            document.getElementById('h2viewEdit').style.display = 'none';
            document.getElementById('h2formEdit').style.display = 'flex';
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

        document.getElementById('formEditUser').addEventListener('submit', async function (e) {
            e.preventDefault();

            const dadosUser = new FormData(this);
            const btnEdit = document.getElementById('btnEdit');

            try {
                btnEdit.disabled = true;
                btnEdit.textContent = 'Enviando'; 

                const response = await fetch('../user.php', {
                        method: 'POST',
                        body: dadosUser,
                        credentials: 'same-origin'
                });

                if (!response.ok) {
    throw new Error('Erro na resposta do servidor: ' + response.status);
}

        // Verifica se a resposta é JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Resposta não é JSON');
        }

        const data = await response.json();

        if (!data.success) {
            document.getElementById('errosEdition').textContent = data.erros.join('\n');
            document.getElementById('errosEdition').style.color = 'red';  
        } else if (data.success){
            window.location.reload();
        }
            } catch (erro) {
                console.log('Erro:', erro);
                document.getElementById('errosEdition').textContent = "Erro ao processar a resposta do servidor";
            }   finally {  
                btnEdit.disabled = false; // Habilita o button
                btnEdit.textContent = 'Adicionar';         
        }   
        });
    </script>
</body>
</html>