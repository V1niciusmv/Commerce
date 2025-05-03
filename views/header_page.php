<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once '../bd/connection.php';

// Array associativo para controlar os redirecionamentos das paginas
$rotas = [
  'home_page.php' => null,
  'product_page.php' => isset($_GET['show_form']) ? 'product_page.php' : 'home_page.php',//Se estiver na product_page, ele verifica se tem o showForm como paramentro e captura a pag
  'productEdit_page.php' => 'product_page.php',
  'shoop_page.php' => 'home_page.php',
  'shoopEdit_page.php' => 'shoop_page.php',
  'user_page.php' => 'home_page.php',
];
// Pegamos o nome da pagina atual que esta na URL
$paginaAtual = basename($_SERVER['PHP_SELF']);

// Verifica e associa 
$destino = isset($rotas[$paginaAtual]) ? $rotas[$paginaAtual] : 'home_page.php';

$sql = "SELECT id_cart FROM cart WHERE user_id_cart = :cartId";
$stmt = $connection->prepare($sql);
$stmt->bindParam(':cartId', $_SESSION['user_id']);
$stmt->execute();
$verificar = $stmt->fetch(PDO::FETCH_ASSOC);
if ($verificar) {
  $cartId = $verificar['id_cart'];
}

// Calcula a soma total dos produtos do usuario que estão dentro do carrinho de compras 
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
      // Ele pega a pagina atual, e verifica no if com o in_rray que é usado para verificar se existe um valor especifico dentro do array temporario
      // passamos $pages para o in_array verificar se existe esse valor dentro do array, se existir ele exibe a logo
      $pages = basename($_SERVER['PHP_SELF']);
      if (in_array($pages, ['home_page.php', 'welcome_page.php', 'register_page.php'])) {
        echo '<img class="logoWebp" src="/img/logo.webp" alt="Logo">';
      } else { // Caso não tenha ele exibe o icon RowBack passando o $destino como href
        echo "<i class='bx bx-chevron-left' onclick='window.location.href=\"$destino\"'></i>";
      }
      ?>
    </div>

    <!-- Pegamos a pagina atual, verificamos se é igual a welcome, se for resetamos a session para não deixar dados constantes salvos nos inputs
    <?php if (basename($_SERVER['PHP_SELF']) === 'welcome_page.php'){
     if (isset($_SESSION['register_data'])) {
      unset($_SESSION['register_data']);
    } if (isset($_SESSION['login_data'])) {
       unset($_SESSION['login_data']);
     }
  }
  ?>
    // Se a pagina atual for igual a buy_page, exibe o h1 -->
    <?php if ($paginaAtual === 'buy_page.php') 
        echo "<h1> Carrinho de compras </h1>";
      ?>

      <?php 
       $paginaSemSearch = ['welcome_page.php', 'register_page.php', 'buy_page.php'];
      // Se não existir a pagina atual não conter no array paginaSemSearch com paginas expecificas, ele exibe a search 
      if(!in_array(basename($_SERVER['PHP_SELF']), $paginaSemSearch)) {
        echo '<div class="search">
      <input type="search" id="search-id-input">
      <i class="bx bx-search"></i>
      <div id="resultadosBusca" class="search-results">
      </div>
    </div>';
      }
      ?>
    <?php if (!isset($_SESSION['user_id'])): ?> <!-- Se não existir o id do user ele exibe login e register-->
      <div class="links-header">
        <div class="font-header" onclick="window.location.href='/views/register_page.php?action=register'"> Cadastre-se
        </div>
        <div class="font-header" onclick="window.location.href='/views/register_page.php?action=login'"> Login </div>
      </div>
    <?php else: ?> <!-- caso exista ele exibe as opções -->
      <?php if ($paginaAtual != 'buy_page.php'): ?>
      <div class="link-header-home">
        <div class="cart">
          <i class='bx bx-cart' onclick="window.location.href='buy_page.php'"></i>
        <?php if ($totalProdutos): ?> <!-- Se existir alguma quantidade o totalprodutos vai exibir -->
          <span id="cart-count" class="cart-count"> <?= $totalProdutos ?></span>
          <?php endif ?>
        </div>
        <div class="notification">
          <i class='bx bx-bell'></i>
        </div>
        <div class="profile" onclick="dropDown()"> <!-- Chama uma função para descer o Dropdow de options -->
          <i class='bx bx-user'></i>
          <div class="dropdown" id="id-dropdown" onclick="stopPropagation()"> <!-- se o dropdow estiver aberto ele chama a function para feichar -->
            <ul>
              <li>  <a href="user_page.php">  Meu perfil </li>
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
      function dropDown() { // Js para abrir e feichar o dropdow
        const modal = document.getElementById('id-dropdown');
        document.getElementById('id-dropdown').addEventListener('click', function (event) {
          event.stopPropagation();
        });
  
        if (modal.classList.contains('show')) {
          modal.classList.remove('show');
        } else {
          modal.classList.add('show');
        }
      }
    }

    const searchInput = document.getElementById('search-id-input');
    if (searchInput) {
    searchInput.addEventListener('input', function () {
  const itens = this.value.trim(); // Ele pega o valor do input atual e elimina espaços em brancos que sobram entre as palavras
  const iconSearch = document.getElementById('resultadosBusca'); //Div exibição de resultados

  if (itens === "") { // Se não tiver nada digitado ele não exibe a div de resultados
    iconSearch.style.display = "none";
    return;
  }
  iconSearch.style.display = "flex"; // se tiver ele, exibe a div de resultados
  iconSearch.innerHTML = ''; // Limpa as resultados anteriores para não ficarem na div

  // Faz uma requisição em fetch object para a header.php passando como paramentro o itens, que são os valores atuais digitados
  fetch(`../header.php?query=${encodeURIComponent(itens)}`)
    .then(response => response.json()) // Tras a reposta do back e transforma em Json para o Js entender
    .then(data => { // Passa essas informações para o data
      document.getElementById('resultadosBusca'); // e exibe na div de resultados
      iconSearch.innerHTML = ""; // Limpa a barra de pesquisa caso tenha resultados anteriores

      // Se o resultado vier em branco, é porque não existe esses produtos
      if (data.length === 0) {
        iconSearch.innerHTML = "<p class='nenhumProduto'>Nenhum produto encontrado</p>";
      } else { // Se existe ele exibe o produto ou Os produtos
        data.forEach(produto => {
          const resultDiv = document.createElement("div"); // Cria uma div expecifica para cada produto para eles poderem aparecer nos resultados
          const span = document.createElement("span"); // cria um span que sera o nome dos produtos

          span.innerText = produto.nome_products; // Define o span como o nome do produto, passando a variavel produto que esta cem loop no foreach
          span.classList.add("produto-item"); // Adiciona um class

          const idProduto = produto.id_products; // Pega o id do produto que esta sendo digitado variavel vindo do foreach

          const paginaAtual = '<?= $paginaAtual ?>'; //pega a paginaAtual
          if (paginaAtual === "product_page.php") { // Verifica se é product
            span.addEventListener('click', function () { 
              window.location.href = `viewPage.php?id=${idProduto}&origin=${paginaAtual}`; // e redireciona para viewPage passando o id do produto e
              // o paramentro com a paginaAtual
            });
          } else { // Se não for product, ele envia apenas com o id do produto
            span.addEventListener('click', function () {
              window.location.href = `viewPage.php?id=${idProduto}`;
            });
          }

          // Cria uma classe para a div de resultados
          resultDiv.classList.add("produto-div");
          
          resultDiv.appendChild(span); // Adiciona o nome dos produtos dentro da div 
          iconSearch.appendChild(resultDiv); // Adiciona a div dentro do conteiner de resultados de div
        });
      }
    });
});
    }
  </script>
</body>
</html>