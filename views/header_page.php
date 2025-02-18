<?php
require_once '../bd/connection.php';

$rotas = [
    'home_page.php' => null,
    'product_page.php' => 'home_page.php',
    'productEdit_page.php' => 'product_page.php',
    'shoop_page.php' => 'home_page.php',
    'shoopEdiit_page.php' => 'shoop_page.php',
];

$paginaAtual = basename($_SERVER['PHP_SELF']);

$destino = isset($rotas[$paginaAtual]) ? $rotas[$paginaAtual] : 'home_page.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/css/header.css">
  <title>header</title>
</head>
<body>
  <div class="container-header">
    <div class="logo-header">
      <?php
      $pages = basename($_SERVER['PHP_SELF']);
      if (in_array($pages, ['home_page.php', 'welcome_page.php', 'register_page.php'])) {
        echo '<img src="/img/logo.webp" alt="Logo">';
      } else {
        echo "<i class='bx bx-chevron-left' onclick='window.location.href=\"$destino\"'></i>";
      }
      ?>
    </div>
    <?php if (!isset($_SESSION['user_id'])): ?>
      <div class="links-header">
        <div class="font-header" onclick="window.location.href='/views/register_page.php?action=register'"> Cadastre-se
        </div>
        <div class="font-header" onclick="window.location.href='/views/register_page.php?action=login'"> Login </div>
      </div>
    <?php else: ?>
      <div class="search">
        <input type="search">
        <i class='bx bx-search'></i>
      </div>
      <div class="link-header-home">
        <div class="cart">
          <i class='bx bx-cart'></i>
        </div>
        <div class="notification">
          <i class='bx bx-bell'></i>
        </div>
        <div class="profile" onclick="dropDown()">
          <i class='bx bx-user'></i>
          <div class="dropdown" id="id-dropdown" onclick="stopPropagation()">
            <ul>
              <li> Meu perfil </li>
              <li> <a href="shoop_page.php"> Loja </a></li>
              <li> <a href="product_page.php"> Produtos </li>
              <li> <a href="../logout.php"> Encerrar sessão </a></li>
            </ul>
          </div>
        </div>
      </div>
    <?php endif ?>
  </div>
  <script>
    function dropDown() {
      const modal = document.getElementById('id-dropdown');

      if (modal.classList.contains('show')) {
        modal.classList.remove('show');
      } else {
        modal.classList.add('show');
      }
    }
    document.getElementById('id-dropdown').addEventListener('click', function (event) {
      event.stopPropagation();
    });
  </script>
  </body>
</html>