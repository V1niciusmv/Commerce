<?php
require_once '../bd/connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/css/header.css">
  <title>header</title>
</head>
<bod>
  <div class="container-header">
    <div class="logo-header"> <img src="/img/logo.png" alt=""></div>
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
            <li> Produtos </li>
            <li> <a href="../logout.php"> Encerrar sessão </a></li>
          </ul>
        </div>
        </div>
      </div>
    <?php endif ?>
  </div>
  <script>
    function dropDown(){
      const modal =document.getElementById('id-dropdown');

      if (modal.classList.contains('show')){
        modal.classList.remove('show');
      }else {
       modal.classList.add('show');
      }
    }
    document.getElementById('id-dropdown').addEventListener('click', function(event) {
  event.stopPropagation();
});
  </script>
  </body>
</html>