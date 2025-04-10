<?php
$nome_loja = 'Dragon Commerce';
$email = 'DragonCommerce@gmail.com';
$telefone = '(81) 7901-4191';
$whatsapp = '(81) 7901-4191';
$instagram = 'https://www.instagram.com/dragoncommerce';
$endereco_loja = 'Campus Aurora';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/welcome.css">
    <title>Commerce</title>
</head>

<body>
    <?php include('header_page.php'); ?>
    <div class="container-full">
        <div class="container">
            <div class="frase">
                <h1> Seja bem vindo ao Commerce</h1>
                <p>Uma ampla variedade de especiarias frescas e exóticas, <br>
                    que você só vai encontrar aqui! Explore sabores únicos e autênticos, <br>
                    ideais para transformar qualquer receita em uma experiência especial.</p>
                    <div class="frase-button">
                        <button onclick="window.location.href=''"> Entrar </button>
                    </div>
            </div>
        </div>

        <div class="carrosel">
            <input type="radio" name="btn-radio" id="radio1">
            <input type="radio" name="btn-radio" id="radio2">
            <input type="radio" name="btn-radio" id="radio3">
            <input type="radio" name="btn-radio" id="radio4">
            <div class="slides">

                <div class="slide-img">
                    <img src="https://th.bing.com/th/id/OIP.JXBW-Jmc4Jui38dRsmi_lwHaE8?w=306&h=204&c=8&rs=1&qlt=90&o=6&pid=3.1&rm=2" alt="imagem1">
                </div>
                <div class="slide-img">
                    <img src="https://malaprontagramado.com.br/wp-content/uploads/2020/04/roupas-para-natal-luz.jpg"
                        alt="imagem2">
                </div>
                <div class="slide-img">
                    <img src="https://th.bing.com/th/id/OIP.A7mkpM5E8jh7rPA4LHkc1wHaE8?w=223&h=180&c=7&r=0&o=5&pid=1.7" alt="imagem3">
                </div>
                <div class="slide-img">
                    <img src="https://cdn.shopify.com/s/files/1/0702/7428/5842/products/GamaAltacollage.png?v=1674000333&width=1946"
                        alt="imagem4">
                </div>
            </div>
            <div class="navigation">
                <label for="radio1" class="labelRadio"></label>
                <label for="radio2" class="labelRadio"></label>
                <label for="radio3" class="labelRadio"></label>
                <label for="radio4" class="labelRadio"></label>
            </div>
        </div>

    </div>
    <footer>
        <div class="footer-container">
            <div class="nomeLoja">
                <p> <strong> <?= $nome_loja ?> </strong> </p>
            </div>
        <div class="options1">
            <p>Email: <?= $email ?></p>
            <p>Telefone: <?= $telefone ?></p>
            <p>Endereço: <?= $endereco_loja ?></p>
            <p>WhatsApp: <a href="#"> <?= $whatsapp ?></a></p>
            <p>Instagram: <a href="#"> <?= $instagram ?> </a></p>
        </div>
        </div>
        </div>
    </footer>

    <script>
        let counter = 1;
        setInterval(() => {
            document.getElementById('radio' + counter).checked = true;
            counter++;
            if (counter > 4) {
                counter = 1;
            }
        }, 2000); // Muda a cada 2 segundos
    </script>
</body>

</html>