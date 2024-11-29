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
    <title>Dragon Commerce</title>
</head>
<body>
    <?php include ('header_page.php'); ?>
    <div class="container-full">
        <div class="container">
            <div class="frase">
                <h1> Seja bem vindo ao Dragon Commerce</h1>
                <p>Uma ampla variedade de especiarias frescas e exóticas, <br>
                que você só vai encontrar aqui! Explore sabores únicos e autênticos, <br>
                ideais para transformar qualquer receita em uma experiência especial.</p>
            </div>
            <div>
                <button onclick="window.location.href=''"> Entrar </button>
            </div>
        </div>

        <div class="carrosel">
            <div class="slides-btn">
                <input type="radio" name="radio-btn" id="radio1" checked>
                <input type="radio" name="radio-btn" id="radio2">
                <input type="radio" name="radio-btn" id="radio3">
                <input type="radio" name="radio-btn" id="radio4">
            

            <div class="slides-img">
                <img src="https://images8.alphacoders.com/127/1270655.jpg" alt="imagem1">
            </div>
            <div class="slides-img">
                <img src="https://th.bing.com/th?id=OIP.gmn_Xq4zfkA-vux_hBsJSAHaHa&w=250&h=250&c=8&rs=1&qlt=90&o=6&dpr=1.1&pid=3.1&rm=2" alt="imagem2">
            </div>
            <div class="slides-img">
                <img src="https://images8.alphacoders.com/127/1270655.jpg" alt="imagem3">
            </div>
            <div class="slides-img">
                <img src="https://th.bing.com/th?id=OIP.gmn_Xq4zfkA-vux_hBsJSAHaHa&w=250&h=250&c=8&rs=1&qlt=90&o=6&dpr=1.1&pid=3.1&rm=2" alt="imagem4">
            </div>
        </div>
        
        <div class="manual-navigation"> 
            <label for="radio1" class="manual-btn"> </label>
            <label for="radio2" class="manual-btn"> </label>
            <label for="radio3" class="manual-btn"> </label>
            <label for="radio4" class="manual-btn"> </label>
        </div>
    </div>
    </div>
    <footer>
        <div class="footer-container">
            <div class="">
                <p> <strong> <?= $nome_loja ?> </strong> </p> 
            </div>
                <p>Email: <?= $email ?></p>
                <p>Telefone: <?= $telefone ?></p>
                <p>WhatsApp: <a href="#"> <?= $whatsapp ?></a></p>
                <p>Instagram: <a href="#"> <?= $instagram ?> </a></p>
                <p>Endereço: <?= $endereco_loja ?></p>
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
?>