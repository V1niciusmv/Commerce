<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once '../bd/connection.php';

$rotas = [
  'home_page.php' => null,
  'product_page.php' => 'home_page.php',
  'productEdit_page.php' => 'product_page.php',
  'shoop_page.php' => 'home_page.php',
  'shoopEdit_page.php' => 'shoop_page.php',
];

$paginaAtual = basename($_SERVER['PHP_SELF'], '.php');

$destino = isset($rotas[$paginaAtual]) ? $rotas[$paginaAtual] : 'home_page.php';

$sql = "SELECT id_cart FROM cart WHERE user_id_cart = :cartId";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':cartId', $_SESSION['user_id']);
$stmt->execute();
$verificar = $stmt->fetch(PDO::FETCH_ASSOC);
if ($verificar) {
  $cartId = $verificar['id_cart'];
}

$sql = "SELECT SUM(quantity) AS total FROM cart_items WHERE cart_id = :cart_Id";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':cart_Id', $cartId);
$stmt->execute();
$totalProdutos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
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
        echo '<img class="logoWebp" src="/img/logo.webp" alt="Logo">';
      } else {
        echo "<i class='bx bx-chevron-left' onclick='window.location.href=\"$destino\"'></i>";
      }
      ?>
    </div>

    <?php if ($paginaAtual === 'buy_page.php') 
        echo "<h1> Carrinho de compras </h1>";
      ?>

      <?php 
       $paginaSemSearch = ['welcome_page.php', 'register_page.php'];
      
      if(!in_array(basename($_SERVER['PHP_SELF']), $paginaSemSearch)) {
        echo '<div class="search">
      <input type="search" id="search-id-input">
      <i class="bx bx-search"></i>
      <div id="resultadosBusca" class="search-results">
      </div>
    </div>';
      }
      ?>
    <?php if (!isset($_SESSION['user_id'])): ?>
      <div class="links-header">
        <div class="font-header" onclick="window.location.href='/views/register_page.php?action=register'"> Cadastre-se
        </div>
        <div class="font-header" onclick="window.location.href='/views/register_page.php?action=login'"> Login </div>
      </div>
    <?php else: ?>
      <?php if ($paginaAtual != 'buy_page.php'): ?>
      <div class="link-header-home">
        <div class="cart">
          <i class='bx bx-cart' onclick="window.location.href='buy_page.php'"></i>
        <?php if ($totalProdutos): ?>
          <span id="cart-count" class="cart-count"> <?= $totalProdutos ?></span>
          <?php endif ?>
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
    <?php endif ?>
  </div>
  <script>
    const userLogado = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    if (userLogado) {
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
    }

    const searchInput = document.getElementById('search-id-input');
    if (searchInput) {
    searchInput.addEventListener('input', function () {
  const itens = this.value.trim();
  const iconSearch = document.getElementById('resultadosBusca');

  if (itens === "") {
    iconSearch.style.display = "none";
    return;
  }
  iconSearch.style.display = "flex";
  iconSearch.innerHTML = '';

  fetch(`../header.php?query=${encodeURIComponent(itens)}`)
    .then(response => response.json())
    .then(data => {
      document.getElementById('resultadosBusca');
      iconSearch.innerHTML = "";

      if (data.length === 0) {
        iconSearch.innerHTML = "<p class='nenhumProduto'>Nenhum produto encontrado</p>";
      } else {
        data.forEach(produto => {
          const resultDiv = document.createElement("div");
          const span = document.createElement("span");

          span.innerText = produto.nome_products;
          span.classList.add("produto-item");

          const idProduto = produto.id_products;

          const paginaAtual = '<?= $paginaAtual ?>';
          if (paginaAtual === "product_page.php") {
            span.addEventListener('click', function () {
              window.location.href = ''; 
            });
          } else {
            span.addEventListener('click', function () {
              document.getElementById('search-id-input').value = produto.nome_products;
              window.location.href = `../viewPage.php?id=${idProduto}`;
            });
          }
          itens.value = produto.nome_products;

          resultDiv.classList.add("produto-div");

          resultDiv.appendChild(span);
          iconSearch.appendChild(resultDiv);
        });
      }
    });
});
    }
  </script>
</body>
</html>