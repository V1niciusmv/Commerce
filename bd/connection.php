<?php 
define('HOST', '127.0.0.1');
define ('USUARIO', 'root');
define ('SENHA', 'mk16');
define ('DB', 'commerce');

$email ='DragonCommerce@gmail.com';
$telefone ='(81) 7901-4191';
$whatsapp ='(81) 7901-4191';
$nome_loja='Dragon Commerce';
$texto_destaque = 'Todos os dragões em promoção!';
$endereco_loja ='Campus Aurora';

try {
    $connection = new PDO("mysql:host=" . HOST . ";dbname=" . DB, USUARIO, SENHA);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}

?>
