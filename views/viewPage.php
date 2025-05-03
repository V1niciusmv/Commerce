<?php 
session_start();

if(isset($_GET['id'])) { // Pega o id do produto clicado enviado do redirecionamento
    $_SESSION['idProduto'] = $_GET['id']; // Pega o id do produto clicado enviado do redirecionamento e salva em uma session

    $origin = isset($_GET['origin']) ? $_GET['origin'] : 'home_page.php'; // Verifica se existe esse Get pra salvar ele com o nome da pagina, 
    // se não salva o home_page

    $paginasPermitidas = ['home_page.php', 'product_page.php']; // Define as paginas que podem ser acessadas, como modo de segurança

    $paginaDestino = in_array($origin, $paginasPermitidas) ? $origin : 'home_page.php';  // E usa o in_array para verificar se o valor de oring tem em
    // paginasPermitidas, se tiver ele captura e se n tiver ele pega a home 
 header("Location: $paginaDestino"); // e redireciona
 exit();
}
?>